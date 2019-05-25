<?php declare(strict_types = 1);

namespace Modette\Core\Setup\Worker;

use Modette\Core\Setup\DataProvider\DataProvider;
use Modette\Core\Setup\SetupHelper;

class DataWorker implements Worker
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

	public function getName(): string
	{
		return 'data';
	}

	public function work(SetupHelper $helper): void
	{
		foreach ($this->dataProviders as $provider) {
			$provider->run($helper);
		}
	}

}
