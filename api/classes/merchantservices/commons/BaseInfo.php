<?php

namespace api\classes\merchantservices\commons;

/**
 * Common property base class
 *
 *
 * @package    Mechantservices
 * @subpackage Commons
 */

class BaseInfo
{
    /**
     * Document Id
     *
     * @var integer
     */
    private int $Id;

    /**
     * Document name
     *
     * @var string
     */
    private string $sName;

    /**
     * Document root node name
     *
     * @var string
     */
    private string $sRootNode;

    /**
     * Array for Node alias
     *
     * @var array
     */
    private array $aNodeAlias;

    /**
     * Additional Dynamic attributes
     *
     * @var array
     */
    private array $aAddtionalAttr = [];

    /**
     * Constructor function
     */
    public function __construct()
    {
        $this->aNodeAlias['id'] = 'id';
        $this->aNodeAlias['name'] = 'name';
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

    /**
     * Generate XML
     *
     * @return void
     */
    public function toXML()
    {
        $sIdNode = isset($this->aNodeAlias['id'])?$this->aNodeAlias['id']:'id';
        $sNameNode = isset($this->aNodeAlias['name'])?$this->aNodeAlias['name']:'name';        

        $xml = sprintf("<{$sIdNode}>%s</{$sIdNode}>", $this->getId());
        $xml .= sprintf("<{$sNameNode}>%s</{$sNameNode}>", $this->getName());

        foreach($this->getAAddtionalAttr() as $key => $value)
        {
            $xml .= sprintf("<{$key}>%s</{$key}>", $value);
        }

        return $xml;
    }

    /**
     * Get the value of sRootNode
     */
    public function getRootNode()
    {
        return $this->sRootNode;
    }

    /**
     * Set the value of sRootNode
     *
     * @return  self
     */
    protected function setRootNode($sRootNode)
    {
        $this->sRootNode = $sRootNode;

        return $this;
    }

    /**
     * If rootnode is set for the document
     *
     * @return boolean
     */
    public function hasRootNode() : bool
    {
        return isset($this->sRootNode);
    }

    /**
     * Load data from Data (array)
     *
     * @param array $aRS
     * @param string $sRootNode
     * @param array $aNodeAlias
     * @return array
     */
    public static function produceFromDataSet($aRS, $sRootNode = '', $aNodeAlias = []): array
    {
        $aBaseInfoDetails = [];
        $aBasicAttributes = array('ID' => 1, 'NAME' => 1);
        $aAddtionalAttr = [];

        foreach ($aRS as $rs) {            
            $BaseInfo = new BaseInfo();
            $BaseInfo->setId($rs["ID"])
                ->setName($rs["NAME"]);

            $aAddtionalAttr = array_diff_key($rs, $aBasicAttributes);

            if(!empty($aAddtionalAttr))
            {
                $BaseInfo->setAAddtionalAttr(array_change_key_case($aAddtionalAttr, CASE_LOWER));
            }

            if (!empty(trim($sRootNode))) {
                $BaseInfo->setRootNode($sRootNode);
            }
            if (!empty($aNodeAlias) && is_array($aNodeAlias)) {
                $BaseInfo->setNodeAlias($aNodeAlias);
            }
            array_push($aBaseInfoDetails, $BaseInfo);
        }

        return $aBaseInfoDetails;
    }

    /**
     * Get array for Node alias
     *
     * @return  array
     */
    public function getNodeAlias()
    {
        return $this->aNodeAlias;
    }

    /**
     * Set array for Node alias
     *
     * @param  array  $aNodeAlias  Array for Node alias
     *
     * @return  self
     */
    public function setNodeAlias(array $aNodeAlias)
    {
        foreach ($aNodeAlias as $key => $value) {
            $this->aNodeAlias[$key] = $value;
        }

        return $this;
    }

    /**
     * Get additional Dynamic attributes
     *
     * @return  array
     */ 
    public function getAAddtionalAttr()
    {
        return $this->aAddtionalAttr;
    }

    /**
     * Set additional Dynamic attributes
     *
     * @param  array  $aAddtionalAttr  Additional Dynamic attributes
     *
     * @return  self
     */ 
    public function setAAddtionalAttr(array $aAddtionalAttr)
    {
        $this->aAddtionalAttr = $aAddtionalAttr;

        return $this;
    }
}
