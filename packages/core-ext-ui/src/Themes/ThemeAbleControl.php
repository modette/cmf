<?php declare(strict_types = 1);

namespace Modette\UI\Themes;

use Modette\Templates\Themes\ThemedTemplate;
use Nette\Application\UI\ITemplate;

/**
 * @method ThemedTemplate getTemplate()
 * @property-read ThemedTemplate $template
 */
trait ThemeAbleControl
{

	/**
	 * @return ThemedTemplate
	 */
	final protected function createTemplate(): ITemplate
	{
		$template = parent::createTemplate();
		assert($template instanceof ThemedTemplate);
		$template->setTemplateAbleObject($this);
		return $template;
	}

}
