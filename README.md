Translator
==========
Extension from Nette\Localization

Required:
- [nette/di](https://github.com/nette/di)
- [nette/caching](https://github.com/nette/caching)
- [nette/neon](https://github.com/nette/neon)
- [nette/utils](https://github.com/nette/utils)
- [nettpack/stage](https://github.com/nettpack/stage)

## Install


````bash
composer require chomenko/translator
````

In base config.neon:
````neon
translator:
    localDir: %appDir%/Localization
    defaultLang: cs
    #pattern: '/%+([a-z0-9]+)/u' #old pattern style %value, new style is {{ value }}
extensions:
    translator: Chomenko\Translator\DI\TranslatorExtension
````

In base presenter:
````php
<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Chomenko\Translator\Translate;

class BasePresenter extends Presenter
{
 
    /**
     * Install Translator 
     */
    use Translate;
    
    public function startup() {
        parent::startup();
        $config = $this->translator->getConfig();
        $this->template->setTranslator($this->translator);
    }

}

````

In latte:
````latte
{_"name", ["Jméno"]} {*Default value. Used if value is not stored*}
{_"Name is: {{ name }}", ["name" => "Franta"]}
{_"Birthdate: {{ date }}", 1991}
````

## Translate Modal

![Translate Modal](.docs/modal.PNG?raw=true)

**Use only in the developer mode. Do not use for production!!**

The translation modal can be invoked by pressing ``CTRL + ALT + mouse CLICK translate item``. Translating elements turn red
Modal is required Bootstrap and JQuery. _If you are developing on Unix you will need to set the right to write_

In presenter:
````php
<?php
$config = $this->translator->getConfig();
$config->translateModalEnable();
````

In @layout.latte:
````latte
{control translateModal}
````
