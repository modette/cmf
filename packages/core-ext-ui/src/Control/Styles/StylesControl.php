<?php declare(strict_types = 1);

namespace Modette\UI\Control\Styles;

use Modette\UI\Base\Control\BaseControl;

/**
 * @property-read StylesTemplate $template
 */
class StylesControl extends BaseControl
{

	/** @var string[] */
	private $styles = [];

	public function addStyle(string $href): self
	{
		$this->styles[] = $href;
		return $this;
	}

	public function render(): void
	{
		$this->template->styles = $this->styles;

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
