<?php declare(strict_types = 1);

namespace Modette\Templates;

use Nette\Bridges\ApplicationLatte\Template as NetteTemplate;
use stdClass;

/**
 * @property-read string     $baseUrl
 * @property-read stdClass[] $flashes
 */
abstract class Template extends NetteTemplate
{

}
