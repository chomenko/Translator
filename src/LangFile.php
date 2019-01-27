<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;

class LangFile extends \SplFileObject implements ILangFile
{

	/**
	 * @var Cache
	 */
	protected $cache;

	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * @var Data
	 */
	protected $data;

	/**
	 * @var bool
	 */
	protected $shutdown = FALSE;

	/**
	 * @param string $aliasFileName
	 * @param Translator $translator
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function attached(string $aliasFileName, Translator $translator): void
	{
		$this->config = $translator->getConfig();
		$this->cache = $translator->getCache();
		$data = $this->loadFileData($this->getRealPath());
		$this->data = $data;
	}

	/**
	 * @param string $file
	 * @return Data|null
	 * @throws \Exception
	 * @throws \Throwable
	 */
	protected function loadFileData(string $file): ?Data
	{
		if (!file_exists($file)) {
			return NULL;
		}

		$data = $this->cache->getData($file);
		$time = filemtime($file);
		if ($data instanceof Data && $data->getTime() === $time) {
			return $data;
		}

		$contents = file_get_contents($file);
		$values = (array)Neon::decode($contents);
		$data = new Data($file, $values, $time);
		$this->cache->saveData($data);
		return $data;
	}

	/**
	 * @param string $name
	 * @param null $default
	 * @return string|array|null
	 */
	public function getValue(string $name, $default = NULL)
	{
		return $this->data->getValue($name, $default);
	}

	/**
	 * @param string $name
	 * @param string|integer $value
	 * @throws \Exception
	 */
	public function saveValue(string $name, $value): void
	{
		$this->data->setValue($name, $value);
		if (!$this->shutdown) {
			$this->shutdown = TRUE;
			register_shutdown_function(function () {
				$this->save();
			});
		}
	}

	protected function save()
	{
		if (!is_writable($this->getRealPath())) {
			throw TranslateException::fileNotWritable($this->getRealPath());
		}
		$content = Neon::encode($this->data->getTreeData(), Encoder::BLOCK);
		file_put_contents($this->getRealPath(), $content);
	}

}
