<?php

namespace api\classes\merchantservices\Helpers;

use api\classes\merchantservices\commons\BaseInfo;

class Helpers {

    /**
     * @param $aCatPropertyInfo
     *
     * @return string
     */
    public static function getPropertiesXML($aCatPropertyInfo) : string  {

        $xml = "<property_details>";
        foreach ($aCatPropertyInfo as $category => $aPropertyInfo)
        {
            $xml .= "<property_detail>";
            $xml .= "<property_sub_category>".$category."</property_sub_category>";
            $xml .= "<properties>";

            foreach ($aPropertyInfo as $propertyInfo) $xml .= $propertyInfo->toXML();
            $xml .= "</properties>";
            $xml .= "</property_detail>";
        }
        $xml .= "</property_details>";
        return $xml;
    }

    /**
     * Return XML based on Input objetData array
     * @param $objectData
     *
     * @return string
     */
    public static function generateXML(array $objectData): string {
        $XML = '';
        foreach ($objectData as $key => $metadata) {
            if(is_array($metadata) === false) {                
                $XML .= (is_object($metadata)) ? $metadata->toXML() : '';
            }
            else {
                if(empty($metadata) === true) continue; // Skip Empty Node
                $XML .= "<{$key}>";
                foreach ($metadata as $data) {
                    $sXmlSection = (is_object($data)) ? $data->toXML() : '';
                    if (isset($data->additionalProp)) {
                        $sXmlSection .= self::generateXML($data->additionalProp);
                    }                    
                    if ($data instanceof BaseInfo && $data->hasRootNode())
                    {
                        $sRootNode = $data->getRootNode();
                        $sXmlSection = sprintf("<{$sRootNode}>%s</{$sRootNode}>",$sXmlSection);
                    }

                    $XML .= $sXmlSection;
                }
                $XML .= "</{$key}>";
            }
        }
        return $XML;
    }

}