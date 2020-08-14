<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Translator\DI;

use Chomenko\Translator\Events;
use Chomenko\Translator\Cache;
use Chomenko\Translator\Config;
use Chomenko\Translator\Control\ITranslateModal;
use Chomenko\Translator\Tracy\Panel;
use Chomenko\Translator\Translator;
use Nette\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

class TranslatorExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$userConfig = $this->getConfig();
		$config = $builder->parameters + $userConfig;

		$configuration = $builder->addDefinition($this->prefix('config'))
			->setFactory(Config::class, [$config]);

		$builder->addDefinition($this->prefix('listener'))
			->setFactory(Events\Listener::class);

		$builder->addDefinition($this->prefix('cache'))
			->setFactory(Cache::class, [$configuration]);

		$builder->addDefinition($this->prefix('translator'))
			->setFactory(Translator::class);

		$builder->addFactoryDefinition($this->prefix('modal'))
            ->setImplement(ITranslateModal::class);

		$builder->addDefinition($this->prefix('panel'))
			->setFactory(Panel::class);

		$builder->getDefinition('tracy.bar')
			->addSetup('$service->addPanel($this->getService(?));', [$this->prefix('panel')]);
	}

	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('translator', new TranslatorExtension());
		};
	}

}
