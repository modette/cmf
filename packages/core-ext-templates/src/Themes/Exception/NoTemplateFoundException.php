<?php declare(strict_types = 1);

namespace Modette\Templates\Themes\Exception;

use Modette\Exceptions\LogicalException;

final class NoTemplateFoundException extends LogicalException
{

	/** @var string[] */
	private $triedPaths;

	/** @var object */
	private $templateAbleObject;

	/**
	 * @param string[] $triedPaths
	 */
	public function __construct(array $triedPaths, object $templateAbleObject)
	{
		parent::__construct(sprintf(
			'Template of \'%s\' not found. None of the following templates exists: %s',
			get_class($templateAbleObject),
			implode('\', \'', $triedPaths)
		));

		$this->triedPaths = $triedPaths;
		$this->templateAbleObject = $templateAbleObject;
	}

	/**
	 * @return string[]
	 */
	public function getTriedPaths(): array
	{
		return $this->triedPaths;
	}

	public function getTemplateAbleObject(): object
	{
		return $this->templateAbleObject;
	}

}
