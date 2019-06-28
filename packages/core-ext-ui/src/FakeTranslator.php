<?php declare(strict_types = 1);

namespace Modette\UI;

use Nette\Localization\ITranslator;

/**
 * @todo - real translator
 */
class FakeTranslator implements ITranslator
{

	/**
	 * @param mixed $message
	 * @param array<int, mixed> $parameters
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function translate($message, ...$parameters): string
	{
		return $message;
	}

	public function getLanguage(): string
	{
		return 'en-US';
	}

}
