<?php

/**
 * Description of Settings
 *
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */

namespace Chomenko\Translator;


class Config{
    
    
    /**
     * @var string
     */
    protected $localDir;

    /**
     * @var string 
     */
    protected $defaultLang;

    /**
     * @var string
     */
    protected $selectLang;

    /**
     * @var bool
     */
    protected $treeStructure = true;

    /**
     * @var string
     */
    protected $childTempDir= "chomenko.translator";

    /**
     * @var string 
     */
    protected $tempDir;

    /**
     * @var string 
     */
    protected $cacheName = 'translator';

    /**
     * @var boolean 
     */
    protected $debugMode = false;

    /**
     * @var bool
     */
    protected $autoSave = false;

    /**
     * @var bool
     */
    protected $translateModal = false;


    /**
     * @var string
     */
    protected $prefix;

    /**
     * Config constructor.
     * @param bool $values
     * @throws \Exception
     */
    public function __construct($values = false) {
        if($values){
            foreach($values as $name => $value){
                if(property_exists($this, $name)){
                    $this->$name = $value;
                }
            }
        }
        if(empty($this->localDir)){
            throw new \Exception('Default lang directory is not define. Check your configuration file and set local_dir: "lang/dir"');
        }
        if(empty($this->defaultLang)){
            throw new \Exception('Default lang is not define check your configuration file ad set default_lang: "cs"');
        }
    }

    /**
     * @return string
     */
    public function getLocalDir(){
        return $this->localDir;
    }

    /**
     * @return string
     */
    public function getDefaultLang(){
        return $this->defaultLang;
    }

    /**
     * @param string $lang
     */
    public function setDefaultLang($lang){
        $this->defaultLang = $lang;
    }

    /**
     * @return string
     */
    public function getLang(){
        return $this->selectLang ? $this->selectLang : $this->defaultLang;
    }

    /**
     * @return string
     */
    public function getTempDir(){
        return $this->tempDir.'/'.$this->childTempDir;
    }

    /**
     * @return string
     */
    public function getCacheName(){
        return $this->cacheName;
    }

    /**
     * @return bool
     */
    public function isDebugMode(){
        return $this->debugMode;
    }

    /**
     * @return bool
     */
    public function isAutoSave(){
        return $this->autoSave;
    }

    /**
     * @return bool
     */
    public function isTreeStructure(){
        return $this->treeStructure;
    }

    /**
     * @param bool $bool
     */
    public function setTreeStructure($bool = true){
        $this->treeStructure = $bool;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix){
        $this->prefix = $prefix;
    }

    /**
     * @return string|null
     */
    public function getPrefix(){
        return $this->prefix;
    }


    /**
     * @param bool $enable
     */
    public function autoSaveEnable($enable = true){
        $this->autoSave = $enable;
    }

    /**
     * @return bool
     */
    public function isTranslateModal(){
        return $this->translateModal;
    }

    /**
     * @param bool $enable
     */
    public function translateModalEnable($enable = true){
        $this->translateModal = $enable;
    }
    
    
}
