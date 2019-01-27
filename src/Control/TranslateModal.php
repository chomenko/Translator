<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator\Control;

use Chomenko\Translator\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class TranslateModal extends Control
{

	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @param Translator $translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	public function render(): void
	{
		$template = $this->template;
		$template->script = file_get_contents(__DIR__ . '/modal.js');
		$template->setFile(__DIR__ . '/modal.latte');
		$template->render();
	}

	public function handleGetTranslateData(): void
	{
		$presenter = $this->getPresenter();
		if ($this->translator->getConfig()->isTranslateModal()) {
			$name = $presenter->getParameter('name');
			$file = $presenter->getParameter('file');
			$presenter->payload->translate = $this->translator->translate($name, NULL, $file, FALSE);
		}
		$presenter->sendPayload();
	}

	public function createComponentTranslateForm(): Form
	{
		$form = new Form();
		$form->addText('name');
		$form->addText('file');
		$form->addTextArea('translate');
		$form->addSubmit('send', 'Translate');

		$form->onSuccess[] = function (Form $form, $values) {
			$local = $this->translator->getLocale();
			if ($values->name) {
				$file = NULL;
				if (!empty($values->file)) {
					$file = $values->file;
				}
				$local->saveValue($values->name, $values->translate, $file);
			}
			$this->redirect("this");
		};
		return $form;
	}

}
