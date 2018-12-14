<?php declare(strict_types = 1);

namespace Modette\UI\Menu;

use Modette\Core\Exception\Logic\InvalidStateException;

/**
 * @todo - show conditions?
 * @todo - permissions
 */
abstract class BaseNode
{

	/** @var string */
	protected $id;

	/** @var string */
	protected $title;

	/** @var Node[] */
	protected $nodes = [];

	public function __construct(string $id, string $title)
	{
		$this->id = $id;
		$this->title = $title;
	}

	abstract protected function getMenu(): Menu;

	abstract public function getFullId(): string;

	public function getId(): string
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		//TODO - translate
		return $this->title;
	}

	public function hasNodes(): bool
	{
		return $this->nodes !== [];
	}

	public function hasNode(string $id): bool
	{
		return array_key_exists($id, $this->nodes);
	}

	public function getNode(string $id): Node
	{
		if (!$this->hasNode($id)) {
			throw new InvalidStateException(\sprintf(
				'Node "%s (%s)" not found.',
				$id,
				$this->getFullId() . '-' . $id
			));
		}

		return $this->nodes[$id];
	}

	public function addNode(string $id, string $title): Node
	{
		if (array_key_exists($id, $this->nodes)) {
			throw new InvalidStateException(sprintf(
				'Node with id "%s (%s)" already exists',
				$id,
				$this->getFullId() . '-' . $id
			));
		}

		return $this->nodes[$id] = new Node($id, $title, $this->getMenu(), $this->getFullId());
	}

	public function hasActiveNode(): bool
	{
		if ($this instanceof Node && $this->isActive()) {
			return true;
		}

		foreach ($this->nodes as $node) {
			if ($node->hasActiveNode()) {
				return true;
			}
		}

		return false;
	}

}
