<?php declare(strict_types = 1);

namespace Tests\Modette\Monorepo\Integration\DI;

use Modette\Core\Boot\Configurator;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Monorepo\Loader;
use Tests\Modette\Monorepo\MonorepoTestsHelper;

class ContainerCompilationTest extends TestCase
{

	protected function setUp(): void
	{
		MonorepoTestsHelper::generateLoader();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testProductionConsole(): void
	{
		$configurator = new Configurator(dirname(__DIR__, 3), new Loader());
		$configurator->setDebugMode(false);
		$configurator->addParameters([
			'consoleMode' => true,
		]);
		$configurator->initializeContainer();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testDebugConsole(): void
	{
		$configurator = new Configurator(dirname(__DIR__, 3), new Loader());
		$configurator->setDebugMode(true);
		$configurator->addParameters([
			'consoleMode' => true,
		]);
		$configurator->initializeContainer();
	}

}
