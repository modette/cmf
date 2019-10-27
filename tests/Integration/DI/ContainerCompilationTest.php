<?php declare(strict_types = 1);

namespace Tests\Modette\Monorepo\Integration\DI;

use PHPUnit\Framework\TestCase;
use Tests\Modette\Monorepo\MonorepoTestsHelper;

class ContainerCompilationTest extends TestCase
{

	/**
	 * @doesNotPerformAssertions
	 */
	public function testProductionConsole(): void
	{
		$configurator = MonorepoTestsHelper::createConfigurator();
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
		$configurator = MonorepoTestsHelper::createConfigurator();
		$configurator->setDebugMode(true);
		$configurator->addParameters([
			'consoleMode' => true,
		]);
		$configurator->initializeContainer();
	}

}
