<?php
/**
 * Author: Mykola Chomenko
 * Email: chomenko@alistra.cz
 * Created: 31.01.2018 22:06
 */

namespace Chomenko\Translator\Control;

use Chomenko\Translator\Translator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class TranslateModal extends Control{

    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(Translator $translator){
        $this->translator = $translator;
    }


    public function render(){

        $config = $this->translator->getConfig();

        //$this->template->enable_modal = $this->translator->getConfig();
        $template = $this->template;
        $template->script = file_get_contents(__DIR__.'/modal.js');
        $template->setFile(__DIR__ . '/modal.latte');
        $template->render();
    }

    public function handleGetTranslateData(){
        $presenter =  $this->getPresenter();
        if($this->translator->getConfig()->isTranslateModal()){
            $name = $presenter->getParameter('name');
            $presenter->payload->translate = $this->translator->translate($name, null, false);
        }
        $presenter->sendPayload();
    }



    public function createComponentTranslateForm(){
        $form = new Form();
        $form->addText('name');
        $form->addTextArea('translate');
        $form->addSubmit('send', 'Translate');

        $form->onSuccess[] = function(Form $form, $values){
            $local = $this->translator->getLocale();
            if($values->name){
                $local->saveValue($values->name, $values->translate);
            }
            $this->redirect("this");
        };

        return $form;
    }
}