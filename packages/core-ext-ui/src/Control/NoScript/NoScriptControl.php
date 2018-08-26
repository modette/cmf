<?php declare(strict_types = 1);

namespace Modette\UI\Control\NoScript;

use Modette\UI\Base\Control\BaseControl;

/**
 * @property-read NoScriptTemplate $template
 */
class NoScriptControl extends BaseControl
{

	/** @var string[] */
	private $noScripts = [];

	/**
	 * Add noscript <noscript>{$content|noescape}</noscript>
	 */
	public function addNoScript(string $content): self
	{
		$this->noScripts[] = $content;
		return $this;
	}

	public function render(): void
	{
		$this->template->noScripts = $this->noScripts;

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
