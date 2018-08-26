<?php declare(strict_types = 1);

namespace Modette\UI\Utils;

class FlashesMapper
{

	/** @var string[] */
	private $flashTypesMapping = [];

	/**
	 * @param string[] $mapping
	 */
	public function addFlashTypesMapping(array $mapping): void
	{
		$this->flashTypesMapping += $mapping;
	}

	public function getMappedFlashType(string $type): string
	{
		return $this->flashTypesMapping[$type] ?? $type;
	}

}
