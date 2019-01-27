<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

use Chomenko\Translator\Control\ITranslateModal;

trait Translate
{

	/**
	 * @var Translator @inject
	 */
	public $translator;

	/**
	 * @var ITranslateModal @inject
	 */
	public $translate_modal;

	/**
	 * @return Control\TranslateModal
	 */
	public function createComponentTranslateModal()
	{
		return $this->translate_modal->create();
	}

}
