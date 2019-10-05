<?php declare(strict_types = 1);

namespace Modette\UI\InternalError\Presenter;

use Modette\UI\Base\Presenter\BasePresenter;
use Nette\Application\BadRequestException;
use Nette\Application\Request;
use Nette\Http\IResponse;
use Nette\Utils\Strings;
use Psr\Log\LogLevel;
use Throwable;

class InternalErrorPresenter extends BasePresenter
{

	protected const SUPPORTED_VIEWS = [400, 403, 404, 410, 500];

	/** @var mixed[] */
	private $errorPresenters = [];

	/** @var string|null */
	private $defaultErrorPresenter;

	public function setDefaultErrorPresenter(string $presenter): void
	{
		$this->defaultErrorPresenter = $presenter;
	}

	public function addErrorPresenter(string $presenter, string $regex): void
	{
		$this->errorPresenters[] = [$presenter, $regex];
	}

	public function actionDefault(Throwable $exception, ?Request $request): void
	{
		// Log error
		$this->getLogger()->log(
			$exception instanceof BadRequestException ? LogLevel::WARNING : LogLevel::ERROR,
			$exception->getMessage(),
			[
				'presenter' => $request !== null ? $request->getPresenterName() : 'unknown',
				'exception' => $exception,
			]
		);

		// Forward to error presenter if matches pattern
		if ($request !== null) {
			foreach ($this->errorPresenters as [$presenter, $regex]) {
				if (Strings::match($request->getPresenterName(), $regex) !== null) {
					$this->forward($presenter, ['error' => $exception, 'request' => $request]);
				}
			}
		}

		// Forward to default error presenter
		if ($this->defaultErrorPresenter !== null) {
			$this->forward($this->defaultErrorPresenter, ['exception' => $exception, 'request' => $request]);
		}

		// Note error in ajax request
		if ($this->isAjax()) {
			$this->sendPayload();
		}
	}

	public function renderDefault(Throwable $exception): void
	{
		if ($exception instanceof BadRequestException) {
			// Use view requested by BadRequestException or generic 404/500
			$code = $exception->getCode();
			if (in_array($code, self::SUPPORTED_VIEWS, true)) {
				$view = $code;
			} else {
				$view = $code >= 400 && $code <= 499 ? 404 : 500;
			}
		} else {
			// Use generic view for real error
			$code = IResponse::S500_INTERNAL_SERVER_ERROR;
			$view = 500;
		}

		// Set page title
		$this['document-head-title']->setMain(
			$this->getTranslator()->translate(sprintf(
				'modette.ui.presenter.error.%s.title',
				$view
			))
		);

		$this->getHttpResponse()->setCode($code);
		$this->setView((string) $view);
		$this['document-head-meta']->setRobots(['noindex']);
	}

	public function sendPayload(): void
	{
		$this->payload->error = true;
		parent::sendPayload();
	}

}
