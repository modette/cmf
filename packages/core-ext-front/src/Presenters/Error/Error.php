<?php declare(strict_types = 1);

namespace Modette\Front\Presenters\Error;

use Modette\Front\Presenters\Front\Front;
use Nette\Http\IResponse;
use Throwable;
use Tracy\Debugger;

class Error extends Front
{

	public function actionDefault(): void
	{
		// Note error in ajax request
		if ($this->isAjax()) {
			$this->sendPayload();
		}
	}

	public function renderDefault(?Throwable $exception = null): void
	{
		$this['document-head-meta']->setRobots(['noindex', 'nofollow']);

		if ($exception !== null) {
			$code = $exception->getCode();
			$view = in_array($code, [403, 404, 405, 410, 500], true) ? $code : '4xx';
		} else {
			// No exception -> page was accessed directly through url -> simulate error
			if (!Debugger::$productionMode &&
				isset($this->request->parameters['view']) &&
				in_array($this->request->parameters['view'], [403, 404, 405, 410, '4xx'], true)
			) {
				// Developer requested specific view - useful for testing
				$view = $this->request->parameters['view'];
			} else {
				$view = 404;
			}
			$this->getHttpResponse()->setCode(IResponse::S404_NOT_FOUND);
		}

		//todo - translate
		//$this->translator->translate("$view.title", null, [], 'modette.front.presenters.error')
		$this['document-head-title']->setMain(sprintf('Error %s', $view));

		$this->setView($view);
	}

	public function sendPayload(): void
	{
		$this->payload->error = true;
		parent::sendPayload();
	}

}
