<?php declare(strict_types = 1);

namespace Modette\UI\Control\Body;

use Modette\UI\Control\Base\BaseControl;
use Nette\Utils\Html;

/**
 * @property-read BodyTemplate $template
 */
class BodyControl extends BaseControl
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
		$this->template->bodyStart = $this->element->startTag();

		$this->template->setFile(__DIR__ . '/templates/start.latte');
		$this->template->render();
	}

	public function renderEnd(): void
	{
		$this->template->bodyEnd = $this->element->endTag();

		$this->template->setFile(__DIR__ . '/templates/end.latte');
		$this->template->render();
	}

}
