<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator;

class Data
{

	/**
	 * @var string
	 */
	private $file;

	/**
	 * @var array
	 */
	private $data = [];

	/**
	 * @var integer
	 */
	private $time;

	/**
	 * @param string $file
	 * @param array $data
	 * @param int $time
	 */
	public function __construct(string $file, array $data, int $time)
	{
		$this->file = $file;
		$result = [];
		$this->recursiveImplode($data, $result);
		$this->data = $result;
		$this->time = $time;
	}

	/**
	 * @return string
	 */
	public function getFile(): string
	{
		return $this->file;
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @param string $key
	 * @param null $default
	 * @return mixed|null
	 */
	public function getValue(string $key, $default = NULL)
	{
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		return $default;
	}

	/**
	 * @return int
	 */
	public function getTime(): int
	{
		return $this->time;
	}

	/**
	 * @param int $time
	 */
	public function setTime($time)
	{
		$this->time = $time;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function setValue(string $key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * @return array
	 */
	public function getTreeData(): array
	{
		$result = [];
		foreach ($this->data as $key => $value) {
			if (self::isValidKey($key)) {
				$this->explodeTree($result, $key, $value, ".");
				continue;
			}
			$result[$key] = $value;
		}
		return $result;
	}

	/**
	 * @param array $arr
	 * @param string $path
	 * @param mixed $value
	 * @param string $glue
	 */
	private function explodeTree(&$arr, $path, $value, $glue)
	{
		$keys = explode($glue, $path);
		foreach ($keys as $key) {
			if (is_string($arr)) {
				$val = $arr;
				$arr = [
					"_" => $val,
				];
			}
			$arr = &$arr[$key];
		}
		if (is_array($arr)) {
			$arr["_"] = $value;
		} else {
			$arr = $value;
		}
	}

	/**
	 * @param array $array
	 * @param array $result
	 * @param string $glue
	 * @param null|string $key
	 */
	private function recursiveImplode($array, &$result = [], $glue = ".", $key = NULL): void
	{
		foreach ($array as $name => $value) {
			$name = $key ? $key . ($name === "_" ? "" : $glue . $name) : $name;
			if (is_array($value)) {
				$this->recursiveImplode($value, $result, $glue, $name);
			} else {
				$result[$name] = $value;
			}
		}
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public static function isValidKey($key): bool
	{
		if (strpos($key, " ") !== FALSE) {
			return FALSE;
		}
		foreach (explode('.', $key) as $item) {
			if (empty($item)) {
				return FALSE;
			}
		}
		return TRUE;
	}

}
