<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

final class CommandProvider implements CommandProviderCapability
{

	/**
	 * @return BaseCommand[]
	 */
	public function getCommands(): array
	{
		return [
			new ModuleValidateCommand(),
		];
	}

}
