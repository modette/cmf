<?php declare(strict_types = 1);

namespace Modette\UI\Control\Links;

use Modette\UI\Base\Control\BaseControl;

/**
 * @property-read LinksTemplate $template
 */
class LinksControl extends BaseControl
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
		$this->template->links = $this->links;
		$this->template->alternateFeeds = $this->alternateFeeds;
		asort($this->alternateLanguages);
		$this->template->alternateLanguages = $this->alternateLanguages;

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
