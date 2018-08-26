<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Worker;

use Modette\Core\Setup\DataProvider\DataProvider;
use Modette\Core\Setup\SetupMeta;
use Modette\Core\Setup\WorkerMode;

class DataWorker implements Worker
{

	/** @var DataProvider[] */
	private $dataProviders; // phpcs:ignore

	/**
	 * @param DataProvider[] $dataProviders
	 */
	public function __construct(array $dataProviders)
	{
		$this->dataProviders = $dataProviders;
	}

	public function work(SetupMeta $meta): void
	{
		if ($meta->getWorkerMode()->is(WorkerMode::UPGRADE())) { // phpcs:ignore
			// Aktualizovat data a testovací data
		} else { // phpcs:ignore
			// Smazat všechna data, vygenerovat testovací data, vložit kompletně data
		}
	}

}
