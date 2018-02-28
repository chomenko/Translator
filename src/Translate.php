<?php
/**
 * Author: Mykola Chomenko
 * Email: chomenko@alistra.cz
 * Created: 31.01.2018 21:59
 */

namespace Chomenko\Translator;


use Chomenko\Translator\Control\ITranslateModal;

trait Translate{

    /**
     * @var Translator @inject
     */
    public $translator;

    /**
     * @var ITranslateModal @inject
     */
    public $translate_modal;

    /**
     * @return Control\TranslateModal
     */
    public function createComponentTranslateModal(){
        return $this->translate_modal->create();
    }

}