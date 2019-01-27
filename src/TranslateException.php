<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */
namespace Chomenko\Translator;

class TranslateException extends \Exception
{

	/**
	 * @param string $file
	 * @return TranslateException
	 */
	public static function fileNotWritable(string $file): TranslateException
	{
		return new self("File '{$file}' is not writable. Check Permission. Allow only for development!");
	}

	/**
	 * @param string $dir
	 * @return TranslateException
	 */
	public static function directoryNotWritable(string $dir): TranslateException
	{
		return new self("Directory '{$dir}' is not writable. Check Permission. Allow only for development!");
	}

	/**
	 * @param string $file
	 * @return TranslateException
	 */
	public static function fileNotFound(string $file): TranslateException
	{
		return new self("The '{$file}' file was not found");
	}

	/**
	 * @param string $allias
	 * @return TranslateException
	 */
	public static function fileAliasDoesNotExist(string $allias): TranslateException
	{
		return new self("Translate file alias '{$allias}' does not exist.");
	}

}
