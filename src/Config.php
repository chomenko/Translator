<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

class Config
{

	/**
	 * @var string
	 */
	protected $localDir;

	/**
	 * @var string
	 */
	protected $defaultLang;

	/**
	 * @var string
	 */
	protected $selectLang;

	/**
	 * @var bool
	 */
	protected $treeStructure = TRUE;

	/**
	 * @var string
	 */
	protected $childTempDir = "chomenko.translator";

	/**
	 * @var string
	 */
	protected $tempDir;

	/**
	 * @var string
	 */
	protected $cacheName = 'translator';

	/**
	 * @var boolean
	 */
	protected $debugMode = FALSE;

	/**
	 * @var bool
	 */
	protected $autoSave = FALSE;

	/**
	 * @var bool
	 */
	protected $translateModal = FALSE;

	/**
	 * @var string
	 */
	private $pattern = "~{{\s([a-z0-9]+)\s}}~";

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * Config constructor.
	 * @param bool $values
	 * @throws \Exception
	 */
	public function __construct($values = FALSE)
	{
		if ($values) {
			foreach ($values as $name => $value) {
				if (property_exists($this, $name)) {
					$this->$name = $value;
				}
			}
		}
		if (empty($this->localDir)) {
			throw new \Exception('Default lang directory is not define. Check your configuration file and set local_dir: "lang/dir"');
		}
		if (empty($this->defaultLang)) {
			throw new \Exception('Default lang is not define check your configuration file ad set default_lang: "cs"');
		}
	}

	/**
	 * @return string
	 */
	public function getLocalDir(): string
	{
		return $this->localDir;
	}

	/**
	 * @return string
	 */
	public function getDefaultLang(): string
	{
		return $this->defaultLang;
	}

	/**
	 * @param string $lang
	 */
	public function setDefaultLang($lang): void
	{
		$this->defaultLang = $lang;
	}

	/**
	 * @return string
	 */
	public function getLang(): string
	{
		return $this->selectLang ? $this->selectLang : $this->defaultLang;
	}

	/**
	 * @return string
	 */
	public function getTempDir(): string
	{
		return $this->tempDir . '/' . $this->childTempDir;
	}

	/**
	 * @return string
	 */
	public function getCacheName(): string
	{
		return $this->cacheName;
	}

	/**
	 * @return bool
	 */
	public function isDebugMode(): bool
	{
		return $this->debugMode;
	}

	/**
	 * @return bool
	 */
	public function isAutoSave(): bool
	{
		return $this->autoSave;
	}

	/**
	 * @return bool
	 */
	public function isTreeStructure(): bool
	{
		return $this->treeStructure;
	}

	/**
	 * @param bool $bool
	 */
	public function setTreeStructure($bool = TRUE): void
	{
		$this->treeStructure = $bool;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix): void
	{
		$this->prefix = $prefix;
	}

	/**
	 * @return string|null
	 */
	public function getPrefix(): ?string
	{
		return $this->prefix;
	}

	/**
	 * @param bool $enable
	 */
	public function autoSaveEnable($enable = TRUE): void
	{
		$this->autoSave = $enable;
	}

	/**
	 * @return bool
	 */
	public function isTranslateModal(): bool
	{
		return $this->translateModal;
	}

	/**
	 * @param bool $enable
	 */
	public function translateModalEnable($enable = TRUE): void
	{
		$this->translateModal = $enable;
	}

	/**
	 * @return string
	 */
	public function getPattern(): string
	{
		return $this->pattern;
	}

	/**
	 * @param string $pattern
	 * @return $this
	 */
	public function setPattern($pattern): Config
	{
		$this->pattern = $pattern;
		return $this;
	}

}
