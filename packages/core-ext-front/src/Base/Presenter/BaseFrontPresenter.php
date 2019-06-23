<?php declare(strict_types = 1);

namespace Modette\Front\Base\Presenter;

use Modette\UI\Base\Presenter\BasePresenter;

abstract class BaseFrontPresenter extends BasePresenter
{

	protected function beforeRender(): void
	{
		parent::beforeRender();

		if ($this->developmentServer) {
			$this->getHttpResponse()->addHeader('X-Robots-Tag', 'none'); // Only supported by some crawlers
			$this['document-head-meta']->setRobots(['nofollow', 'noindex']);
		} else {
			$this['document-head-meta']->setRobots(['index', 'follow']);
		}
	}

}
