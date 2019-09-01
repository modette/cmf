<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Exception;

use Modette\Exceptions\LogicalException;

final class InvalidConfigurationException extends LogicalException
{

	public function __construct(string $package, string $file, string $message)
	{
		$error = sprintf(
			'Package %s have invalid %s: %s',
			$package,
			$file,
			$message
		);
		parent::__construct($error);
	}

}
