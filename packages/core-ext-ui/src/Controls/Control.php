<?php declare(strict_types = 1);

namespace Modette\UI\Controls;

use Modette\Themes\Bridges\NetteApplication\ThemeAbleControl;
use Modette\UI\Presenters\Presenter;
use Nette\Application\UI\Control as NetteControl;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * @property Presenter              $presenter
 * @property-read Template $template
 */
abstract class Control extends NetteControl
{

	use ThemeAbleControl;

}
