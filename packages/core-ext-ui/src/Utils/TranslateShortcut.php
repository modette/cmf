<?php declare(strict_types = 1);

namespace Modette\UI\Utils;

use Modette\UI\Presenter\Base\BasePresenter;

/**
 * @method BasePresenter getPresenter()
 */
trait TranslateShortcut
{

	/**
	 * @param mixed  ...$parameters
	 */
	protected function _(string $message, ...$parameters): string // phpcs:ignore
	{
		return $this->getPresenter()->getTranslator()->translate($message, ...$parameters);
	}

}
