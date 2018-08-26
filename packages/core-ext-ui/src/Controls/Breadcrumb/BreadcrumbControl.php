<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Breadcrumb;

use Modette\UI\Controls\Base\BaseControl;

/**
 * @property-read BreadcrumbTemplate $template
 */
class BreadcrumbControl extends BaseControl
{

	/** @var mixed[] */
	private $links = [];

	/** @var string[] */
	private $iconsMapping = [];

	/**
	 * @param string[] $mapping
	 */
	public function addIconsMapping(array $mapping): self
	{
		$this->iconsMapping += $mapping;
		return $this;
	}

	public function addLink(string $title, ?string $uri = null, ?string $icon = null): self
	{
		$this->links[] = [
			'title' => $title,
			'uri' => $uri,
			'icon' => $this->iconsMapping[$icon] ?? $icon,
		];
		return $this;
	}

	public function render(): void
	{
		$this->template->links = $this->links;

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
