<?php declare(strict_types = 1);

namespace Modette\UI\Control\Icons;

use Modette\UI\Base\Control\BaseControl;

/**
 * @property-read IconsTemplate $template
 */
class IconsControl extends BaseControl
{

	/** @var string|null */
	private $favicon;

	/** @var mixed[] */
	private $icons = [];

	public function setFavicon(string $favicon): self
	{
		$this->favicon = $favicon;

		return $this;
	}

	/**
	 * @param string[] $sizes
	 */
	public function addIcon(string $href, array $sizes = [], ?string $type = null, string $rel = 'icon'): self
	{
		$this->icons[] = [
			'href' => $href,
			'rel' => $rel,
			'sizes' => implode(' ', $sizes),
			'type' => $type,
		];

		return $this;
	}

	/**
	 * @param string[] $sizes
	 */
	public function addApple(string $href, array $sizes = []): self
	{
		$this->addIcon($href, $sizes, null, 'apple-touch-icon');

		return $this;
	}

	/**
	 * @param string[] $sizes
	 */
	public function addApplePrecomposed(string $href, array $sizes = []): self
	{
		$this->addIcon($href, $sizes, null, 'apple-touch-icon-precomposed');

		return $this;
	}

	public function render(): void
	{
		// Sort icons by rel
		uasort($this->icons, function ($a, $b) {
			return strcmp($a['rel'], $b['rel']);
		});

		$this->template->favicon = $this->favicon;
		$this->template->icons = $this->icons;

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
