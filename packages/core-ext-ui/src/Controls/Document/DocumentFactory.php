<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Document;

interface DocumentFactory
{

	public function create(): DocumentControl;

}
