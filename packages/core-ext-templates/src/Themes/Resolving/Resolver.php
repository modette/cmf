<?php declare(strict_types = 1);

namespace Modette\Templates\Themes\Resolving;

use Modette\Templates\Themes\Exception\IncompatibleResolverException;
use Modette\Templates\Themes\Exception\NoTemplateFoundException;

interface Resolver
{

	/**
	 * @param string[] $parameters
	 * @throws IncompatibleResolverException Resolver cannot be used for given object
	 * @throws NoTemplateFoundException No template found for given object
	 */
	public function getTemplatePath(object $templateAbleObject, string $view, array $parameters = []): string;

}
