<?php declare(strict_types = 1);

namespace Modette\Templates;

use Nette\Bridges\ApplicationLatte\Template as NetteTemplate;
use Nette\Security\User;

/**
 * @property-read string   $baseUri
 * @property-read string   $basePath
 * @property-read string[] $flashes
 * @property-read User     $user
 */
abstract class Template extends NetteTemplate
{

}
