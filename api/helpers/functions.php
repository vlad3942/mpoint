<?php
if (!function_exists('xml_encode')) {

    function xml_encode($mixed, $DOMDocument = null, $DOMEelement = null)
    {
        try {
            if (is_null($DOMDocument)) {
                $DOMDocument =new DOMDocument();
                $DOMDocument->formatOutput = true;
                xml_encode($mixed, $DOMDocument, $DOMDocument);
                echo $DOMDocument->saveXML();
            }
            else{
                if(is_object($mixed))
                {
                    $className= get_class($mixed);
                    $path = explode('\\', $className);
                    $className = array_pop($path);
                    $element = $DOMDocument->createElement($className);
                    $DOMEelement->appendChild($element);
                    if(method_exists($mixed, 'xmlSerialize')){
                        $mixed = $mixed->xmlSerialize();
                    }
                    else{
                        $mixed = get_object_vars($mixed);
                    }
                    xml_encode($mixed, $DOMDocument, $element);
                }
                elseif (is_array($mixed)) {
                    foreach ($mixed as $index => $mixedElement) {
                        if(is_array($mixedElement))
                        {
                            $element = $DOMDocument->createElement($index);
                            xml_encode($mixedElement, $DOMDocument, $element);
                            $DOMEelement->appendChild($element);
                        }elseif(is_object($mixedElement)) {
                            xml_encode($mixedElement, $DOMDocument, $DOMEelement);
                        }
                        else
                        {
                            $mixedElement = (string)$mixedElement;
                            $element = $DOMDocument->createElement($index, $mixedElement);
                            $DOMEelement->appendChild($element);
                        }
                    }
                }
            }

        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }

    }
}
?>