<?php declare(strict_types = 1);

namespace Modette\UI\Base\Presenter;

use Modette\Core\Logging\LoggerAccessor;
use Modette\UI\Control\Document\DocumentControl;
use Modette\UI\Control\Document\DocumentFactory;
use Modette\UI\FakeTranslator;
use Modette\UI\InternalError\Presenter\InternalErrorPresenter;
use Modette\UI\Themes\ThemeAblePresenter;
use Modette\UI\Utils\FlashesMapper;
use Modette\UI\Utils\FlashMessages;
use Modette\UI\Utils\TranslateShortcut;
use Nette\Application\UI\Presenter as NettePresenter;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Psr\Log\LoggerInterface;

/**
 * @method self getPresenter()
 * @method TemplateFactory getTemplateFactory()
 * @method BasePresenterTemplate getTemplate()
 * @property-read BasePresenter         $presenter
 * @property-read BasePresenterTemplate $template
 * @property-read null                  $user
 */
abstract class BasePresenter extends NettePresenter
{

	use FlashMessages;
	use ThemeAblePresenter;
	use TranslateShortcut;

	/** @var bool */
	protected $developmentServer;

	/** @var DocumentFactory */
	private $documentFactory;

	/** @var LoggerAccessor */
	private $loggerAccessor;

	/** @var FakeTranslator */
	private $translator;

	/** @var FlashesMapper */
	private $flashesMapper;

	public function injectSecondary(
		DocumentFactory $documentFactory,
		LoggerAccessor $loggerAccessor,
		FakeTranslator $translator,
		FlashesMapper $flashesMapper,
		bool $developmentServer
	): void
	{
		$this->documentFactory = $documentFactory;
		$this->loggerAccessor = $loggerAccessor;
		$this->translator = $translator;
		$this->flashesMapper = $flashesMapper;
		$this->developmentServer = $developmentServer;
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

	public function getTranslator(): FakeTranslator
	{
		return $this->translator;
	}

	public function getFlashesMapper(): FlashesMapper
	{
		return $this->flashesMapper;
	}

}
