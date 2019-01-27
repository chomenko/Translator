<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator\Tracy;

use Chomenko\Translator\Translator;
use Nette\Utils\Html;
use Tracy\IBarPanel;
use Latte;

class Panel implements IBarPanel
{

	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @param Translator $translator
	 */
	public function __construct(Translator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @return Html
	 */
	private function getIconHtml()
	{
		$path = __DIR__ . '/icon.png';
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents(__DIR__ . '/icon.png');
		$src = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return Html::el("img")
			->setAttribute("src", $src)
			->setAttribute('weight', 16)
			->setAttribute('height', 16);
	}

	/**
	 * @return string
	 */
	public function getTab()
	{
		$count = count($this->getFiles());
		return Html::el()->addHtml($this->getIconHtml())->addHtml(" " . $count);
	}

	/**
	 * @return string
	 */
	public function getPanel()
	{
		$latte = new Latte\Engine;
		$data = [
			"files" => $this->getFiles(),
		];
		return $latte->renderToString(__DIR__ . '/panel.latte', $data);
	}

	/**
	 * @return array
	 */
	protected function getFiles(): array
	{
		$locales = $this->translator->getLocales();
		$files = [];
		foreach ($locales as $local) {
			foreach ($local->getFiles() as $name => $file) {
				$files[] = [
					"lang" => $local->getLang(),
					"name" => $name === $local->getLang() ? "" : $name,
					"file" => $file->getRealPath(),
				];
			}
		}
		return $files;
	}

}
