<?php declare(strict_types = 1);

namespace Modette\UI\Themes\Resolving;

use Modette\Exceptions\Logic\InvalidStateException;
use Modette\Templates\Themes\Exception\IncompatibleResolverException;
use Modette\Templates\Themes\Exception\NoTemplateFoundException;
use Modette\Templates\Themes\Resolving\Resolver;
use Modette\Templates\Themes\Utils\Classes;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;

final class ControlResolver implements Resolver
{

	public const PRESENTER_TEMPLATE_TYPE_MAIN = 'main';
	public const PRESENTER_TEMPLATE_TYPE_LAYOUT = 'layout';

	public const PRESENTER_TEMPLATE_TYPES = [
		self::PRESENTER_TEMPLATE_TYPE_MAIN,
		self::PRESENTER_TEMPLATE_TYPE_LAYOUT,
	];

	public const OPTION_PRESENTER_TEMPLATE_TYPE = 'presenterTemplateType';

	/**
	 * @param string[] $parameters
	 * @throws IncompatibleResolverException
	 * @throws NoTemplateFoundException
	 */
	public function getTemplatePath(object $templateAbleObject, string $view, array $parameters = []): string
	{
		if (!$templateAbleObject instanceof Control) {
			throw new IncompatibleResolverException();
		}

		if ($templateAbleObject instanceof Presenter) {
			return $this->resolvePresenterTemplatePath($templateAbleObject, $view, $parameters);
		}

		return $this->resolveControlTemplatePath($templateAbleObject, $view);
	}

	/**
	 * @param string[] $parameters
	 * @throws NoTemplateFoundException
	 */
	private function resolvePresenterTemplatePath(Presenter $presenter, string $view, array $parameters): string
	{
		if (!isset($parameters[self::OPTION_PRESENTER_TEMPLATE_TYPE])) {
			throw new InvalidStateException('Presenter template type not specified');
		}

		$templateType = $parameters[self::OPTION_PRESENTER_TEMPLATE_TYPE];

		if (!in_array($templateType, self::PRESENTER_TEMPLATE_TYPES, true)) {
			throw new InvalidStateException(sprintf('Invalid presenter template type \'%s\'', $templateType));
		}

		$classes = Classes::getClassList($presenter);
		$triedPaths = [];

		foreach ($classes as $class) {
			if ($class === Presenter::class) {
				throw new NoTemplateFoundException($triedPaths, $presenter);
			}

			$dir = Classes::getClassDir($class);

			$templatePath = $templateType === self::PRESENTER_TEMPLATE_TYPE_MAIN
				? $dir . '/templates/' . $view . '.latte'
				: $dir . '/templates/@' . $view . '.latte';

			if (is_file($templatePath)) {
				return $templatePath;
			}

			$triedPaths[] = $templatePath;
		}

		throw new NoTemplateFoundException($triedPaths, $presenter);
	}

	/**
	 * @throws NoTemplateFoundException
	 */
	private function resolveControlTemplatePath(Control $control, string $view): string
	{
		$classes = Classes::getClassList($control);
		$triedPaths = [];

		foreach ($classes as $class) {
			if ($class === Control::class) {
				throw new NoTemplateFoundException($triedPaths, $control);
			}

			$dir = Classes::getClassDir($class);
			$templatePath = $dir . '/templates/' . $view . '.latte';

			if (is_file($templatePath)) {
				return $templatePath;
			}

			$triedPaths[] = $templatePath;
		}

		throw new NoTemplateFoundException($triedPaths, $control);
	}

}
