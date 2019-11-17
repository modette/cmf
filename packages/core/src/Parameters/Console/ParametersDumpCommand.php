<?php declare(strict_types = 1);

namespace Modette\Core\Parameters\Console;

use Modette\Core\Parameters\Utils\ParametersSorter;
use Nette\DI\Container;
use Nette\DI\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ParametersDumpCommand extends Command
{

	/** @var string */
	protected static $defaultName = 'nette:di:parameters';

	/** @var Container */
	private $container;

	public function __construct(Container $container)
	{
		parent::__construct();
		$this->container = $container;
	}

	protected function configure(): void
	{
		$this->setName(self::$defaultName);
		$this->setDescription('Dump DI container parameters');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->printSortedParameters(
			$output,
			ParametersSorter::sort($this->container->getParameters())
		);

		return 0;
	}

	/**
	 * @param mixed[] $parameters
	 */
	private function printSortedParameters(OutputInterface $output, array $parameters, string $spaces = '  '): void
	{
		$lastKey = array_keys($parameters)[count($parameters) - 1];

		foreach ($parameters as $key => $item) {
			if (is_array($item)) {
				$output->writeln(sprintf(
					'%s<fg=cyan>%s</>:',
					$spaces,
					$key
				));

				$this->printSortedParameters($output, $item, $spaces . '  ');
			} else {
				$output->writeln(sprintf(
					'%s<fg=cyan>%s</>: %s',
					$spaces,
					$key,
					$this->valueToString($item)
				));

				if ($key === $lastKey) {
					$output->writeln('');
				} elseif (is_array(next($parameters))) {
					$output->writeln('');
					prev($parameters);
				}
			}
		}
	}

	/**
	 * @param mixed $value
	 */
	private function valueToString($value): string
	{
		if (is_bool($value)) {
			$value = $value ? 'true' : 'false';
			$fg = 'magenta';
		} elseif (is_int($value) || is_float($value)) {
			$fg = 'green';
		} elseif (is_string($value)) {
			$fg = 'yellow';
		} elseif ($value === null) {
			$value = 'null';
			$fg = 'white';
		} else {
			$value = $value instanceof Statement ? 'Statement' : 'Unknown';
			$fg = 'red';
		}

		return sprintf(
			'<fg=%s>%s</>',
			$fg,
			$value
		);
	}

}
