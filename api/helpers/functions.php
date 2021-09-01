<?php
if (function_exists('xml_encode') === false) {

    function xml_encode($mixed, $DOMDocument = null, $DOMEelement = null, $useClassRef = false)
    {

            if (is_null($DOMDocument)) {
                $DOMDocument =new DOMDocument();
                $DOMDocument->formatOutput = false;
                xml_encode($mixed, $DOMDocument, $DOMDocument, true);
                return preg_replace("/\r|\n/", '', $DOMDocument->saveHTML());
            }
            else{
                if(is_object($mixed))
                {
                    $element = $DOMEelement;
                    if ($useClassRef === true) {

                        $className = get_class($mixed);
                        // get xml node name for a class
                        $annotation = getClassAnnotations($className);
                        foreach ($annotation as $item) {
                            if (strpos($item, 'xmlName') !== false) {
                                $index = strpos($item, 'xmlName') + strlen("xmlName") + 1;
                                $xmlNodeName = trim(preg_replace('/\s\s+/', ' ', substr($item, $index)));
                            }
                        }
                        $path = explode('\\', $className);
                        $className = array_pop($path);
                        if (empty($xmlNodeName) === true) {
                            $xmlNodeName = $className;
                        }

                        $element = $DOMDocument->createElement($xmlNodeName);
                    }
                    if(method_exists($mixed, 'xmlSerialize')){
                        $mixed = $mixed->xmlSerialize();
                    }
                    else{
                        $mixed = get_object_vars($mixed);
                    }
                    xml_encode($mixed, $DOMDocument, $element);
                    if($useClassRef === true) {
                        $DOMEelement->appendChild($element);
                    }
                }
                elseif (is_array($mixed)) {
                    foreach ($mixed as $index => $mixedElement) {
                        if(is_array($mixedElement) )
                        {
                            $element = $DOMDocument->createElement($index);
                            xml_encode($mixedElement, $DOMDocument, $element);
                            $DOMEelement->appendChild($element);
                        }elseif(is_object($mixedElement)) {
                            if (is_numeric($index) === true) {
                                xml_encode($mixedElement, $DOMDocument, $DOMEelement, true);
                            } else {
                                $element = $DOMDocument->createElement($index);
                                xml_encode($mixedElement, $DOMDocument, $element);
                                $DOMEelement->appendChild($element);
                            }

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

    }
}

function getClassAnnotations($class)
{
    $r = new ReflectionClass($class);
    $doc = $r->getDocComment();
    preg_match_all('#@(.*?)\n#s', $doc, $annotations);
    return $annotations[1];
}
?>