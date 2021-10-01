<?php
namespace api\classes\merchantservices\MetaData;

/**
   * Url Info
   * 
   * 
   * @package    Mechantservices
   * @subpackage UrlInfo Class
   * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>

 */
class UrlInfo
{

    /**
     * Url Type Id
     *
     * @var integer
     */
    private int $typeId;

    /**
     * Url Category
     *
     * @var string
     */
    private string $category;

    /**
     * Url name
     *
     * @var string
     */
    private string $name;

    /**
     * Url value
     *
     * @var string
     */
    private string $value;
    

    /**
     * Get url Type Id
     *
     * @return  integer
     */ 
    public function getTypeId() : int
    {
        return $this->typeId;
    }

    /**
     * Set url Type Id
     *
     * @param  integer  $typeId  Url Type Id
     *
     * @return  self
     */ 
    public function setTypeId($typeId) : UrlInfo
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get url Category
     *
     * @return  string
     */ 
    public function getCategory() : string
    {
        return $this->category;
    }

    /**
     * Set url Category
     *
     * @param  string  $category  Url Category
     *
     * @return  self
     */ 
    public function setCategory(string $category) : UrlInfo
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get url name
     *
     * @return  string
     */ 
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set url name
     *
     * @param  string  $name  Url name
     *
     * @return  self
     */ 
    public function setName(string $name) : UrlInfo
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get url value
     *
     * @return  string
     */ 
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * Set url value
     *
     * @param  string  $value  Url value
     *
     * @return  self
     */ 
    public function setValue(string $value) : UrlInfo
    {
        $this->value = $value;

        return $this;
    }


    public function toXml() : string
    {
        $xml = '';
        
        $xml .= '<url>';
        $xml .= sprintf("<type_id>%s</type_id>",$this->getTypeId());
        $xml .= sprintf("<category>%s</category>",$this->getCategory());
        $xml .= sprintf("<name>%s</name>",$this->getName());
        $xml .= sprintf("<value>%s</value>",$this->getValue());
        $xml .= '</url>';

        return $xml;        

    }
}

