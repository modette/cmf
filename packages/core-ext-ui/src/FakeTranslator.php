<?php declare(strict_types = 1);

namespace Modette\UI;

use Nette\Localization\ITranslator;

/**
 * @todo - real translator
 */
class FakeTranslator implements ITranslator
{

	/**
	 * @param mixed            $message
	 * @param mixed[]|int|null $count
	 * @return mixed
	 */
	public function translate($message, $count = null)
	{
		return $message;
	}

	public function getLanguage(): string
	{
		return 'cs-cz';
	}

}
