<?php

namespace api\classes\merchantservices\MetaData;

/**
 * Property details
 * 
 * 
 * @package    Mechantservices
 * @subpackage Property Detail Class
 * @author     Ijaj Inamdar <ijaj.inamdar@cellpointmobile.com>

 */
class PropertyDetail
{

    /**
     * Property Category
     *
     * @var string
     */
    private string $category;

    /**
     * Property SubCategory
     *
     * @var string
     */
    private string $subCategory;

    /**
     * Property Reference Id
     *
     * @var string
     */
    private string $refernceId;

    /**
     * Property attributes
     *
     * @var array
     */
    private array $properties;

    /**
     * Get property Category
     *
     * @return  string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set property Category
     *
     * @param  string  $category  Property Category
     *
     * @return  self
     */
    public function setCategory(string $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get property Reference Id
     *
     * @return  string
     */
    public function getRefernceId()
    {
        return $this->refernceId;
    }

    /**
     * Set property Reference Id
     *
     * @param  string  $refernceId  Property Reference Id
     *
     * @return  self
     */
    public function setRefernceId(string $refernceId)
    {
        $this->refernceId = $refernceId;

        return $this;
    }

    /**
     * Get property SubCategory
     *
     * @return  string
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * Set property SubCategory
     *
     * @param  string  $subCategory  Property SubCategory
     *
     * @return  self
     */
    public function setSubCategory(string $subCategory)
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    /**
     * Get property attributes
     *
     * @return  array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set property attributes
     *
     * @param  array  $properties  Property attributes
     *
     * @return  self
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
    }


    public function toXml()
    {
        $xml = '';
        $xml .= '<property_detail>';
        $xml .= sprintf("<property_category>%s</property_category>", $this->getCategory());
        $xml .= sprintf("<property_sub_category>%s</property_sub_category>", $this->getSubCategory());
        $xml .= sprintf("<property_reference_id>%s</property_reference_id>", $this->getRefernceId());

        $xml .= '<properties>';
        foreach ($this->getProperties() as $Property) {
            $xml .= $Property->toXml();
        }
        $xml .= '</properties>';
        $xml .= '</property_detail>';

        return $xml;
    }
}
