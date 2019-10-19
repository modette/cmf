<?php declare(strict_types = 1);

namespace Tests\Modette\Monorepo\Integration;

use Modette\ModuleInstaller\Tests\PluginTestsHelper;
use PHPUnit\Framework\TestCase;
use Tests\Modette\Monorepo\MonorepoTestsHelper;

class ModuleValidationTest extends TestCase
{

	public function test(): void
	{
		self::assertSame(
			0,
			PluginTestsHelper::validateModule(MonorepoTestsHelper::getModuleFile())
		);
	}

}
