<?php declare(strict_types = 1);

namespace Modette\Front\Presenters\Front;

use Modette\UI\Presenters\Base\BasePresenter;

abstract class BaseFrontPresenter extends BasePresenter
{

	protected function beforeRender(): void
	{
		parent::beforeRender();
		if ($this->getContainerParameters()->isDevelopmentServer()) {
			$this['document-head-meta']->setRobots(['nofollow', 'noindex']);
		} else {
			$this['document-head-meta']->setRobots(['index', 'follow']);
		}
	}

}
