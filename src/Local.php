<?php

/**
 * @author Mykola Chomenko <mykola.chomenko@dipcom.cz>
 */
namespace Chomenko\Translator;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use SplFileObject;

class Local{

    /**
     * @var integer 
     */
    private $filemtime = null;

    /**
     * @var SplFileObject
     */
    private $file;

    /**
     * @var string 
     */
    private $lang = null;

    /**
     * @var array
     */
    private $data = array();

    /**
     * @var bool
     */
    private $shutdown = false;

    /**
     * Local constructor.
     * @param string $lang
     * @param SplFileObject $file
     * @param Config $file
     */
    public function __construct($lang, SplFileObject $file, Config $config) {
        $this->lang = $lang;
        $this->file = $file;
        $this->config = $config;
        $this->filemtime = $file->getATime();
        $contents = $file->getSize() > 0 ? $file->fread($file->getSize()): "";
        $this->data = Neon::decode($contents);
    }



    /**
     * @return int
     */
    public function getFileMTime(){
        return $this->filemtime;
    }


    private function setRecursive($data, $value, array $keys, $key_index = 0){
        $keys = array_values($keys);
        $key = $keys[$key_index];
        if($key_index < count($keys)-1) {

            if(is_array($data) && !array_key_exists($key, $data)){
                $data[$key] = array();
            }if(is_array($data) && array_key_exists($key, $data)){
                if(is_string($data[$key]) ){
                    $data[$key] = array(
                        '_' => $data[$key]
                    );
                }
            }elseif(is_string($data) ){
                $data = array(
                    '_' => $data,
                    $key => array()
                );
            }
            $data[$key] = $this->setRecursive($data[$key], $value, $keys, $key_index + 1);
        }else{
            if(is_array($data) && !array_key_exists($key, $data)){
                $data[$key] = $value;
            }elseif(is_string($data) && is_array($value)){
                if(!array_key_exists('_', $value)){
                    $value['_'] = $data;
                }
                $data = array($key => $value);
            }elseif(is_array($data) && array_key_exists($key, $data)){
                if(is_array($data[$key])){
                    $data[$key] = array('_' => $value) + $data[$key];
                }else{
                    $data[$key] = $value;
                }
            }else{
                $data = $value;
            }
        }
        return $data;
    }


    /**
     * @param string $name
     * @param array|string $value
     */
    public function saveValue($name, $value){

        $name = $this->setPrefix($name);

        if (strpos($name, " ") == false){
            $name = explode('.', $name);
        }
        if(is_array($name)){
            $this->data = $this->setRecursive($this->data,$value, $name);
        }else{
            if(array_key_exists($name, $this->data)){
                $this->data[$name] = $value;
            }
        }

        if(!$this->shutdown){
            if(!is_writable($this->file->getRealPath())){
                throw new \Exception("File {$this->file->getRealPath()} is not writable. Check Permission.");
            }
            $this->shutdown = true;
            register_shutdown_function(array($this, 'save'));
        }
    }


    /**
     * @param $name
     * @param null $default
     * @param bool $return_array
     * @return string|array|null
     */
    public function getValue($name, $default = null, $return_array = false){

        $name = $this->setPrefix($name);

        if (strpos($name, " ") == false){
            $name = explode('.', $name);
        }

        if(is_array($name)){
            $last = $this->data;
            foreach ($name as $n){
                if(is_array($last) && array_key_exists($n, $last)){
                    $last = $last[$n];
                }else{
                    return $default;
                }
            }
            if(is_array($last) && $return_array === false){
                if(array_key_exists('_', $last)){
                    return $last['_'];
                }
                return $default;
            }
            return $last;
        }else{
            if(array_key_exists($name, $this->data)){
                return $this->data[$name];
            }
        }
        return $default;
    }

    /**
     * @param string $name
     * @return string
     */
    private function setPrefix($name){
        $ignore_prefix = false;
        if(substr($name, 0, 1) == "."){
            $name = substr($name, 1, strlen($name));
            $ignore_prefix = true;
        }

        if(($prefix = $this->config->getPrefix()) && !$ignore_prefix){
            $name = $prefix . '.' . $name;
        };
        return $name;
    }


    public function save(){
        $content = Neon::encode($this->data, Encoder::BLOCK);
        file_put_contents($this->file->getRealPath(), $content);
    }


    public function __sleep(){
        $this->shutdown = false;
    }


}
