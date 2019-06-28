<?php declare(strict_types = 1);

namespace Modette\UI\Control\Document;

use Modette\UI\Base\Control\BaseControl;
use Modette\UI\Control\Body\BodyControl;
use Modette\UI\Control\Body\BodyFactory;
use Modette\UI\Control\Head\HeadControl;
use Modette\UI\Control\Head\HeadFactory;
use Nette\Utils\Html;

/**
 * @property-read DocumentTemplate $template
 */
class DocumentControl extends BaseControl
{

	/** @var Html */
	private $element;

	/** @var HeadFactory */
	private $headFactory;

	/** @var BodyFactory */
	private $bodyFactory;

	public function __construct(HeadFactory $headFactory, BodyFactory $bodyFactory)
	{
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
		$this->template->documentStart = $this->element->startTag();

		$this->template->setFile(__DIR__ . '/templates/start.latte');
		$this->template->render();
	}

	public function renderEnd(): void
	{
		$this->template->documentEnd = $this->element->endTag();

		$this->template->setFile(__DIR__ . '/templates/end.latte');
		$this->template->render();
	}

	protected function createComponentHead(): HeadControl
	{
		return $this->headFactory->create();
	}

	protected function createComponentBody(): BodyControl
	{
		return $this->bodyFactory->create();
	}

}
