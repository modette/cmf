<?php declare(strict_types = 1);

namespace Modette\UI\Presenters;

use Contributte\Application\UI\Presenter\StructuredTemplates;
use Modette\Themes\Bridges\NetteApplication\ThemeAblePresenter;
use Modette\UI\Controls\Document\Document;
use Modette\UI\Controls\Document\IDocumentFactory;
use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\UI\ITemplateFactory;
use Nette\Application\UI\Presenter as NettePresenter;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Session;
use Nette\Security\User;

/**
 * @property-read null $context
 */
abstract class Presenter extends NettePresenter
{

	use ThemeAblePresenter;
	use StructuredTemplates;

	/** @var IDocumentFactory */
	protected $documentFactory;

	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this['document']->addAttribute('class', 'no-js');
		//todo - language
		//$this['document']->setAttribute('lang', 'cs-cz');
	}

	public function injectDocument(IDocumentFactory $documentFactory): void
	{
		$this->documentFactory = $documentFactory;
	}

	protected function createComponentDocument(): Document
	{
		return $this->documentFactory->create();
	}

	public function injectPrimary(
		?Container $context = null,
		?IPresenterFactory $presenterFactory = null,
		?IRouter $router = null,
		IRequest $httpRequest,
		IResponse $httpResponse,
		?Session $session = null,
		?User $user = null,
		?ITemplateFactory $templateFactory = null
	): void
	{
		$context = null; // context is disallowed
		parent::injectPrimary($context, $presenterFactory, $router, $httpRequest, $httpResponse, $session, $user, $templateFactory);
	}

}
