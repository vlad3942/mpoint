<?php

namespace api\classes\merchantservices\commons;

class BaseInfo
{
    private int $Id;
    private string $sName;

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->Id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->sName;
    }

    /**
     * @param string $sName
     * @return BaseInfo
     */
    protected function setName(string $sName): BaseInfo
    {
        $this->sName = $sName;
        return $this;
    }

    /**
     * @param int $Id
     * @return BaseInfo
     */
    protected function setId(int $Id): BaseInfo
    {
        $this->Id = $Id;
        return $this;
    }

    protected function toXML()
    {
        $xml = "<id>".$this->getId()."</id>";
        $xml .= "<name>".$this->getName()."</name>";
        return $xml;
    }


}