<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Provider Detail
   * 
   * 
   * @package    Mechantservices
   * @subpackage Provider Detail Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>

 */
class ProviderDetail
{
    /**
     *  Provider detail id
     *
     * @var int
     */
    private int $id;

    /**
     * Provider detail type id
     *
     * @var integer
     */
    private int $typeId;

    /**
     * Provider detail name
     *
     * @var string
     */
    private string $name;


    /**
     * Get provider detail id
     *
     * @return  int
     */ 
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set provider detail id
     *
     * @param  int  $id  Provider detail id
     *
     * @return  self
     */ 
    public function setId(int $id) : ProviderDetail
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get provider detail type id
     *
     * @return  integer
     */ 
    public function getTypeId() : int
    {
        return $this->typeId;
    }

    /**
     * Set provider detail type id
     *
     * @param  integer  $typeId  Provider detail type id
     *
     * @return  self
     */ 
    public function setTypeId($typeId) : ProviderDetail
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get provider detail name
     *
     * @return  string
     */ 
    public function getName() : string 
    {
        return $this->name;
    }

    /**
     * Set provider detail name
     *
     * @param  string  $name  Provider detail name
     *
     * @return  self
     */ 
    public function setName(string $name) : ProviderDetail
    {
        $this->name = $name;

        return $this;
    }

    public function toXML() : string 
    {
        $xml = '';
        $xml .= '<provider_detail>';
        $xml .= sprintf("<id>%s</id>",$this->getId());
        $xml .= sprintf("<type_id>%s</type_id>",$this->getTypeId());
        $xml .= sprintf("<name>%s</name>",$this->getName());        
        $xml .= '</provider_detail>';

        return $xml;
    }
}
