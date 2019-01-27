<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

interface ILangFile
{

	/**
	 * @param Translator $translate
	 */
	public function attached(string $aliasFileName, Translator $translate): void;

	/**
	 * @param string $name
	 * @param null $default
	 * @return mixed
	 */
	public function getValue(string $name, $default = NULL);

	/**
	 * @param string $name
	 * @param string|integer $value
	 */
	public function saveValue(string $name, $value): void;

}
