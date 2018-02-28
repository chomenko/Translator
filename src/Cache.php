<?php

/**
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */


namespace Chomenko\Translator;


use Nette\Caching;
use Nette\Caching\Storages\FileStorage;

class Cache extends Caching\Cache{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    protected $cache_data;

    /**
     * @param Config $config
     */
    public function __construct(Config $config) {

        if(!is_dir($config->getTempDir())){
            mkdir($config->getTempDir());
        }

        $storage = new FileStorage($config->getTempDir());
        $this->config = $config;
        parent::__construct($storage);
        $this->cache_data = $this->load($this->config->getCacheName());
        if($this->cache_data === null){
            $this->save($this->config->getCacheName(), array());
            $this->cache_data = array();
        }
    }
    
    /**
     * @param string $lang
     * @return NULL|LocalObject
     */
    public function loadFile($lang) {
        if(isset($this->cache_data[$lang])){
            return $this->cache_data[$lang];
        }
        return null;
    }



    public function getByName($name){

    }


    /**
     * @return array
     */
    public function getData(){
        return $this->cache_data;
    }

    /**
     * @param LocalObject $data
     */
    public function saveFile(LocalObject $data){
        $this->cache_data[$data->lang] = $data; 
        $this->save($this->settings->cache_name, $this->cache_data);
    }

}
