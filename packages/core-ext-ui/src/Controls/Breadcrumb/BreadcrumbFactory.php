<?php declare(strict_types = 1);

namespace Modette\UI\Controls\Breadcrumb;

interface BreadcrumbFactory
{

	public function create(): BreadcrumbControl;

}
