<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller\Command;

use Composer\Command\BaseCommand as ComposerBaseCommand;
use Modette\ModuleInstaller\Plugin;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseCommand extends ComposerBaseCommand
{

	protected const OPTION_FILE = 'file';

	protected function configure(): void
	{
		$this->addOption(
			self::OPTION_FILE,
			'f',
			InputOption::VALUE_REQUIRED,
			sprintf('Use different config file than %s (for tests)', Plugin::DEFAULT_FILE_NAME),
			Plugin::DEFAULT_FILE_NAME
		);
	}

}
