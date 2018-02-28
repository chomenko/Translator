<?php

/**
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */

namespace Chomenko\Translator\DI;

use Chomenko\Translator\Cache;
use Chomenko\Translator\Config;
use Chomenko\Translator\Control\ITranslateModal;
use Chomenko\Translator\Translator;
use Nette\Configurator;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

class TranslatorExtension extends CompilerExtension{

    public function loadConfiguration() {
        
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig($builder->parameters);

        $configuration = $builder->addDefinition($this->prefix('config'))
		->setClass(Config::class, array($config))
                ->setInject(false);
        
        $builder->addDefinition($this->prefix('cache'))
		->setClass(Cache::class, array($configuration))
                 ->setInject(false);

        $builder->addDefinition($this->prefix('translator'))
		    ->setClass(Translator::class);

        $builder->addDefinition($this->prefix('modal'))
            ->setImplement(ITranslateModal::class);
    }

     /**
     * @param Configurator $configurator
     */
    public static function register(Configurator $configurator){
        $configurator->onCompile[] = function ($config, Compiler $compiler){
            $compiler->addExtension('translator', new TranslatorExtension());
        };
    }
}
