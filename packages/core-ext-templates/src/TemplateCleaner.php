<?php declare(strict_types = 1);

namespace Modette\Templates;

use Nette\Bridges\ApplicationLatte\Template;

class TemplateCleaner
{

	public function __invoke(Template $template): void
	{
		unset(
			$template->_control,
			$template->_presenter,
			$template->baseUrl,
			$template->netteCacheStorage
		);
		//TODO unset($template->baseUri, $template->basePath); - nahradit pomocí rout
	}

}
