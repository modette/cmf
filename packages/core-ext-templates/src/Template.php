<?php declare(strict_types = 1);

namespace Modette\Templates;

use Nette\Bridges\ApplicationLatte\Template as NetteTemplate;
use stdClass;

/**
 * @property-read string     $baseUri
 * @property-read string     $basePath
 * @property-read stdClass[] $flashes
 */
abstract class Template extends NetteTemplate
{

}
