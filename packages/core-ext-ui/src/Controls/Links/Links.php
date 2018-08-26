<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Links;

use Modette\UI\Controls\Control;

class Links extends Control
{

	/** @var string[] */
	private $links = [];

	/** @var string[] */
	private $alternateLanguages = [];

	/** @var mixed[] */
	private $alternateFeeds = [];

	public function addLink(string $href, string $rel): self
	{
		$this->links[$href] = $rel;
		return $this;
	}

	/**
	 * Adds alternate language
	 * <link rel="alternate" href="$href" hreflang="$hreflang">
	 */
	public function addAlternateLanguage(string $href, string $hreflang): self
	{
		$this->alternateLanguages[$href] = $hreflang;
		return $this;
	}

	/**
	 * Adds alternate feed
	 * <link rel="alternate" href="$href" type="$type" title="$title">
	 * <link rel="alternate" href="https://feeds.feedburner.com/example" type="application/rss+xml" title="RSS">
	 */
	public function addAlternateFeed(string $href, string $type, string $title): self
	{
		$this->alternateFeeds[$href] = [
			'type' => $type,
			'title' => $title,
		];
		return $this;
	}

	public function render(): void
	{
		$this->template->setParameters([
			'links' => $this->links,
			'alternateLanguages' => $this->alternateLanguages,
			'alternateFeeds' => $this->alternateFeeds,
		]);

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
