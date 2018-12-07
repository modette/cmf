<?php declare(strict_types = 1);

namespace Modette\UI\Utils;

use Modette\UI\Base\Presenter\BasePresenter;
use stdClass;

/**
 * @method BasePresenter getPresenter()
 */
trait FlashMessages
{

	/**
	 * @internal
	 * @param string $message
	 * @param string $type
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 */
	public function flashMessage($message, $type = 'info')
	{
		parent::flashMessage(
			$message,
			$this->getPresenter()->getFlashesMapper()->getMappedFlashType($type)
		);
	}

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
