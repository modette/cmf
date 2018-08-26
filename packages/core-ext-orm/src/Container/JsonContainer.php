<?php declare(strict_types = 1);

namespace Modette\Orm\Container;

use Nette\Utils\Json;
use Nextras\Orm\Entity\ImmutableValuePropertyContainer;

class JsonContainer extends ImmutableValuePropertyContainer
{

	/**
	 * @internal
	 * @param  mixed $value
	 * @return mixed
	 */
	public function convertToRawValue($value)
	{
		return Json::encode($value);
	}

	/**
	 * @internal
	 * @param  mixed $value
	 * @return mixed
	 */
	public function convertFromRawValue($value)
	{
		return Json::decode($value);
	}

}
