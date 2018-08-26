<?php declare(strict_types = 1);

namespace Modette\UI\Presenters\InternalError;

use Modette\UI\Presenters\Base\BasePresenter;
use Nette\Application\Request;
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

	public function actionDefault(Throwable $exception, ?Request $request = null): void
	{
		$code = $exception->getCode();
		$level = ($code >= 400 && $code <= 499) ? LogLevel::WARNING : LogLevel::ERROR;

		$context = [
			'presenter' => $request !== null ? $request->getPresenterName() : 'unknown',
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
		];

		$this->getLogger()->log($level, sprintf(
			'Code %s: %s',
			$code,
			$exception->getMessage()
		), $context);

		if ($request !== null) {
			foreach ($this->errorPresenters as [$presenter, $regex]) {
				if (Strings::match($request->getPresenterName(), $regex) !== null) {
					$this->forward($presenter, ['exception' => $exception]);
				}
			}

			if ($this->defaultErrorPresenter !== null) {
				$this->forward($this->defaultErrorPresenter, ['exception' => $exception]);
			}
		}

		// Note error in ajax request
		if ($this->isAjax()) {
			$this->sendPayload();
		}
	}

	public function renderDefault(Throwable $exception): void
	{
		$code = $exception->getCode();

		if ($code >= 500) {
			$this['document-head-meta']->setRobots(['noindex']);
		} else {
			$this['document-head-meta']->setRobots(['noindex', 'nofollow']);
		}

		$view = in_array($code, static::SUPPORTED_VIEWS, true)
			? $code
			: ($code >= 500 ? 500 : 400);

		$this['document-head-title']->setMain(
			$this->getTranslator()->translate(sprintf(
				'modette.ui.presenter.error.%s.title',
				$view
			))
		);

		$this->setView($view);
	}

	public function sendPayload(): void
	{
		$this->payload->error = true;
		parent::sendPayload();
	}

}
