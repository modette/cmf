<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Driver;

use Modette\Core\Setup\DataProvider\DataProvider;

class DataDriver implements Driver
{

	/** @var DataProvider[] */
	private $dataProviders;

	/**
	 * @param DataProvider[] $dataProviders
	 */
	public function __construct(array $dataProviders)
	{
		$this->dataProviders = $dataProviders;
	}

	public function upgrade(): void
	{
		// Aktualizovat data a testovací data
	}

	public function reload(): void
	{
		// Smazat všechna data, vygenerovat testovací data, vložit kompletně data
	}

}
