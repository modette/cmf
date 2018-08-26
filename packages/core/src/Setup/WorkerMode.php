<?php declare(strict_types = 1);

namespace Modette\Core\Setup;

use MabeEnum\Enum;

/**
 * @method static self UPGRADE()
 * @method static self RELOAD()
 *
 * @method int getValue()
 */
class WorkerMode extends Enum
{

	public const UPGRADE = 1;

	public const RELOAD = 2;

}
