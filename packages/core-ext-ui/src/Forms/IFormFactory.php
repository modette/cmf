<?php declare(strict_types = 1);

namespace Modette\UI\Forms;

use Nette\Application\UI\Form;

interface IFormFactory
{

	public function create(): Form;

}
