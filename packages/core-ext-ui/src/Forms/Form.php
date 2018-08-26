<?php declare(strict_types = 1);

namespace Modette\UI\Forms;

use Modette\Core\Exception\Logic\InvalidArgumentException;
use Nette\Application\UI\Form as BaseForm;
use Nette\Localization\ITranslator;

class Form extends BaseForm
{

	/**
	 * @internal
	 */
	public function setTranslator(?ITranslator $translator = null): self
	{
		throw new InvalidArgumentException('Do not use form built-in translator, translate values passed into form directly.');
	}

}
