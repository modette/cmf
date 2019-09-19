<?php declare(strict_types = 1);

namespace Modette\UI\Themes;

use Modette\Exceptions\Logic\NotImplementedException;
use Modette\Templates\Themes\Exception\NoTemplateFoundException;
use Modette\Templates\Themes\ThemedTemplate;
use Modette\UI\Themes\Resolving\ControlResolver;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\Responses\TextResponse;
use Nette\FileNotFoundException;

/**
 * @method ThemedTemplate getTemplate()
 * @property-read ThemedTemplate $template
 */
trait ThemeAblePresenter
{

	/**
	 * @return string[]
	 * @throws NotImplementedException
	 * @internal
	 */
	final public function formatLayoutTemplateFiles(): array
	{
		throw new NotImplementedException(sprintf(
			'Implementation of \'%s\' is in findLayoutTemplateFiles(), do not call method directly',
			__METHOD__
		));
	}

	/**
	 * @internal
	 */
	final public function findLayoutTemplateFile(): ?string
	{
		$layout = $this->getLayout();

		if ($layout === false) {
			return null;
		}

		if (is_string($layout) && preg_match('#/|\\\\#', $layout) === 1) {
			return $layout;
		}

		if ($layout === true || $layout === null) {
			$layout = 'layout';
		}

		try {
			return $this->getTemplate()->getTheme()->getTemplatePath($this, $layout, [
				ControlResolver::OPTION_PRESENTER_TEMPLATE_TYPE => ControlResolver::PRESENTER_TEMPLATE_TYPE_LAYOUT,
			]);
		} catch (NoTemplateFoundException $exception) {
			throw new FileNotFoundException(sprintf(
				'Layout of \'%s\' not found. None of the following templates exists: %s',
				get_class($exception->getTemplateAbleObject()),
				implode('\', \'', $exception->getTriedPaths())
			), 0, $exception);
		}
	}

	/**
	 * @return string[]
	 * @throws NotImplementedException
	 * @internal
	 */
	final public function formatTemplateFiles(): array
	{
		throw new NotImplementedException(sprintf(
			'Implementation of \'%s\' is in sendTemplates(), do not call method directly',
			__METHOD__
		));
	}

	/**
	 * @throws BadRequestException
	 * @throws AbortException
	 * @internal
	 */
	final public function sendTemplate(): void
	{
		$template = $this->getTemplate();
		if ($template->getFile() === null) {
			try {
				$file = $this->getTemplate()->getTheme()->getTemplatePath($this, $this->getView(), [
					ControlResolver::OPTION_PRESENTER_TEMPLATE_TYPE => ControlResolver::PRESENTER_TEMPLATE_TYPE_MAIN,
				]);
				$template->setFile($file);
			} catch (NoTemplateFoundException $exception) {
				throw new BadRequestException(sprintf(
					'Page not found. None of the following templates exists: %s',
					implode('\', \'', $exception->getTriedPaths())
				), 0, $exception);
			}
		}

		$this->sendResponse(new TextResponse($template));
	}

}
