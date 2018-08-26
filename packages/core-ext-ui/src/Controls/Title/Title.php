<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Title;

use Modette\UI\Controls\Control;

class Title extends Control
{

	/** @var string|null */
	private $site;

	/** @var string|null */
	private $module;

	/** @var string|null */
	private $main;

	/** @var string|null */
	private $separator = '-';

	/** @var bool */
	private $revert = false;

	public function setSite(?string $site): self
	{
		$this->site = $site;
		return $this;
	}

	public function setModule(?string $module): self
	{
		$this->module = $module;
		return $this;
	}

	public function setMain(?string $main): self
	{
		$this->main = $main;
		return $this;
	}

	public function setSeparator(?string $separator): self
	{
		$this->separator = $separator;
		return $this;
	}

	/**
	 * Display 'Site Module Main' instead of 'Main Module Site'
	 */
	public function revert(bool $revert = true): self
	{
		$this->revert = $revert;
		return $this;
	}

	public function getTitle(): ?string
	{
		if ($this->site === null && $this->module === null && $this->main === null) {
			return null;
		}

		if ($this->site !== null && $this->module !== null) {
			$site = sprintf('%s %s', $this->site, $this->module);
		} elseif ($this->site !== null) {
			$site = $this->site;
		} elseif ($this->module !== null) {
			$site = $this->module;
		} else {
			$site = null;
		}

		$main = $this->main;

		if ($main === null || $site === null) {
			$separator = null;
		} elseif ($this->separator === null) {
			$separator = ' ';
		} else {
			$separator = sprintf(' %s ', $this->separator);
		}

		if ($this->revert === true) {
			return $site . $separator . $main;
		}
		return $main . $separator . $site;
	}

	public function render(): void
	{
		$this->template->setParameters([
			'title' => $this->getTitle(),
		]);
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
