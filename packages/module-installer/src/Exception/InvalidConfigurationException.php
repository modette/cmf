<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Exception;

use Composer\Package\PackageInterface;
use Modette\Exceptions\LogicalException;

final class InvalidConfigurationException extends LogicalException
{

	public function __construct(PackageInterface $package, string $file, string $message)
	{
		$error = sprintf(
			'Package %s have invalid %s: %s',
			$package->getName(),
			$file,
			$message
		);
		parent::__construct($error);
	}

}
