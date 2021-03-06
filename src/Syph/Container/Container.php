<?php
namespace Syph\Container;
use Syph\Container\Interfaces\ContainerInterface;
use Syph\DependencyInjection\ServiceInterface;

/**
 * Created by PhpStorm.
 * User: Bruno
 * Date: 26/08/2015
 * Time: 14:47
 */
class Container implements ContainerInterface
{
    public $service = array();

    public function __construct($kernel)
    {
        $this->service['kernel'] = $kernel;
    }

    public function startContainer(array $mainServices = array()){
        $this->service['container'] = $this;
        $this->load($mainServices);
    }

    public function get($name)
    {
        return $this->service[$name];
    }

    public function has($name){
        return array_key_exists($name,$this->service);
    }

    public function set(ServiceInterface $service)
    {
        $this->service[$service->getName()] = $service;
    }

    public function load($services,$nameFather = null)
    {

        foreach($services as $name => $service){
            $args = array();
            if(!$this->has($name)){
                if(isset($service['args']) && count($service['args']) > 0){
                    foreach ($service['args'] as $argName=>$arg) {
                        $this->load(array($argName=>$arg),$name);
                        if($this->has($argName)){

                            $args[$name][] = $this->get($argName);
                        }

                    }
                }
                $reflect = new \ReflectionClass($service['class']);
                $serviceInstance = $reflect->newInstanceArgs((array_key_exists($name,$args))?$args[$name]:array());
                $this->set($serviceInstance);
            }else{
                $args[$nameFather][] = $this->get($name);
            }
        }
    }
}