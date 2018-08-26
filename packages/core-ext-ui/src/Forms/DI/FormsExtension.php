<?php declare(strict_types = 1);

namespace Modette\UI\Forms\DI;

use Modette\Core\Exception\Logic\InvalidArgumentException;
use Modette\UI\Forms\FormFactory;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;

final class FormsExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'messages' => [
			'PROTECTION' => 'modette.ui.forms.protection',
			'EQUAL' => 'modette.ui.forms.equal',
			'NOT_EQUAL' => 'modette.ui.forms.notEqual',
			'FILLED' => 'modette.ui.forms.filled',
			'BLANK' => 'modette.ui.forms.blank',
			'MIN_LENGTH' => 'modette.ui.forms.minLength',
			'MAX_LENGTH' => 'modette.ui.forms.maxLength',
			'LENGTH' => 'modette.ui.forms.length',
			'EMAIL' => 'modette.ui.forms.email',
			'URL' => 'modette.ui.forms.url',
			'INTEGER' => 'modette.ui.forms.integer',
			'FLOAT' => 'modette.ui.forms.float',
			'MIN' => 'modette.ui.forms.min',
			'MAX' => 'modette.ui.forms.max',
			'RANGE' => 'modette.ui.forms.range',
			'MAX_FILE_SIZE' => 'modette.ui.forms.maxFileSize',
			'MAX_POST_SIZE' => 'modette.ui.forms.maxPostSize',
			'MIME_TYPE' => 'modette.ui.forms.mimeType',
			'IMAGE' => 'modette.ui.forms.image',
			'Nette\Forms\Controls\SelectBox::VALID' => 'modette.ui.forms.select',
			'Nette\Forms\Controls\UploadControl::VALID' => 'modette.ui.forms.upload',
		],
	];

	public function loadConfiguration(): void
	{
		// Register form factory
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('factory'))
			->setImplement(FormFactory::class);
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->validateConfig($this->defaults);
		$messages = $config['messages'];

		$initialize = $class->getMethod('initialize');

		// Prevent collisions of multiple forms at one page
		$initialize->addBody('Nette\Forms\Controls\BaseControl::$idMask = \'frm-%2$s-%1$s\';');

		// Translate messages
		foreach ($messages as $type => $message) {
			if (defined('Nette\Forms\Form::' . $type)) {
				$initialize->addBody('Nette\Forms\Validator::$messages[Nette\Forms\Form::?] = ?;', [$type, $message]);
			} elseif (defined($type)) {
				$initialize->addBody('Nette\Forms\Validator::$messages[' . $type . '] = ?;', [$message]);
			} else {
				throw new InvalidArgumentException('Constant Nette\Forms\Form::' . $type . ' or constant ' . $type . ' does not exist.');
			}
		}
	}

}
