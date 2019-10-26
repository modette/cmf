<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Package\PackageInterface;
use Modette\Exceptions\Logic\InvalidStateException;
use Modette\ModuleInstaller\Configuration\ConfigurationValidator;
use Modette\ModuleInstaller\Configuration\PackageConfiguration;

final class PluginActivator
{

	/** @var PackageInterface */
	private $rootPackage;

	/** @var ConfigurationValidator */
	private $validator;

	/** @var PathResolver */
	private $pathResolver;

	/** @var string */
	private $unresolvedFileName;

	/** @var PackageConfiguration|null */
	private $configuration;

	/** @var string|null */
	private $schemaFileFullName;

	public function __construct(PackageInterface $rootPackage, ConfigurationValidator $validator, PathResolver $pathResolver, string $unresolvedFileName)
	{
		$this->rootPackage = $rootPackage;
		$this->validator = $validator;
		$this->pathResolver = $pathResolver;
		$this->unresolvedFileName = $unresolvedFileName;
	}

	public function isEnabled(): bool
	{
		if (!file_exists($this->getSchemaFileFullName())) {
			return false;
		}

		return $this->getRootPackageConfiguration()->getLoader() !== null;
	}

	public function getRootPackageConfiguration(): PackageConfiguration
	{
		if ($this->configuration !== null) {
			return $this->configuration;
		}

		if (!file_exists($this->getSchemaFileFullName())) {
			throw new InvalidStateException(sprintf(
				'Plugin is not activated, check with \'%s()\' before calling \'%s\'',
				self::class . '::isEnabled()',
				self::class . '::' . __METHOD__ . '()'
			));
		}

		$configuration = $this->configuration = $this->validator->validateConfiguration($this->rootPackage, $this->unresolvedFileName);

		return $configuration;
	}

	private function getSchemaFileFullName(): string
	{
		if ($this->schemaFileFullName !== null) {
			return $this->schemaFileFullName;
		}

		$configFile = $this->schemaFileFullName = $this->pathResolver->getSchemaFileFullName($this->rootPackage, $this->unresolvedFileName);

		return $configFile;
	}

}
