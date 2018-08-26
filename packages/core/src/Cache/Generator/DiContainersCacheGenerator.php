<?php declare(strict_types = 1);

namespace Modette\Core\Cache\Generator;

use Contributte\Console\Extra\Cache\Generators\IGenerator;
use Modette\Core\Configurator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiContainersCacheGenerator implements IGenerator
{

	/** @var Configurator */
	private $configurator;

	/** @var mixed[] */
	private $configs = [
		'debug' => [
			'debug' => true,
			'parameters' => [
				'consoleMode' => false,
			],
		],
		'production' => [
			'debug' => false,
			'parameters' => [
				'consoleMode' => false,
			],
		],
		'console' => [
			'debug' => true,
			'parameters' => [
				'consoleMode' => true,
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

		$output->writeln('Compiling DI containers...');

		foreach ($this->configs as $container => $config) {
			$output->writeln(sprintf(
				'Compiling container `%s`...',
				$container
			));
			$configurator = clone $this->configurator;
			$configurator->setDebugMode($config['debug']);
			$configurator->addParameters($config['parameters']);
			$configurator->createContainer();
		}

		$output->writeln('<info>All containers successfully generated.</info>');
		return true;
	}

}
