<?php declare(strict_types = 1);

namespace Modette\Front\Error\Presenter;

use Modette\Front\Base\Presenter\BaseFrontPresenter;
use Nette\Application\BadRequestException;
use Nette\Http\IResponse;
use Throwable;

class ErrorPresenter extends BaseFrontPresenter
{

	protected const SUPPORTED_VIEWS = [400, 403, 404, 410, 500];

	public function actionDefault(): void
	{
		// Note error in ajax request
		if ($this->isAjax()) {
			$this->sendPayload();
		}
	}

	public function renderDefault(?Throwable $error = null): void
	{
		if ($error === null) {
			// Direct access, act as user error
			$code = IResponse::S404_NOT_FOUND;
			$view = 404;
		} elseif ($error instanceof BadRequestException) {
			// Use view requested by BadRequestException or generic 404/500
			$code = $error->getCode();
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
