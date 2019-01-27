<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator\Control;

interface ITranslateModal
{

	/**
	 * @return TranslateModal
	 */
	public function create(): TranslateModal;

}
