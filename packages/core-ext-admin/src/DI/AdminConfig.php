<?php declare(strict_types = 1);

namespace Modette\Admin\DI;

class AdminConfig
{

	/** @var string */
	private $signInAction;

	/** @var string */
	private $signOutAction;

	public function __construct(string $signInAction, string $signOutAction)
	{
		$this->signInAction = $signInAction;
		$this->signOutAction = $signOutAction;
	}

	public function getSignInAction(): string
	{
		return $this->signInAction;
	}

	public function getSignOutAction(): string
	{
		return $this->signOutAction;
	}

}
