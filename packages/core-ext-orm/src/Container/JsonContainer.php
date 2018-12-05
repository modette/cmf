<?php declare(strict_types = 1);

namespace Modette\Orm\Container;

use Nette\Utils\Json;
use Nextras\Orm\Entity\ImmutableValuePropertyContainer;

class JsonContainer extends ImmutableValuePropertyContainer
{

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	protected function serialize($value)
	{
		return Json::encode($value);
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	protected function deserialize($value)
	{
		return Json::decode($value);
	}

}
