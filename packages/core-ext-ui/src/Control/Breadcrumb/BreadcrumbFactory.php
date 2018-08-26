<?php declare(strict_types = 1);

namespace Modette\UI\Control\Breadcrumb;

interface BreadcrumbFactory
{

	public function create(): BreadcrumbControl;

}
