<?php declare(strict_types = 1);

namespace Modette\UI\Control\Base;

use Modette\UI\Base\Presenter\BasePresenter;
use Modette\UI\FakeTranslator;
use Modette\UI\Themes\ThemeAbleControl;
use Modette\UI\Utils\FlashMessages;
use Modette\UI\Utils\TranslateShortcut;
use Nette\Application\UI\Control as NetteControl;
use Psr\Log\LoggerInterface;

/**
 * @method BasePresenter getPresenter()
 * @method BaseControlTemplate getTemplate()
 * @property-read BasePresenter       $presenter
 * @property-read BaseControlTemplate $template
 */
abstract class BaseControl extends NetteControl
{

	use FlashMessages;
	use ThemeAbleControl;
	use TranslateShortcut;

	public function getLogger(): LoggerInterface
	{
		return $this->getPresenter()->getLogger();
	}

	public function getTranslator(): FakeTranslator
	{
		return $this->getPresenter()->getTranslator();
	}

}
