<?php declare(strict_types = 1);

namespace Modette\UI\Control\Meta;

use Modette\UI\Base\Control\BaseControl;

/**
 * @property-read MetaTemplate $template
 */
class MetaControl extends BaseControl
{

	/** @var string[] */
	private $httpEquivs = [];

	/** @var string[] */
	private $metasWithName = [];

	/** @var string[] */
	private $metasWithProperty = [];

	/**
	 * Sets robots
	 * <meta name="robots" content="$value1,$value2,$value3...">
	 *
	 * @param string[] $values
	 */
	public function setRobots(array $values): self
	{
		$this->metasWithName['robots'] = implode(', ', $values);

		return $this;
	}

	/**
	 * Adds standard meta <meta name="$name" content="$content">
	 */
	public function addMeta(string $name, string $content): self
	{
		$this->metasWithName[$name] = $content;

		return $this;
	}

	/**
	 * Adds application link meta
	 * <meta property="al:$property" content="$content">
	 */
	public function addApplicationLink(string $property, string $content): self
	{
		$this->metasWithProperty['al:' . $property] = $content;

		return $this;
	}

	/**
	 * Adds open graph meta
	 * <meta property="og:$property" content="$content">
	 */
	public function addOpenGraph(string $property, string $content): self
	{
		$this->metasWithProperty['og:' . $property] = $content;

		return $this;
	}

	/**
	 * Adds facebook meta
	 * <meta property="fb:$property" content="$content" />
	 */
	public function addFacebook(string $property, string $content): self
	{
		$this->metasWithProperty['fb:' . $property] = $content;

		return $this;
	}

	/**
	 * Adds twitter meta
	 * <meta name="twitter:$name" content="$content">
	 */
	public function addTwitter(string $name, string $content): self
	{
		$this->metasWithName['twitter:' . $name] = $content;

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
		$this->metasWithName['author'] = $author;

		return $this;
	}

	/**
	 * Sets description meta
	 * <meta name="description" content="$description">
	 */
	public function setDescription(string $description): self
	{
		$this->metasWithName['description'] = $description;

		return $this;
	}

	public function render(): void
	{
		$this->template->httpEquivs = $this->httpEquivs;
		ksort($this->metasWithName);
		$this->template->metasWithName = $this->metasWithName;
		ksort($this->metasWithProperty);
		$this->template->metasWithProperty = $this->metasWithProperty;

		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}

}
