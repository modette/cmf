<?php declare(strict_types = 1);

namespace Modette\Admin\Error\Presenter;

use Modette\Admin\Base\Presenter\BaseAdminPresenter;
use Nette\Http\IResponse;
use Throwable;

class ErrorPresenter extends BaseAdminPresenter
{

	protected const SUPPORTED_VIEWS = [400, 403, 404, 410, 500];

	/** @var bool */
	private $debugMode;

	public function __construct(bool $debugMode)
	{
		parent::__construct();
		$this->debugMode = $debugMode;
	}

	public function actionDefault(): void
	{
		// Note error in ajax request
		if ($this->isAjax()) {
			$this->sendPayload();
		}
	}

	public function renderDefault(?Throwable $exception = null): void
	{
		if ($exception !== null) {
			// Exception was thrown and InternalError forwarded here
			$code = $exception->getCode();
			$view = in_array($code, static::SUPPORTED_VIEWS, true)
				? $code
				: ($code >= 500 ? 500 : 400);
		} elseif (
			$this->debugMode &&
			isset($this->request->parameters['view']) &&
			in_array($this->request->parameters['view'], static::SUPPORTED_VIEWS, true)
		) {
			// Developer requested specific view - useful for testing
			$view = $this->request->parameters['view'];
			$this->getHttpResponse()->setCode($view);
		} else {
			// Page was accessed directly by user -> simulate error
			$view = 404;
			$this->getHttpResponse()->setCode(IResponse::S404_NOT_FOUND);
		}

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
