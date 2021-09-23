<?php

/*
interface IConfig
{

    public function getConfiguration();

    public function getServiceType();

    public function getProperties();

}
*/

abstract class IConfig
{

    public $type;    

    abstract public function getConfiguration();

    abstract public function getServiceType();

    abstract public function getProperties();
    
}