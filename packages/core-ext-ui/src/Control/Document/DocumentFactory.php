<?php declare(strict_types = 1);

namespace Modette\UI\Control\Document;

interface DocumentFactory
{

	public function create(): DocumentControl;

}
