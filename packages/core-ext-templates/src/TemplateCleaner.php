<?php declare(strict_types = 1);

namespace Modette\Templates;

use Contributte\Events\Extra\Event\Latte\TemplateCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TemplateCleaner implements EventSubscriberInterface
{

	/**
	 * @return mixed[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [TemplateCreateEvent::class => 'cleanTemplate'];
	}

	public function cleanTemplate(TemplateCreateEvent $event): void
	{
		$template = $event->getTemplate();
		// $baseUrl is better, if really needed
		// Nette\Security is not used at all
		unset($template->basePath, $template->user);
	}

}
