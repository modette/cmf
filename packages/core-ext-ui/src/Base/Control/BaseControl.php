<?php declare(strict_types = 1);

namespace Modette\UI\Base\Control;

use Modette\Templates\Themes\Bridges\NetteApplication\ThemeAbleControl;
use Modette\UI\Base\Presenter\BasePresenter;
use Modette\UI\FakeTranslator;
use Modette\UI\Utils\FlashMessages;
use Modette\UI\Utils\TranslateShortcut;
use Nette\Application\UI\Control as NetteControl;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method BasePresenter getPresenter()
 * @method BaseControlTemplate getTemplate()
 * @property BasePresenter            $presenter
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

	public function getEventDispatcher(): EventDispatcherInterface
	{
		return $this->getPresenter()->getEventDispatcher();
	}

	public function getTranslator(): FakeTranslator
	{
		return $this->getPresenter()->getTranslator();
	}

}
