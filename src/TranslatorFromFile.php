<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

use Nette\Localization\ITranslator as IBaseTranslator;

class TranslatorFromFile implements IBaseTranslator, ITranslator
{

	/**
	 * @var string
	 */
	private $fileName;

	/**
	 * @var Translator
	 */
	private $parent;

	public function __construct(string $fileName, Translator $parent)
	{
		$this->fileName = $fileName;
		$this->parent = $parent;
	}

	/**
	 * @return Config
	 */
	public function getConfig(): Config
	{
		return $this->parent->getConfig();
	}

	/**
	 * @return Cache
	 */
	public function getCache(): Cache
	{
		return $this->parent->getCache();
	}

	/**
	 * @return Events\Listener
	 */
	public function getListener(): Events\Listener
	{
		return $this->parent->getListener();
	}

	/**
	 * @return Local[]
	 */
	public function getLocales()
	{
		return $this->parent->getLocales();
	}

	/**
	 * @param null $lang
	 * @return Local
	 * @throws \Exception
	 */
	public function getLocale($lang = NULL): Local
	{
		return $this->parent->getLocale($lang);
	}

	/**
	 * @param string $message
	 * @param null|array $parameters
	 * @param string|NULL $fileName
	 * @param bool $translateModal
	 * @return string|void
	 * @throws \Exception
	 */
	public function translate($message, $parameters = NULL, string $fileName = NULL, $translateModal = TRUE)
	{
		$fileName = $fileName ? $fileName : $this->fileName;
		return $this->parent->translate($message, $parameters, $fileName, $translateModal);
	}

	/**
	 * @param string $fileName
	 * @return TranslatorFromFile
	 */
	public function translatorFromFile(string $fileName): ITranslator
	{
		return $this->parent->translatorFromFile($fileName);
	}

}
