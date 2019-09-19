<?php declare(strict_types = 1);

namespace Modette\Templates;

use Contributte\Events\Extra\Event\Latte\TemplateCreateEvent;
use Modette\Templates\Themes\Theme;
use Modette\Templates\Themes\ThemedTemplate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TemplateConfigurator implements EventSubscriberInterface
{

	/** @var Theme */
	private $theme;

	/**
	 * @return mixed[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [TemplateCreateEvent::class => 'configureTemplate'];
	}

	public function __construct(Theme $theme)
	{
		$this->theme = $theme;
	}

	public function configureTemplate(TemplateCreateEvent $event): void
	{
		$template = $event->getTemplate();
		assert($template instanceof ThemedTemplate);

		// $baseUrl is better, if really needed
		// Nette\Security is not used at all
		unset($template->basePath, $template->user);

		$template->setTheme($this->theme);
	}

}
