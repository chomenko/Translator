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
     * @var Config
     */
    private $config;

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

        if (empty($contents)){
			$this->data = [];
			return;
		}

        $this->data = (array) Neon::decode($contents);
    }



    /**
     * @return int
     */
    public function getFileMTime(){
        return $this->filemtime;
    }

    /**
     * @param array $jumps
     * @param mixed $final_value
     * @param array $level
     * @return array
     */
    private function recursiveSave(array $jumps = array(), $final_value, array $level = array()){
        if(count($jumps) > 0){
            $first_key = $jumps[0]; unset($jumps[0]);

            $next_level = array_key_exists($first_key,  $level) ? $level[$first_key] : array();

            if(!is_array($next_level) && count($jumps) > 0){
                $next_level = array(
                    '_' => $next_level
                );
            }

            $level[$first_key] = is_array($next_level) ? $this->recursiveSave(array_values($jumps), $final_value, $next_level) : $final_value;
            return $level;
        }

        if(is_array($level) && count($level) > 0){
            $level['_'] = $final_value;
            return $level;
        }
        return $final_value;

    }


    /**
     * @param string $key
     * @return bool
     */
    private function isValidKey($key){
        if(strpos($key, " ") !== false){
            return false;
        }
        foreach (explode('.', $key) as $_key){
            if(empty($_key)){
                return false;
            }
        }
        return true;
    }


    /**
     * @param string $name
     * @param array|string $value
     */
    public function saveValue($name, $value){

        if(!$this->isValidKey($name) || !$this->config->isTreeStructure()){
            $this->data[$name] = $value;
        }else{
            $name = $this->setPrefix($name);
            $jumps = explode('.', $name);
            $first_key = $jumps[0];
            unset($jumps[0]);

            $child_data = isset($this->data[$first_key]) ? $this->data[$first_key] : array();
            $this->data[$first_key] = $this->recursiveSave(array_values($jumps), $value, $child_data);
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
        if(!$this->isValidKey($name) || !$this->config->isTreeStructure()){
            if(array_key_exists($name, $this->data)){
                return $this->data[$name];
            }
        }else{
            $name = $this->setPrefix($name);
            $name = explode('.', $name);
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
