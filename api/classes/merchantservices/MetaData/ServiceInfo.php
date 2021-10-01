<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Serivce Info
   * 
   * 
   * @package    Mechantservices
   * @subpackage ServiceInfo Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>

 */
class ServiceInfo
{

    /**
     * Service id
     *
     * @var integer
     */
    private int $id;

    /**
     * Service name
     *
     * @var string
     */
    private string $name;
    
    /**
     * Sub types related to the service
     *
     * @var array
     */
    private array $SubTypes;


    /**
     * Get service id
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set service id
     *
     * @param  integer  $id  Service id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get service name
     *
     * @return  string
     */ 
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set service name
     *
     * @param  string  $name  Service name
     *
     * @return  self
     */ 
    public function setName(string $name) : ServiceInfo
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get sub types related to the service
     *
     * @return  array
     */ 
    public function getSubTypes() 
    {
        return $this->SubTypes;
    }

    /**
     * Set sub types related to the service
     *
     * @param  array  $SubTypes  Sub types related to the service
     *
     * @return  self
     */ 
    public function setSubTypes(array $SubTypes)
    {
        $this->SubTypes = $SubTypes;

        return $this;
    }

    public function toXml() : string
    {
        
        $xml = '';
        $xml .= '<service>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<name>%s</name>",$this->getName());

        $xml .= '<subtypes>';
        foreach($this->getSubTypes() as $Subtype)
        {
            $xml .= $Subtype->toXml();
        }
        $xml .= '</subtypes>';
        $xml .= '</service>';

        return $xml;

    }
}