<?php declare(strict_types = 1);

namespace Modette\Front\Presenters\Front;

use Modette\UI\Presenters\Presenter;

abstract class Front extends Presenter
{

	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this['document-head-meta']->setRobots(['index', 'follow']);
	}

}
