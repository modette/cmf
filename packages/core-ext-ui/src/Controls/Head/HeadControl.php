<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Head;

use Modette\UI\Controls\Base\BaseControl;
use Modette\UI\Controls\Base\BaseControlTemplate;
use Modette\UI\Controls\Icons\IconsControl;
use Modette\UI\Controls\Icons\IconsFactory;
use Modette\UI\Controls\Links\LinksControl;
use Modette\UI\Controls\Links\LinksFactory;
use Modette\UI\Controls\Meta\MetaControl;
use Modette\UI\Controls\Meta\MetaFactory;
use Modette\UI\Controls\NoScript\NoScriptControl;
use Modette\UI\Controls\NoScript\NoScriptFactory;
use Modette\UI\Controls\Styles\StylesControl;
use Modette\UI\Controls\Styles\StylesFactory;
use Modette\UI\Controls\Title\TitleControl;
use Modette\UI\Controls\Title\TitleFactory;

/**
 * @property-read BaseControlTemplate $template
 */
class HeadControl extends BaseControl
{

	/** @var IconsFactory */
	private $iconsFactory;

	/** @var LinksFactory */
	private $linksFactory;

	/** @var MetaFactory */
	private $metaFactory;

	/** @var NoScriptFactory */
	private $noScriptFactory;

	/** @var TitleFactory */
	private $titleFactory;

	/** @var StylesFactory */
	private $stylesFactory;

	public function __construct(
		IconsFactory $iconsFactory,
		LinksFactory $linksFactory,
		MetaFactory $metaFactory,
		NoScriptFactory $noScriptFactory,
		TitleFactory $titleFactory,
		StylesFactory $stylesFactory
	)
	{
		parent::__construct();
		$this->iconsFactory = $iconsFactory;
		$this->linksFactory = $linksFactory;
		$this->metaFactory = $metaFactory;
		$this->noScriptFactory = $noScriptFactory;
		$this->titleFactory = $titleFactory;
		$this->stylesFactory = $stylesFactory;
	}

	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

	protected function createComponentIcons(): IconsControl
	{
		return $this->iconsFactory->create();
	}

	protected function createComponentLinks(): LinksControl
	{
		return $this->linksFactory->create();
	}

	protected function createComponentMeta(): MetaControl
	{
		return $this->metaFactory->create();
	}

	protected function createComponentNoScript(): NoScriptControl
	{
		return $this->noScriptFactory->create();
	}

	protected function createComponentTitle(): TitleControl
	{
		return $this->titleFactory->create();
	}

	protected function createComponentStyles(): StylesControl
	{
		return $this->stylesFactory->create();
	}

}
