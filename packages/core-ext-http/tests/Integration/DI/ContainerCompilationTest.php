<?php declare(strict_types = 1);

namespace Tests\Modette\Http\Integration\DI;

use PHPUnit\Framework\TestCase;
use Tests\Modette\Http\HttpTestsHelper;

class ContainerCompilationTest extends TestCase
{

	/**
	 * @doesNotPerformAssertions
	 */
	public function testProductionHttp(): void
	{
		$configurator = HttpTestsHelper::createConfigurator();
		$configurator->setDebugMode(true);
		$configurator->addParameters([
			'consoleMode' => false,
		]);
		$configurator->initializeContainer();
	}

}
