<?php declare(strict_types = 1);

namespace Modette\UI\Forms;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\UploadControl;
use Nette\Forms\Validator;
use Nette\Localization\ITranslator;

class FormFactory
{

	/** @var bool */
	private $initialized = false;

	/** @var ITranslator */
	private $translator;

	public function __construct(ITranslator $translator)
	{
		$this->translator = $translator;
	}

	private function initialize(): void
	{
		if ($this->initialized) {
			return;
		}

		BaseControl::$idMask = 'frm-%2$s-%1$s';

		Validator::$messages[Form::EQUAL] = $this->translator->translate('modette.ui.forms.equal');
		Validator::$messages[Form::NOT_EQUAL] = $this->translator->translate('modette.ui.forms.notEqual');
		Validator::$messages[Form::FILLED] = $this->translator->translate('modette.ui.forms.filled');
		Validator::$messages[Form::BLANK] = $this->translator->translate('modette.ui.forms.blank');
		Validator::$messages[Form::MIN_LENGTH] = $this->translator->translate('modette.ui.forms.minLength');
		Validator::$messages[Form::MAX_LENGTH] = $this->translator->translate('modette.ui.forms.maxLength');
		Validator::$messages[Form::LENGTH] = $this->translator->translate('modette.ui.forms.length');
		Validator::$messages[Form::EMAIL] = $this->translator->translate('modette.ui.forms.email');
		Validator::$messages[Form::URL] = $this->translator->translate('modette.ui.forms.url');
		Validator::$messages[Form::INTEGER] = $this->translator->translate('modette.ui.forms.integer');
		Validator::$messages[Form::FLOAT] = $this->translator->translate('modette.ui.forms.float');
		Validator::$messages[Form::MIN] = $this->translator->translate('modette.ui.forms.min');
		Validator::$messages[Form::MAX] = $this->translator->translate('modette.ui.forms.max');
		Validator::$messages[Form::RANGE] = $this->translator->translate('modette.ui.forms.range');
		Validator::$messages[Form::MAX_FILE_SIZE] = $this->translator->translate('modette.ui.forms.maxFileSize');
		Validator::$messages[Form::MAX_POST_SIZE] = $this->translator->translate('modette.ui.forms.maxPostSize');
		Validator::$messages[Form::MIME_TYPE] = $this->translator->translate('modette.ui.forms.mimeType');
		Validator::$messages[Form::IMAGE] = $this->translator->translate('modette.ui.forms.image');
		Validator::$messages[SelectBox::VALID] = $this->translator->translate('modette.ui.forms.select');
		Validator::$messages[UploadControl::VALID] = $this->translator->translate('modette.ui.forms.upload');

		$this->initialized = true;
	}

	public function create(): Form
	{
		$this->initialize();
		return new Form();
	}

}
