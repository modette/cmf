<?php declare(strict_types = 1);

namespace Modette\UI\Base\Presenter;

use Contributte\Application\UI\Presenter\StructuredTemplates;
use Modette\Core\DI\Parameters;
use Modette\Core\Logging\LoggerAccessor;
use Modette\Templates\Themes\Bridges\NetteApplication\ThemeAblePresenter;
use Modette\UI\Control\Document\DocumentControl;
use Modette\UI\Control\Document\DocumentFactory;
use Modette\UI\FakeTranslator;
use Modette\UI\InternalError\Presenter\InternalErrorPresenter;
use Modette\UI\Utils\FlashMessages;
use Modette\UI\Utils\TranslateShortcut;
use Nette\Application\UI\Presenter as NettePresenter;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method self getPresenter()
 * @method TemplateFactory getTemplateFactory()
 * @method BasePresenterTemplate getTemplate()
 * @property-read BasePresenterTemplate $template
 */
abstract class BasePresenter extends NettePresenter
{

	use FlashMessages;
	use StructuredTemplates;
	use ThemeAblePresenter;
	use TranslateShortcut;

	/** @var DocumentFactory */
	private $documentFactory;

	/** @var EventDispatcherInterface */
	private $eventDispatcher;

	/** @var LoggerAccessor */
	private $loggerAccessor;

	/** @var Parameters */
	private $parameters;

	/** @var FakeTranslator */
	private $translator;

	public function injectSecondary(
		DocumentFactory $documentFactory,
		EventDispatcherInterface $eventDispatcher,
		LoggerAccessor $loggerAccessor,
		Parameters $parameters,
		FakeTranslator $translator
	): void
	{
		// Because injectPrimary is taken
		$this->documentFactory = $documentFactory;
		$this->eventDispatcher = $eventDispatcher;
		$this->loggerAccessor = $loggerAccessor;
		$this->parameters = $parameters;
		$this->translator = $translator;
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this['document']->addAttribute('class', 'no-js');
		$this['document']->setAttribute('lang', $this->translator->getLanguage());

		if (!($this instanceof InternalErrorPresenter)) {
			$link = $this->link('//this', ['backlink' => null]);
			$this['document-head-links']->addLink(
				$link,
				'canonical'
			);
			$this['document-head-meta']->addOpenGraph(
				'url',
				$link
			);
		}

		//TODO - real translator
		$this->template->setTranslator($this->translator);
	}

	protected function createComponentDocument(): DocumentControl
	{
		return $this->documentFactory->create();
	}

	public function getLogger(): LoggerInterface
	{
		return $this->loggerAccessor->get();
	}

	public function getEventDispatcher(): EventDispatcherInterface
	{
		return $this->eventDispatcher;
	}

	public function getContainerParameters(): Parameters
	{
		return $this->parameters;
	}

	public function getTranslator(): FakeTranslator
	{
		return $this->translator;
	}

}
