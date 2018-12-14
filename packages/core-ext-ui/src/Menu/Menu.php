<?php declare(strict_types = 1);

namespace Modette\UI\Menu;

use Modette\Core\Exception\Logic\InvalidStateException;
use Nette\Application\Application;
use Nette\Application\LinkGenerator;
use Nette\Localization\ITranslator;

class Menu extends BaseNode
{

	/** @var Application|null */
	private $application;

	/** @var LinkGenerator|null */
	private $linkGenerator;

	/** @var ITranslator|null */
	private $translator;

	public function __construct(
		string $id,
		string $title,
		Application $application = null,
		LinkGenerator $linkGenerator = null,
		ITranslator $translator = null
	)
	{
		parent::__construct($id, $title);
		$this->application = $application;
		$this->linkGenerator = $linkGenerator;
		$this->translator = $translator;
	}

	protected function getMenu(): Menu
	{
		return $this;
	}

	public function getFullId(): string
	{
		return $this->id;
	}

	public function hasApplication(): bool
	{
		return $this->application !== null;
	}

	public function getApplication(): Application
	{
		if (!$this->hasApplication()) {
			throw new InvalidStateException('Application is not available.');
		}

		return $this->application;
	}

	public function hasLinkGenerator(): bool
	{
		return $this->linkGenerator !== null;
	}

	public function getLinkGenerator(): LinkGenerator
	{
		if (!$this->hasLinkGenerator()) {
			throw new InvalidStateException('Link generator is not available.');
		}

		return $this->linkGenerator;
	}

	public function hasTranslator(): bool
	{
		return $this->translator !== null;
	}

	public function getTranslator(): ITranslator
	{
		if (!$this->hasTranslator()) {
			throw new InvalidStateException('Translator is not available.');
		}

		return $this->translator;
	}

}
