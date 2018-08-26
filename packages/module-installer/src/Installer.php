<?php declare(strict_types = 1);

namespace Modette\ModuleInstaller;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;

class Installer extends LibraryInstaller
{

	/** @var Composer */
	protected $composer;

	/** @var IOInterface */
	protected $io;

	public function __construct(Composer $composer, IOInterface $io)
	{
		parent::__construct($io, $composer);
		$this->composer = $composer;
		$this->io = $io;
	}

}
