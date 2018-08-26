<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Head;

use Modette\UI\Controls\Control;
use Modette\UI\Controls\Icons\Icons;
use Modette\UI\Controls\Icons\IIconsFactory;
use Modette\UI\Controls\Links\ILinksFactory;
use Modette\UI\Controls\Links\Links;
use Modette\UI\Controls\Meta\IMetaFactory;
use Modette\UI\Controls\Meta\Meta;
use Modette\UI\Controls\Title\ITitleFactory;
use Modette\UI\Controls\Title\Title;

class Head extends Control
{

	/** @var IMetaFactory */
	private $metaFactory;

	/** @var ITitleFactory */
	private $titleFactory;

	/** @var IIconsFactory */
	private $iconsFactory;

	/** @var ILinksFactory */
	private $linksFactory;

	public function __construct(
		IMetaFactory $metaFactory,
		ITitleFactory $titleFactory,
		IIconsFactory $iconsFactory,
		ILinksFactory $linksFactory
	)
	{
		parent::__construct();
		$this->metaFactory = $metaFactory;
		$this->titleFactory = $titleFactory;
		$this->iconsFactory = $iconsFactory;
		$this->linksFactory = $linksFactory;
	}

	public function render(): void
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

	protected function createComponentMeta(): Meta
	{
		return $this->metaFactory->create();
	}

	protected function createComponentTitle(): Title
	{
		return $this->titleFactory->create();
	}

	protected function createComponentIcons(): Icons
	{
		return $this->iconsFactory->create();
	}

	protected function createComponentLinks(): Links
	{
		return $this->linksFactory->create();
	}

}
