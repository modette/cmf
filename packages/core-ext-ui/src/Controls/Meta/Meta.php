<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Meta;

use Modette\UI\Controls\Control;

class Meta extends Control
{

	/** @var string[] */
	private $robots = [];

	/** @var string[] */
	private $metas = [];

	/** @var string[] */
	private $appLinkMetas = [];

	/** @var string[] */
	private $ogMetas = [];

	/** @var string[] */
	private $fbMetas = [];

	/** @var string[] */
	private $twitterMetas = [];

	/** @var string[] */
	private $httpEquivs = [];

	/**
	 * Sets robots
	 * <meta name="robots" content="$value1,$value2,$value3...">
	 *
	 * @param string[] $values
	 */
	public function setRobots(array $values): self
	{
		foreach ($values as $value) {
			$this->robots[$value] = $value;
		}
		return $this;
	}

	/**
	 * Adds standard meta <meta name="$name" content="$content">
	 */
	public function addMeta(string $name, string $content): self
	{
		$this->metas[$name] = $content;
		return $this;
	}

	/**
	 * Adds application link meta
	 * <meta property="al:$property" content="$content">
	 */
	public function addApplicationLink(string $property, string $content): self
	{
		$this->appLinkMetas['al:' . $property] = $content;
		return $this;
	}

	/**
	 * Adds open graph meta
	 * <meta property="og:$property" content="$content">
	 */
	public function addOpenGraph(string $property, string $content): self
	{
		$this->ogMetas['og:' . $property] = $content;
		return $this;
	}

	/**
	 * Adds facebook meta
	 * <meta property="fb:$property" content="$content" />
	 */
	public function addFacebook(string $property, string $content): self
	{
		$this->fbMetas['fb:' . $property] = $content;
		return $this;
	}

	/**
	 * Adds twitter meta
	 * <meta name="twitter:$name" content="$content">
	 */
	public function addTwitter(string $name, string $content): self
	{
		$this->twitterMetas['twitter:' . $name] = $content;
		return $this;
	}

	/**
	 * Adds httpEquiv meta
	 * <meta http-equiv="$httpEquiv" content="$content">
	 */
	public function addHttpEquiv(string $httpEquiv, string $content): self
	{
		$this->httpEquivs[$httpEquiv] = $content;
		return $this;
	}

	/**
	 * Sets author meta
	 * <meta name="author" content="$author">
	 */
	public function setAuthor(string $author): self
	{
		$this->metas['author'] = $author;
		return $this;
	}

	/**
	 * Sets description meta
	 * <meta name="description" content="$description">
	 */
	public function setDescription(string $description): self
	{
		$this->metas['description'] = $description;
		return $this;
	}

	public function render(): void
	{
		if (array_filter($this->robots) !== []) {
			$this->metas['robots'] = implode(', ', array_filter($this->robots));
		}

		$this->template->setParameters([
			'metas' => $this->metas,
			'httpEquivs' => $this->httpEquivs,
			'appLinks' => $this->appLinkMetas,
			'ogs' => $this->ogMetas,
			'fbMetas' => $this->fbMetas,
			'twitterMetas' => $this->fbMetas,
		]);

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
