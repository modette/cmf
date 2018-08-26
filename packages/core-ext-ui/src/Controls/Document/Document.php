<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Document;

use Modette\UI\Controls\Body\Body;
use Modette\UI\Controls\Body\IBodyFactory;
use Modette\UI\Controls\Control;
use Modette\UI\Controls\Head\Head;
use Modette\UI\Controls\Head\IHeadFactory;
use Nette\Utils\Html;

class Document extends Control
{

	/** @var Html */
	private $element;

	/** @var IHeadFactory */
	private $headFactory;

	/** @var IBodyFactory */
	private $bodyFactory;

	public function __construct(IHeadFactory $headFactory, IBodyFactory $bodyFactory)
	{
		parent::__construct();
		$this->headFactory = $headFactory;
		$this->bodyFactory = $bodyFactory;
		$this->element = Html::el('html');
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
				'documentStart' => $this->element->startTag(),
			]
		);
		$this->template->setFile(__DIR__ . '/templates/start.latte');
		$this->template->render();
	}

	public function renderEnd(): void
	{
		$this->template->setParameters(
			[
				'documentEnd' => $this->element->endTag(),
			]
		);
		$this->template->setFile(__DIR__ . '/templates/end.latte');
		$this->template->render();
	}

	protected function createComponentHead(): Head
	{
		return $this->headFactory->create();
	}

	protected function createComponentBody(): Body
	{
		return $this->bodyFactory->create();
	}

}
