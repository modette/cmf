<?php declare(strict_types = 1);

namespace Modette\UI\Control\Head;

use Modette\UI\Base\Control\BaseControl;
use Modette\UI\Base\Control\BaseControlTemplate;
use Modette\UI\Control\Icons\IconsControl;
use Modette\UI\Control\Icons\IconsFactory;
use Modette\UI\Control\Links\LinksControl;
use Modette\UI\Control\Links\LinksFactory;
use Modette\UI\Control\Meta\MetaControl;
use Modette\UI\Control\Meta\MetaFactory;
use Modette\UI\Control\NoScript\NoScriptControl;
use Modette\UI\Control\NoScript\NoScriptFactory;
use Modette\UI\Control\Styles\StylesControl;
use Modette\UI\Control\Styles\StylesFactory;
use Modette\UI\Control\Title\TitleControl;
use Modette\UI\Control\Title\TitleFactory;

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
