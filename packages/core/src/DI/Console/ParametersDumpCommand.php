<?php declare(strict_types = 1);

namespace Modette\Core\DI\Console;

use Nette\DI\Container;
use Nette\DI\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ParametersDumpCommand extends Command
{

	private const TYPE_ARRAY = 'array';
	private const TYPE_BOOL = 'bool';
	private const TYPE_NUMBER = 'number';
	private const TYPE_STRING = 'string';
	private const TYPE_NULL = 'null';
	private const TYPE_OTHER = 'other';

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
		$this->printArray($output, $this->container->getParameters());

		return 0;
	}

	/**
	 * @param mixed[] $array
	 */
	private function printArray(OutputInterface $output, array $array, string $spaces = '  '): void
	{
		ksort($array);
		$byType = [
			self::TYPE_ARRAY => [],
			self::TYPE_BOOL => [],
			self::TYPE_NUMBER => [],
			self::TYPE_STRING => [],
			self::TYPE_NULL => [],
			self::TYPE_OTHER => [],
		];

		foreach ($array as $key => $item) {
			if (is_array($item)) {
				$type = self::TYPE_ARRAY;
			} elseif (is_bool($item)) {
				$type = self::TYPE_BOOL;
			} elseif (is_int($item) || is_float($item)) {
				$type = self::TYPE_NUMBER;
			} elseif (is_string($item)) {
				$type = self::TYPE_STRING;
			} elseif ($item === null) {
				$type = self::TYPE_NULL;
			} else {
				$type = self::TYPE_OTHER;
			}

			$byType[$type][$key] = $item;
		}

		$sorted = array_merge(
			$byType[self::TYPE_BOOL],
			$byType[self::TYPE_STRING],
			$byType[self::TYPE_NUMBER],
			$byType[self::TYPE_NULL],
			$byType[self::TYPE_OTHER],
			$byType[self::TYPE_ARRAY]
		);
		$lastKey = array_keys($sorted)[count($sorted) - 1];

		foreach ($sorted as $key => $item) {
			if (is_array($item)) {
				$output->writeln(sprintf(
					'%s<fg=cyan>%s</>:',
					$spaces,
					$key
				));

				$this->printArray($output, $item, $spaces . '  ');
			} else {
				$output->writeln(sprintf(
					'%s<fg=cyan>%s</>: %s',
					$spaces,
					$key,
					$this->valueToString($item)
				));

				if ($key === $lastKey) {
					$output->writeln('');
				} elseif (is_array(next($sorted))) {
					$output->writeln('');
					prev($sorted);
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
