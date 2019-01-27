<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

use Nette\Caching;
use Nette\Caching\Storages\FileStorage;

class Cache extends Caching\Cache
{

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		if (!is_dir($config->getTempDir())) {
			mkdir($config->getTempDir());
		}

		$storage = new FileStorage($config->getTempDir());
		$this->config = $config;
		parent::__construct($storage);
	}

	/**
	 * @param string $file
	 * @return mixed
	 */
	public function getData(string $file): ?Data
	{
		return $this->load(md5(realpath($file)));
	}

	/**
	 * @param Data $data
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function saveData(Data $data): void
	{
		$this->save(md5(realpath($data->getFile())), $data);
	}

}
