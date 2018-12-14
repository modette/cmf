<?php declare(strict_types = 1);

namespace Modette\UI\Menu;

use Modette\Core\Exception\Logic\InvalidStateException;
use Nette\Application\UI\Presenter;

class Node extends BaseNode
{

	/** @var Menu */
	private $menu;

	/** @var string|null */
	private $parentId;

	/** @var string|null */
	private $link;

	/** @var mixed[] */
	private $linkParameters = [];

	/** @var string|null */
	private $icon;

	public function __construct(string $id, string $title, Menu $menu, string $parentId)
	{
		parent::__construct($id, $title);
		$this->menu = $menu;
		$this->parentId = $parentId;
	}

	protected function getMenu(): Menu
	{
		return $this->menu;
	}

	public function getFullId(): string
	{
		return $this->parentId . '-' . $this->id;
	}

	public function hasLink(): bool
	{
		return $this->link !== null;
	}

	public function getLink(): string
	{
		if (!$this->hasLink()) {
			throw new InvalidStateException('Link is not set.');
		}

		if ($this->isLinkExternal()) {
			return $this->link;
		}

		if (!$this->menu->hasLinkGenerator()) {
			throw new InvalidStateException(sprintf(
				'Cannot generate link to %s, link generator is missing.',
				$this->link
			));
		}

		return $this->menu->getLinkGenerator()->link($this->link, $this->linkParameters);
	}

	public function setLink(string $link, array $parameters = []): self
	{
		$this->link = $link;
		$this->linkParameters = $parameters;
		return $this;
	}

	public function isActive(): bool
	{
		if (!$this->hasLink()) {
			throw new InvalidStateException('Link is not set.');
		}

		/** @var Presenter $presenter */
		$presenter = $this->menu->getApplication()->getPresenter();
		return $presenter->isLinkCurrent($this->link, $this->linkParameters);
	}

	public function isLinkExternal(): bool
	{
		//TODO - works with https://, ftp:// etc. but not with //
		//     - unfortunatelly // is also used for nette links to make them absolute
		//	   - preg_match? - if link contains only a-zA-Z0-9, : and // then it's nette link
		return filter_var($this->link, FILTER_VALIDATE_URL) !== false;
	}

	public function hasIcon(): bool
	{
		return $this->icon !== null;
	}

	/**
	 * @todo - translate table
	 */
	public function getIcon(): string
	{
		if (!$this->hasIcon()) {
			throw new InvalidStateException('Icon is not set');
		}

		return $this->icon;
	}

	public function setIcon(string $icon): self
	{
		$this->icon = $icon;
		return $this;
	}

}
