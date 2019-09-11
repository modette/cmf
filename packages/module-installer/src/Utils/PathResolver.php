<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Utils;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Modette\ModuleInstaller\Files\File;

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
		$installationManager = $this->composer->getInstallationManager();

		if ($package === $this->composer->getPackage()) {
			return $this->getRootDir();
		}

		return $installationManager->getInstallPath($package);
	}

	public function getRelativePath(PackageInterface $package): string
	{
		return substr($this->getAbsolutePath($package), strlen($this->getRootDir()));
	}

	public function getConfigFileFqn(PackageInterface $package, string $fileName = File::DEFAULT_NAME): string
	{
		return $this->getAbsolutePath($package) . '/' . $fileName;
	}

	public function getRootDir(): string
	{
		// Composer supports ProjectInstaller only during create-project command so let's hope no-one change vendor-dir
		return dirname($this->composer->getConfig()->get('vendor-dir'));
	}

}
