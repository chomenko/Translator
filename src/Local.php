<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

use Chomenko\Translator\Events\Listener;
use Nette\Utils\Finder;

class Local
{

	/**
	 * @var string
	 */
	private $lang = NULL;

	/**
	 * @var Translator
	 */
	private $translator;

	/**
	 * @var LangFile[]
	 */
	private $files = [];

	/**
	 * @param string $lang
	 * @param Translator $translator
	 */
	public function __construct(string $lang, Translator $translator)
	{
		$this->translator = $translator;
		$config = $translator->getConfig();
		$this->lang = $lang;
		/** @var \SplFileInfo $file */
		foreach (Finder::find([$lang . "_*.neon", $lang . ".neon"])->in($config->getLocalDir()) as $file) {
			$name = $file->getBasename('.' . $file->getExtension());
			$prefix = $lang . "_";
			if (substr($name, 0, strlen($prefix)) === $prefix) {
				$name = substr($name, strlen($prefix), strlen($name));
			}
			$langFile = new LangFile($file);
			$this->addFile($name, $langFile);
		}
	}

	/**
	 * @return ILangFile
	 * @throws TranslateException
	 */
	public function getMasterFile(): ILangFile
	{
		if (!array_key_exists($this->getLang(), $this->files)) {
			throw TranslateException::fileNotFound($this->getLang() . ".neon");
		}
		return $this->files[$this->getLang()];
	}

	/**
	 * @param string $aliasFileName
	 * @param ILangFile $file
	 */
	public function addFile(string $aliasFileName, ILangFile $file)
	{
		$this->files[$aliasFileName] = $file;
		$file->attached($aliasFileName, $this->getTranslator());
		$this->translator->getListener()->emit(Listener::ON_ADD_FILE, $file, $aliasFileName);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param string|NULL $aliasFileName
	 * @throws TranslateException
	 */
	public function saveValue(string $name, $value, string $aliasFileName = NULL): void
	{
		$file = $this->getMasterFile();

		if ($aliasFileName !== NULL) {
			if (!array_key_exists($aliasFileName, $this->files)) {
				barDump($aliasFileName);
				throw TranslateException::fileAliasDoesNotExist($aliasFileName);
			}
			$file = $this->files[$aliasFileName];
		}
		$this->translator->getListener()->emit(Listener::ON_SAVE_VALUE, $file, $name, $value, $aliasFileName);
		$file->saveValue($name, $value);
	}

	/**
	 * @param string $name
	 * @param null $default
	 * @param null|string $aliasFileName
	 * @return string|array|null
	 */
	public function getValue($name, $default = NULL, string $aliasFileName = NULL)
	{
		$value = new \stdClass();

		if ($aliasFileName && array_key_exists($aliasFileName, $this->files)) {
			$file = $this->files[$aliasFileName];
			$value = $file->getValue($name, new \stdClass());
		}

		if ($value instanceof \stdClass && array_key_exists($this->lang, $this->files)) {
			$file = $this->files[$this->lang];
			$value = $file->getValue($name, new \stdClass());
		}

		if (!$value instanceof \stdClass) {
			return $value;
		}

		return $default;
	}

	/**
	 * @return string
	 */
	public function getLang(): string
	{
		return $this->lang;
	}

	/**
	 * @return Translator
	 */
	public function getTranslator(): Translator
	{
		return $this->translator;
	}

	/**
	 * @return LangFile[]
	 */
	public function getFiles(): array
	{
		return $this->files;
	}

}
