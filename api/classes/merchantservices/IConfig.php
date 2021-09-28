<?php

/*
interface IConfig
{

    public function getConfiguration();

    public function getServiceType();

    public function getProperties();

}
*/
namespace api\classes\merchantservices;


interface IConfig
{

    public function getConfiguration() : array;
    public function getServiceType() : AddonServiceType;
    public function getProperties();
    public function toXML():string;



}