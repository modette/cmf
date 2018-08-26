<?php declare(strict_types = 1);

namespace Modette\Core\Setup;

class SetupMeta
{

	/** @var WorkerMode */
	private $workerMode;

	/** @var bool */
	private $debugMode;

	/** @var bool */
	private $developmentServer;

	public function __construct(WorkerMode $workerMode, bool $debugMode, bool $developmentServer)
	{
		$this->workerMode = $workerMode;
		$this->debugMode = $debugMode;
		$this->developmentServer = $developmentServer;
	}

	public function getWorkerMode(): WorkerMode
	{
		return $this->workerMode;
	}

	/**
	 * Warning: This method only tells if current user run application in debug mode, not if server is dev-only
	 *
	 * @see SetupMeta::isDevelopmentServer()
	 */
	public function isDebugMode(): bool
	{
		return $this->debugMode;
	}

	public function isDevelopmentServer(): bool
	{
		return $this->developmentServer;
	}

}
