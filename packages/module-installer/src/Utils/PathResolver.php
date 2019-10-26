<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Modette\ModuleInstaller\Monorepo\SimulatedPackage;

final class PathResolver
{

	/** @var Composer */
	private $composer;

	public function __construct(Composer $composer)
	{
		$this->composer = $composer;
	}

	public function getAbsolutePath(PackageInterface $package): string
	{
		if ($package === $this->composer->getPackage()) {
			return $this->getRootDir();
		}

		if ($package instanceof SimulatedPackage) {
			return $package->getPackageDirectory();
		}

		return $this->composer->getInstallationManager()->getInstallPath($package);
	}

	public function getRelativePath(PackageInterface $package): string
	{
		return ltrim(substr($this->getAbsolutePath($package), strlen($this->getRootDir())), '/');
	}

	public function getSchemaFileFullName(PackageInterface $package, string $unresolvedFileName): string
	{
		// File name is absolute, use it
		if (realpath($unresolvedFileName) === $unresolvedFileName && file_exists($unresolvedFileName)) {
			return $unresolvedFileName;
		}

		return $this->getAbsolutePath($package) . '/' . $unresolvedFileName;
	}

	/**
	 * Relative path to schema file resolved from schema file fqn
	 */
	public function getSchemaFileRelativeName(PackageInterface $package, string $schemaFileFqn): string
	{
		return mb_substr($schemaFileFqn, mb_strlen($this->getAbsolutePath($package)) + 1);
	}

	public function getRootDir(): string
	{
		// Composer supports ProjectInstaller only during create-project command so let's hope no-one change vendor-dir
		return dirname($this->composer->getConfig()->get('vendor-dir'));
	}

	/**
	 * @param mixed[] $parts
	 */
	public function buildPathFromParts(array $parts): string
	{
		$paths = [];

		foreach ($parts as $part) {
			if ($part !== '/' && $part !== '') {
				$paths[] = $part;
			}
		}

		return implode('/', $paths);
	}

}
