<?php declare(strict_types = 1);

namespace Modette\UI\Presenters\InternalError;

use Modette\UI\Presenters\Presenter;
use Nette\Application\Request;
use Throwable;

class InternalError extends Presenter
{

	public function actionDefault(Throwable $exception, ?Request $request = null): void
	{
		// Log error
		//TODO - monolog
		/*
		$exceptionContext = [
			'presenter' => $request !== null ? $request->getPresenterName() : 'undefined',
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
		];
		if ($exception instanceof BadRequestException) {
			$this->logger->warning(
				"Code {$exception->getCode()}: {$exception->getMessage()}", $exceptionContext
			);
		} else {
			$this->logger->critical(
				"Code {$exception->getCode()}: {$exception->getMessage()}", $exceptionContext
			);
		}*/

		// TODO - přesměrovat (podle názvu, ne podle třídy)
		/*if ($request !== null) {
			$presenterClass = $this->presenterFactory->getPresenterClass($request->presenterName);
			if (\is_subclass_of($presenterClass, AdminPresenter::class)) {
				$this->forward(':Modex:Admin:Error:', ['exception' => $exception]);
			} elseif (\is_subclass_of($presenterClass, FrontPresenter::class)) {
				$this->forward(':Modex:Front:Error:', ['exception' => $exception]);
			}
		}*/

		// Note error in ajax request
		if ($this->isAjax()) {
			$this->sendPayload();
		}
	}

	public function renderDefault(Throwable $exception): void
	{
		$this['document-head-meta']->setRobots(['noindex', 'nofollow']);

		//TODO - šablony a překlady k nim

		$code = $exception->getCode();
		$view = in_array($code, [403, 404, 405, 410, 500], true) ? $code : '4xx';
		$this->setView($view);
	}

	public function sendPayload(): void
	{
		$this->payload->error = true;
		parent::sendPayload();
	}

}
