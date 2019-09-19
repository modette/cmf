<?php declare(strict_types = 1);

namespace Modette\Templates;

use Modette\Templates\Themes\ThemedTemplate;
use stdClass;

/**
 * @property-read string     $baseUrl
 * @property-read stdClass[] $flashes
 */
abstract class Template extends ThemedTemplate
{

}
