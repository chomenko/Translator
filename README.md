Translator
==========

Install
-------

````bash
# composer require chomenko/translator
````
In base config.neon:
````neon
translator:
    localDir: %appDir%/Localization
    defaultLang: cs
extensions:
    translator: Chomenko\Translator\DI\TranslatorExtension
````

In base presenter:
````php
<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Chomenko\Translator\Translate;

class BasePresenter extends Presenter{
 
    /**
     * Install Translator 
     */
    use Translate;
    
    public function startup() {
        parent::startup();
        
        $config = $this->translator->getConfig();
        $config->translateModalEnable();
        $this->template->setTranslator($this->translator);
    }
    
}
````