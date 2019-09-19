<?php declare(strict_types = 1);

namespace Modette\Templates\Themes;

use Modette\Exceptions\Logic\InvalidStateException;
use Nette\Bridges\ApplicationLatte\Template;
use stdClass;

/**
 * @property-read string     $baseUrl
 * @property-read stdClass[] $flashes
 */
class ThemedTemplate extends Template
{

	public const DEFAULT_VIEW = 'default';

	/** @var string */
	private $view = self::DEFAULT_VIEW;

	/** @var Theme|null */
	private $theme;

	/** @var object|null */
	private $templateAbleObject;

	public function setTheme(Theme $theme): void
	{
		$this->theme = $theme;
	}

	public function getTheme(): Theme
	{
		if ($this->theme === null) {
			throw new InvalidStateException('Theme is not set');
		}

		return $this->theme;
	}

	public function setTemplateAbleObject(object $templateAbleObject): void
	{
		$this->templateAbleObject = $templateAbleObject;
	}

	public function setView(string $view): void
	{
		$this->view = $view;
	}

	/**
	 * @param mixed[] $params
	 */
	public function render(?string $file = null, array $params = []): void
	{
		parent::render($this->getFilePath($file), $params);
	}

	/**
	 * @param mixed[] $params
	 */
	public function renderToString(?string $file = null, array $params = []): string
	{
		return parent::renderToString($this->getFilePath($file), $params);
	}

	private function getFilePath(?string $file): ?string
	{
		if ($file === null && ($file = $this->getFile()) === null && $this->theme !== null && ($templateAbleObject = $this->templateAbleObject) !== null) {
			return $this->theme->getTemplatePath($templateAbleObject, $this->view);
		}

		return $file;
	}

}
