<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Body;

use Modette\UI\Controls\Control;
use Nette\Utils\Html;

class Body extends Control
{

	/** @var Html */
	private $element;

	public function __construct()
	{
		parent::__construct();
		$this->element = Html::el('body');
	}

	public function addAttribute(string $name, string $value): self
	{
		$this->element->appendAttribute($name, $value);
		return $this;
	}

	public function setAttribute(string $name, string $value): self
	{
		$this->element->setAttribute($name, $value);
		return $this;
	}

	public function renderStart(): void
	{
		$this->template->setParameters(
			[
				'bodyStart' => $this->element->startTag(),
			]
		);
		$this->template->setFile(__DIR__ . '/templates/start.latte');
		$this->template->render();
	}

	public function renderEnd(): void
	{
		$this->template->setParameters(
			[
				'bodyEnd' => $this->element->endTag(),
			]
		);
		$this->template->setFile(__DIR__ . '/templates/end.latte');
		$this->template->render();
	}

}
