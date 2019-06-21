<?php declare(strict_types = 1);

namespace Modette\Core\Cache\Generator;

use Contributte\Console\Extra\Cache\Generators\IGenerator;
use Modette\Core\Boot\Configurator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiContainersCacheGenerator implements IGenerator
{

	/** @var Configurator */
	private $configurator;

	/** @var mixed[] */
	private $configs = [
		'debug' => [
			'parameters' => [
				'consoleMode' => false,
				'debugMode' => true,
			],
		],
		'production' => [
			'parameters' => [
				'consoleMode' => false,
				'debugMode' => false,
			],
		],
		'console' => [
			'parameters' => [
				'consoleMode' => true,
				'debugMode' => true,
			],
		],
	];

	public function __construct(Configurator $configurator)
	{
		$this->configurator = $configurator;
	}

	public function getDescription(): string
	{
		return 'DI Containers cache';
	}

	public function generate(InputInterface $input, OutputInterface $output): bool
	{
		if ($this->configs === []) {
			$output->writeln('<comment>Containers generating skipped, no containers configuration defined.</comment>');

			return false;
		}

		$output->writeln('<comment>Compiling DI containers</comment>');

		foreach ($this->configs as $container => $config) {
			$output->writeln(sprintf(
				'Compiling container %s',
				$container
			));
			$configurator = clone $this->configurator;
			$configurator->addParameters($config['parameters']);
			$configurator->loadContainer();
		}

		$output->writeln('<info>All containers successfully generated.</info>');

		return true;
	}

}
