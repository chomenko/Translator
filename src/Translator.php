<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

use Nette\Localization\ITranslator as IBaseTranslator;
use Nette\Utils\Html;

class Translator implements IBaseTranslator, ITranslator
{

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @var Events\Listener
	 */
	private $listener;

	/**
	 * @var Local[]
	 */
	private $locals = [];

	/**
	 * @var TranslatorFromFile[]
	 */
	private $translators = [];

	/**
	 * Translator constructor.
	 * @param Config $config
	 * @param Cache $cache
	 * @param Events\Listener $listener
	 */
	public function __construct(Config $config, Cache $cache, Events\Listener $listener)
	{
		$this->config = $config;
		$this->cache = $cache;
		$this->listener = $listener;
	}

	/**
	 * @return Config
	 */
	public function getConfig(): Config
	{
		return $this->config;
	}

	/**
	 * @return Cache
	 */
	public function getCache(): Cache
	{
		return $this->cache;
	}

	/**
	 * @return Events\Listener
	 */
	public function getListener(): Events\Listener
	{
		return $this->listener;
	}

	/**
	 * @return Local[]
	 */
	public function getLocales()
	{
		return $this->locals;
	}

	/**
	 * @param string|null $lang
	 * @return Local
	 */
	public function getLocale($lang = NULL): Local
	{
		if (!$lang) {
			$lang = $this->config->getDefaultLang();
		}

		if (!array_key_exists($lang, $this->locals)) {
			$fileLang = $this->config->getLocalDir() . '/' . $lang . '.neon';
			if (!file_exists($fileLang)) {
				touch($fileLang);
			}
			$this->locals[$lang] = $data = new Local($lang, $this);
			$this->listener->emit(Events\Listener::ON_CREATE_LOCALE, $data, $this);
			return $data;
		}

		return $this->locals[$lang];
	}

	/**
	 * @param string|Html $message
	 * @param null $parameters
	 * @param string|null $fileName
	 * @param bool $translateModal
	 * @return mixed|Html|static
	 * @throws \Exception
	 */
	public function translate($message, $parameters = NULL, string $fileName = NULL, $translateModal = TRUE)
	{
		$this->listener->emit(Events\Listener::ON_BEFORE_TRANSLATE, $this, ...func_get_args());

		if ($message instanceof Html) {
			return $message;
		}

		$locale = $this->getLocale($this->config->getLang());
		$value = $locale->getValue($message, $this, $fileName);
		$return = $message;

		if ($value instanceof $this) {
			if (is_array($parameters) && count($parameters) == 1 && array_key_exists(0, $parameters)) {
				$return = $parameters[0];
				if ($this->config->isAutoSave()) {
					$locale->saveValue($message, $return);
				}
			}
		} else {
			$return = $value;
		}

		if ($parameters) {
			preg_match_all($this->config->getPattern(), $return, $match);
			if (isset($match[1]) && $match[1]) {
				if (is_array($parameters)) {
					foreach ($match[1] as $i => $name) {
						if (array_key_exists($name, $parameters)) {
							$return = str_replace($match[0][$i], $parameters[$name], $return);
						}
					}
				} else {
					$return = str_replace($match[0][0], $parameters, $return);
				}
			}
		}

		if ($this->config->isTranslateModal() && $translateModal) {
			$el = Html::el('span');
			$el->class[] = "translate-item";
			$el->addAttributes([
				'data-trans-name' => $message,
				'data-trans-file' => $fileName,
			]);
			$el->setText($return);
			$return = $el;
		}

		$this->listener->emit(Events\Listener::ON_AFTER_TRANSLATE, $this, $return);
		return $return;
	}

	/**
	 * @param string $fileName
	 * @return TranslatorFromFile
	 */
	public function translatorFromFile(string $fileName): ITranslator
	{
		if (!array_key_exists($fileName, $this->translators)) {
			$this->translators[$fileName] = new TranslatorFromFile($fileName, $this);
		}
		return $this->translators[$fileName];
	}

}
