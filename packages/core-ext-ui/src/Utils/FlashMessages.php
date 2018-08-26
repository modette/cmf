<?php declare(strict_types = 1);

namespace Modette\UI\Utils;

use stdClass;

trait FlashMessages
{

	/**
	 * @internal
	 * @param string $message
	 * @param string $type
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	abstract public function flashMessage($message, $type = 'info');

	public function flashInfo(string $message): stdClass
	{
		return $this->flashMessage($message, 'info');
	}

	public function flashSuccess(string $message): stdClass
	{
		return $this->flashMessage($message, 'success');
	}

	public function flashWarning(string $message): stdClass
	{
		return $this->flashMessage($message, 'warning');
	}

	public function flashError(string $message): stdClass
	{
		return $this->flashMessage($message, 'danger');
	}

}
