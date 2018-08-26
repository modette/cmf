<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Icons;

use Modette\UI\Controls\Control;

class Icons extends Control
{

	/** @var string|null */
	private $favicon;

	/** @var mixed[] */
	private $icons = [];

	/** @var string[]|null[] */
	private $apple = [];

	/** @var string[]|null[] */
	private $applePrecomposed = [];

	public function setFavicon(string $favicon): self
	{
		$this->favicon = $favicon;
		return $this;
	}

	public function addIcon(string $href, ?string $sizes = null, ?string $type = null): self
	{
		$this->icons[$href] = [
			'size' => $sizes,
			'type' => $type,
		];
		return $this;
	}

	public function addApple(string $href, ?string $sizes = null): self
	{
		//todo - sizes do pole?
		$this->apple[$href] = $sizes;
		return $this;
	}

	public function addApplePrecomposed(string $href, ?string $sizes = null): self
	{
		//todo - sizes do pole?
		$this->applePrecomposed[$href] = $sizes;
		return $this;
	}

	public function render(): void
	{
		$this->template->setParameters([
			'favicon' => $this->favicon,
			'icons' => $this->icons,
			'apple' => $this->apple,
			'applePrecomposed' => $this->applePrecomposed,
		]);
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
