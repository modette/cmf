<?php declare(strict_types = 1);

namespace Modette\Templates\Themes;

use Modette\Exceptions\Logic\InvalidStateException;
use Modette\Templates\Themes\Exception\IncompatibleResolverException;
use Modette\Templates\Themes\Exception\NoTemplateFoundException;
use Modette\Templates\Themes\Resolving\Resolver;

final class Theme
{

	/** @var Resolver[] */
	private $resolvers;

	/**
	 * @param Resolver[] $resolvers
	 */
	public function __construct(array $resolvers)
	{
		$this->resolvers = $resolvers;
	}

	/**
	 * @param string[] $parameters
	 * @throws NoTemplateFoundException
	 */
	public function getTemplatePath(object $templateAbleObject, string $view, array $parameters = []): string
	{
		foreach ($this->resolvers as $resolver) {
			try {
				return $resolver->getTemplatePath($templateAbleObject, $view, $parameters);
			} catch (IncompatibleResolverException $exception) {
				continue;
			}
		}

		throw new InvalidStateException(sprintf('No theme resolver found for object of type \'%s\'', get_class($templateAbleObject)));
	}

}
