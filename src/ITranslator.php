<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

interface ITranslator
{

	/**
	 * @return Config
	 */
	public function getConfig(): Config;

	/**
	 * @return Cache
	 */
	public function getCache(): Cache;

	/**
	 * @return Events\Listener
	 */
	public function getListener(): Events\Listener;

	/**
	 * @return Local[]
	 */
	public function getLocales();

	/**
	 * @param null $lang
	 * @return Local
	 * @throws \Exception
	 */
	public function getLocale($lang = NULL): Local;

	/**
	 * @param string $fileName
	 * @return ITranslator
	 */
	public function translatorFromFile(string $fileName): ITranslator;

}
