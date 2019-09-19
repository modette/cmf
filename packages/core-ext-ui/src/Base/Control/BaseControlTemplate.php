<?php declare(strict_types = 1);

namespace Modette\UI\Base\Control;

use Modette\Templates\Themes\ThemedTemplate;
use Modette\UI\Base\Presenter\BasePresenter;

/**
 * @property-read BaseControl   $control
 * @property-read BasePresenter $presenter
 */
abstract class BaseControlTemplate extends ThemedTemplate
{

}
