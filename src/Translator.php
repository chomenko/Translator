<?php
/**
 * Author: Mykola Chomenko
 * Email: chomenko@alistra.cz
 * Created: 28.01.2018 11:31
 */

namespace Chomenko\Translator;

use Nette\Localization\ITranslator;
use Nette\Utils\Html;

class Translator implements ITranslator{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Local[]
     */
    private $locals = array();

    /**
     * @param Config $config
     */
    public function __construct(Config $config, Cache $cache){
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * @return Config
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * @param string|null $lang
     * @return Local
     */
    public function getLocale($lang = null){

        if(!$lang){
            $lang = $this->config->getDefaultLang();
        }
        if(!array_key_exists($lang, $this->locals)){
            $file_lang = $this->config->getLocalDir().'/'.$lang.'.neon';
            if(!file_exists($file_lang)){
                touch($file_lang);
            }
            $this->locals[$lang] = $data = new Local($lang, new \SplFileObject($file_lang));
            return $data;
        }
        return $this->locals[$lang];
    }



    /**
     * @param $message
     * @param null $count
     * @param bool $translate_modal
     * @return mixed|Html|static
     * @throws \Exception
     */
    function translate($message, $count = NULL, $translate_modal = true){

        if($message instanceof Html){
            return $message;
        }

        $locale = $this->getLocale($this->config->getLang());
        $return = $locale->getValue($message);
        if($count) {
            preg_match_all('/%+([a-z0-9]+)/u', $return, $match);
            if (isset($match[1]) && $match[1]){
                if (is_array($count)) {
                    foreach ($match[1] as $i => $name) {
                        if (array_key_exists($name, $count)) {
                            $return = str_replace($match[0][$i], $count[$name], $return);
                        }
                    }
                }else{
                    $return = str_replace($match[0][0], $count, $return);
                }
            }
        }
        if($this->config->isTranslateModal() && $translate_modal){
            $el = Html::el('span');
            $el->class[] = "translate-item";
            $el->addAttributes(array(
                'data-trans-name' => $message
            ));
            $el->setText($return);
            return $el;
        }else{
            return $return;
        }
    }

}
