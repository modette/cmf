<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Files;

use Composer\Downloader\FilesystemException;
use Modette\Exceptions\Logic\InvalidArgumentException;
use Modette\Exceptions\Logic\InvalidStateException;
use Nette\Neon\Neon;
use Nette\Schema\Helpers;
use Nette\Utils\Validators;

final class NeonReader
{

	private const INCLUDES_KEY = 'includes';

	private const PREVENT_MERGING_SUFFIX = '!';

	/** @var bool[] */
	private $loadedFiles = [];

	/**
	 * @return mixed[]
	 */
	public function read(string $file): array
	{
		if (!is_file($file) || !is_readable($file)) {
			throw new FilesystemException(sprintf('File "%s" is missing or is not readable.', $file));
		}

		if (isset($this->loadedFiles[$file])) {
			throw new InvalidStateException(sprintf('Recursively included file "%s"', $file));
		}

		$this->loadedFiles[$file] = true;

		$data = $this->process((array) Neon::decode(file_get_contents($file)));

		$result = [];

		if (isset($data[self::INCLUDES_KEY])) {
			Validators::assert($data[self::INCLUDES_KEY], 'list', sprintf('section "includes" in file "%s"', $file));
			$includes = $data[self::INCLUDES_KEY];

			foreach ($includes as $include) {
				$include = $this->expandIncludedFileName($include, $file);
				$result = Helpers::merge($this->read($include), $result);
				assert(is_array($result));
			}
		}

		unset($data[self::INCLUDES_KEY], $this->loadedFiles[$file]);

		$result = Helpers::merge($data, $result);
		assert(is_array($result));

		return $result;
	}

	private function expandIncludedFileName(string $includedFile, string $mainFile): string
	{
		return preg_match('#([a-z]+:)?[/\\\\]#Ai', $includedFile) // is absolute
			? $includedFile
			: dirname($mainFile) . '/' . $includedFile;
	}

	/**
	 * @param mixed[] $array
	 * @return mixed[]
	 */
	private function process(array $array): array
	{
		$res = [];

		foreach ($array as $key => $value) {
			if (is_string($key) && substr($key, -1) === self::PREVENT_MERGING_SUFFIX) {
				if (!is_array($value) && $value !== null) {
					throw new InvalidArgumentException(sprintf(
						'Replacing operator is available only for arrays, item "%s" is not array.',
						$key
					));
				}

				$key = substr($key, 0, -1);
				$value[Helpers::PREVENT_MERGING] = true;
			}

			if (is_array($value)) {
				$value = $this->process($value);
			}

			$res[$key] = $value;
		}

		return $res;
	}

}
