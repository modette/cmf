<?php declare(strict_types = 1);

namespace Modette\Core\Setup;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;

class SetupHelper
{

	/** @var WorkerMode */
	private $workerMode;

	/** @var bool */
	private $debugMode;

	/** @var bool */
	private $developmentServer;

	/** @var Application */
	private $application;

	/** @var OutputInterface */
	private $output;

	public function __construct(
		WorkerMode $workerMode,
		bool $debugMode,
		bool $developmentServer,
		Application $application,
		OutputInterface $output
	)
	{
		$this->workerMode = $workerMode;
		$this->debugMode = $debugMode;
		$this->developmentServer = $developmentServer;
		$this->application = $application;
		$this->output = $output;
	}

	public function getWorkerMode(): WorkerMode
	{
		return $this->workerMode;
	}

	/**
	 * Warning: This method only tells if current user run application in debug mode, not if server is dev-only
	 *
	 * @see SetupHelper::isDevelopmentServer()
	 */
	public function isDebugMode(): bool
	{
		return $this->debugMode;
	}

	public function isDevelopmentServer(): bool
	{
		return $this->developmentServer;
	}

	public function getApplication(): Application
	{
		return $this->application;
	}

	public function getOutput(): OutputInterface
	{
		return $this->output;
	}

}
