<?php declare(strict_types = 1);

namespace Modette\Admin\Base\Presenter;

use Modette\Admin\DI\AdminConfig;
use Modette\UI\Presenter\Base\BasePresenter;

abstract class BaseAdminPresenter extends BasePresenter
{

	/** @var AdminConfig */
	private $adminConfig;

	/** @var bool */
	private $requireLogin = true;

	public function injectAdmin(AdminConfig $adminConfig): void
	{
		$this->adminConfig = $adminConfig;
	}

	public function getAdminConfig(): AdminConfig
	{
		return $this->adminConfig;
	}

	public function setRequireLogin(bool $requireLogin): void
	{
		$this->requireLogin = $requireLogin;
	}

	protected function startup(): void
	{
		parent::startup();

		// Don't check permissions if login is not required (e.g. sign in page)
		if ($this->requireLogin === false) {
			return;
		}

		//TODO - rozdělit usera na front, admin a api usera (firewally?) a ověřit, že je user admin
		/*if($userIsNotAdmin) { // musí se ověřovat i admin s vypršeným přihlášením
			$this->flashError(
				'You are not signed as admin' // Pokud admin je, tak se přihlásí skrze administraci, pokud ne, tak ho pošleme do pryč
			);

			$this->redirect(
				$this->adminConfig->getSignInAction(),
				['backlink' => $this->storeRequest()]
			);
		}*/

		/*
		if (!$this->user->isLoggedIn()) {

			if ($this->user->getLogoutReason() === IUserStorage::INACTIVITY) {
				$this->flashInfo(
					$this->_('modette.admin.actions.logoutReason.inactivity')
				);
			}

			$this->redirect(
				$this->adminConfig->getSignInAction(),
				['backlink' => $this->storeRequest()]
			);
		}*/
	}

	protected function beforeRender(): void
	{
		parent::beforeRender();
		$this['document-head-meta']->setRobots(['noindex', 'nofollow']);
		$this['document-head-title']->setModule('Administration'); //todo - translate
	}

}
