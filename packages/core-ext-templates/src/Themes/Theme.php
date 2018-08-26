<?php declare(strict_types = 1);

namespace Modette\Themes;

class Theme
{

	/** @var mixed[] */
	private $checkers = [
		//ControlTypeChecker::TYPE => ControlTypeChecker::class
		//jaký checker bude pro layout presenteru?
	];

	public function getTemplate(string $component, string $view, string $componentType): string
	{
		//většinou renderuju jednu konkrétní šablonu pro kontrolku/presenter/email
	}

	/**
	 * @return string[]
	 */
	public function getTemplates(string $component, string $view, string $componentType): array
	{
		//pro datagrid mám více šablon
		return [];
	}

}
