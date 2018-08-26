<?php declare(strict_types = 1);

namespace Modette\Admin\Presenters\Admin;

use Modette\UI\Presenters\Presenter;

abstract class Admin extends Presenter
{

	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this['document-head-meta']->setRobots(['noindex', 'nofollow']);
		$this['document-head-title']->setModule('Administration'); //todo - do konfigurace (s možností překladu?)
	}

}
