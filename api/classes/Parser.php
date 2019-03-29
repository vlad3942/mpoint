<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint
 * Package:
 * File Name:Parser.php
 */

/* Examples
 * Context -  <?xml version="1.0" encoding="utf-8"?><root><authorize client-id="10007" account="100007"><psp-config id="21" type="1"><name>GlobalCollect</name><merchant-account>337</merchant-account><merchant-sub-account>-1</merchant-sub-account><username>d05f1d86bf1611f7</username><password>t91nuhCNWxVgRJNQK21CXRNAueGjkQT4zuiGPKF/opo=</password><messages></messages><additional-config></additional-config></psp-config><transaction id="1844619" type="30" gmid="-1" mode="0" eua-id="52365" attempt="0" psp-id="21" card-id="8" wallet-id="0" product-type="100" external-id=""><captured-amount country-id="604" currency="KWD" symbol="" format="" alpha2code="KW" alpha3code="KWT" code="414">0</captured-amount><fee country-id="604" currency="KWD" symbol="" format="">0</fee><price /><points country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</points><reward country-id="0" currency="points" symbol="points" format="{PRICE} {CURRENCY}">-1</reward><refund country-id="604" currency="KWD" symbol="" format="">0</refund><orderid>NYSI0O</orderid><mobile country-id="200" country-code="965">9876543210</mobile><operator>20000</operator><email>sagar@cellpointmobile.com</email><device-id /><logo><url>https://hpp-dev2.cellpointmobile.com/css/img/logo.jpg</url><width>100%</width><height>20%</height></logo><css-url>https://s3-ap-southeast-1.amazonaws.com/cpmassets/marchant/mpl/style.css</css-url><accept-url>http://dev2.cellpointmobile.com:8989/booking-confirmation</accept-url><cancel-url>http://dev2.cellpointmobile.com:8989/booking-confirmation</cancel-url><decline-url /><callback-url>https://webhook.site/f4f16c2f-9a98-4241-98ba-5fcc16092231</callback-url><icon-url /><auth-url>http://localhost:10081/mprofile/login</auth-url><language>us</language><auto-capture>false</auto-capture><auto-store-card>false</auto-store-card><markup-language>html5</markup-language><customer-ref /><description /><ip>::1</ip><hmac>df1c34c3ce2b5617a8f3eb7ed1f3145b55fc5e68</hmac><created-date>20190304</created-date><created-time>194856</created-time><authorized-amount country-id="604" currency-id="414" currency="KWD" decimals="3" symbol="" format="" alpha2code="KW" alpha3code="KWT" code="414">100</authorized-amount></transaction><order-attempt>1</order-attempt><card type-id="8"><card-holder-name>GC Test</card-holder-name><card-number>4567350000427977</card-number><expiry-month>12</expiry-month><expiry-year>2020</expiry-year><cvc>123</cvc></card><address country-id="640" alpha2code="PH" alpha3code="PHL" code="608"><full-name>First Last</full-name><company>Cellpoint Mobile</company><street>Place Street 2</street><postal-code>23456789</postal-code><city>Town City</city><state>Place State</state></address></authorize></root>
 * Rules
 *  XPATH - orderid ::= (transaction.orderid)
 *          attempt ::= (@attempt)
 *  Variables - invoiceid ::= <orderid>"CPM"<transactionid><attempt>
 *              orderid ::= (transaction.orderid)
 *              transactionid ::= (transaction.@id)
 *              attempt ::= (@attempt)
 *  Constant - storefront ::=  "NMA"
 *  Conditions - invoiceid ::=  <storefront>(orderid)(@attempt)
 *               storefront ::=  (markup-language)=="html5"="web":"app"
 *  Function - timestamp ::= {date.dmY.`timestamp`}  - '`' is placeholder
 *  Variable In XPath And Fuction - invoiceid ::=  (orderid)(<attempt>){date."dmY"}{memory_get_usage}
 *                                  attempt ::= "@attempt"
 *
 */


namespace mPoint\Core {
    /**
     * Class Parser
     * @package mPoint\Core
     */
    class Parser
    {
        /**
         * @var int
         *
         * _iVariableIndex is index which contains the count of rules
         *
         */
        private $_iVariableIndex = -1;

        /**
         * @var array
         * It contains the actual index for rule and values of rules
         */
        private $_aVariable = array();

        /**
         * @var array
         *
         * It contain the all rules
         *
         */
        private $_aRule = array();

        /**
         * @var array
         */
        private $_aVariableUsageCount = array();

        /**
         * @var array
         *
         * It contains the context on which rule will execute
         * A Context is a XML string
         * This class support multiple context
         */
        private $_sContext = array();

        /**
         * This function will set the a rule
         *
         * @param $name
         * @param $rule
         */
        public function setRule($name, $rule)
        {
            $this->_iVariableIndex++;
            $name = trim($name);
            $rule = trim($rule);
            $this->_aVariable[$name] = array('index' => $this->_iVariableIndex, 'value' => '');
            $this->_aRule[$this->_iVariableIndex] = $rule;
            $this->_aVariableUsageCount[$this->_iVariableIndex] = 0;
        }

        /**
         * This will set multiple rule at a time
         *
         * @param $ruleString
         */
        public function setRules($ruleString)
        {
            $rules = explode("\n", $ruleString);
            foreach ($rules as $rawRule) {
                $rule = explode('::=', $rawRule);
                if (count($rule) === 2) {
                    $this->_iVariableIndex++;
                    $ruleName = trim($rule[0]);
                    $this->_aVariable[$ruleName] = array('index' => $this->_iVariableIndex, 'value' => '');
                    $this->_aRule[$this->_iVariableIndex] = trim($rule[1]);
                    $this->_aVariableUsageCount[$this->_iVariableIndex] = 0;
                }
            }
        }

        /**
         * This function will return all rules added in class
         *
         * @return array
         */
        public function getRules()
        {
            $returnRules = array();
            foreach ($this->_aVariable as $name => $values) {
                $rule = $this->_aRule[$values['index']];
                $returnRules[$name] = $rule;
            }
            return $returnRules;
        }

        /**
         * This function will add context to class
         * @param $context
         */
        public function setContext($context)
        {
            $contextXMLElement = @simplexml_load_string($context);
            if ($contextXMLElement) {
                array_push($this->_sContext, $contextXMLElement);
            }
        }

        /**
         * This function will execute the all rules
         *
         * @return bool|string
         */
        public function parse()
        {
            //This loop will calculate the usage of each rule/variable
            foreach ($this->_aVariable as $name => $values) {
                $index = $values['index'];
                for ($ruleIndex = 0; $ruleIndex <= $this->_iVariableIndex; $ruleIndex++) {
                    $rule = $this->_aRule[$ruleIndex];
                    if (strpos($rule, '<' . $name . '>') !== false) {
                        $this->_aVariableUsageCount[$index]++;
                    }
                }
            }

            //This will sort the array, so that high usage count rule will execute first
            arsort($this->_aVariableUsageCount);
            end($this->_aVariableUsageCount);

            $lastElementOfArray = key($this->_aVariableUsageCount);
            $parseOutput = '';

            foreach ($this->_aVariableUsageCount as $index => $count) {
                $rule = $this->_aRule[$index];
                $output = $this->_parseInput($rule);
                foreach ($this->_aVariable as $name => $values) {
                    $variableIndex = $values['index'];
                    if ($lastElementOfArray === $variableIndex) {
                        $parseOutput = $output;
                    }
                    if ($index === $variableIndex) {
                        $this->_aVariable[$name]['value'] = $output;
                        break;
                    }
                }
            }
            return $parseOutput;
        }

        /**
         * This function will parse the rule
         * @param $rule
         * @return bool|string
         */
        private function _parseInput($rule)
        {
            $output = '';
            $ruleLength = strlen($rule);
            for ($stringIndex = 0; $ruleLength !== $stringIndex;) {
                $currentChar = $rule[$stringIndex];
                $currentString = substr($rule, $stringIndex);
                switch ($currentChar) {
                    case '(' :  // The string in ( and ) will be XPath and it will apply on XML context
                        $stringIndex++;
                        $xmlVariable = $this->_getSubstring($rule, ')', $stringIndex, $nextIndex);

                        if ($rule[$stringIndex] === '<' || $rule[$stringIndex] === '"') {
                            $xmlVariable = $this->_parseInput($xmlVariable);
                        }
                        $stringIndex = $nextIndex;
                        $xmlVariable = str_replace('.', '/', $xmlVariable);
                        $output .= $this->_getValueFromXMLContext($xmlVariable);
                        break;

                    case '<' : // The string in < and > will variable, it is already define in other rule
                        $stringIndex++;
                        $xmlVariable = $this->_getSubstring($rule, '>', $stringIndex, $nextIndex);
                        $stringIndex = $nextIndex;
                        $output .= $this->_aVariable[$xmlVariable]['value'];
                        break;

                    case '"' : // The string in " will be a constant and will be used as it is
                        $stringIndex++;
                        $xmlVariable = $this->_getSubstring($rule, '"', $stringIndex, $nextIndex);
                        $stringIndex = $nextIndex + 1;
                        $output .= $xmlVariable;
                        break;

                    case strpos($currentString, '==') === 0; // == Operator will compare LHS with RHL
                        $stringIndex += 2;
                        $nextIndex = $stringIndex;

                        // This will create a substring which need to compare
                        $xmlVariable = $this->_getSubstring($rule, array('=', 'AND', 'OR'), $stringIndex, $nextIndex);
                        if ($rule[$stringIndex] === '<' || $rule[$stringIndex] === '"' || $rule[$stringIndex] === '(') {
                            $xmlVariable = $this->_parseInput($xmlVariable);
                        }
                        if ($output === $xmlVariable) {
                            $output = true;
                        } else {
                            $output = false;
                        }
                        $stringIndex = $nextIndex;
                        break;

                    case strpos($currentString, 'AND') === 0; // AND is used to check all conditions are true or not
                        $stringIndex += 3;
                        $nextIndex = $stringIndex;

                        $xmlVariable = $this->_getSubstring($rule, array('=', 'AND', 'OR'), $stringIndex, $nextIndex);

                        if ($rule[$stringIndex] === '<' || $rule[$stringIndex] === '"' || $rule[$stringIndex] === '(') {
                            $xmlVariable = $this->_parseInput($xmlVariable);
                        }

                        if ($output && $xmlVariable) {
                            $output = true;
                        } else {
                            $output = false;
                        }
                        $stringIndex = $nextIndex;
                        break;

                    case strpos($currentString, 'OR') === 0; // AND is used to check any conditions is true
                        $stringIndex += 2;
                        $nextIndex = $stringIndex;

                        $xmlVariable = $this->_getSubstring($rule, array('=', 'AND', 'OR'), $stringIndex, $nextIndex);

                        if ($rule[$stringIndex] === '<' || $rule[$stringIndex] === '"' || $rule[$stringIndex] === '(') {
                            $xmlVariable = $this->_parseInput($xmlVariable);
                        }

                        if ($output || $xmlVariable) {
                            $output = true;
                        } else {
                            $output = false;
                        }
                        $stringIndex = $nextIndex;
                        break;

                    case '=' : // if '==', 'AND' or 'OR' is true the it will assign the value next to '='
                        $stringIndex++;
                        if ($output === true) {
                            if ($rule[$stringIndex] === '<' || $rule[$stringIndex] === '"' || $rule[$stringIndex] === '(') {
                                $newRule = substr($rule, $stringIndex);
                                $xmlVariable = $this->_parseInput($newRule);
                                $nextIndex = strlen($newRule);
                            }
                            $output = $xmlVariable;
                            $stringIndex += $nextIndex;
                        } else {
                            $xmlVariable = $this->_getSubstring($rule, array(':', 'AND', 'OR'), $stringIndex, $nextIndex);
                            $stringIndex = $nextIndex;
                        }
                        break;

                    case ':' :// if '==', 'AND' or 'OR' is false the it will assign the value next to ':'
                        $stringIndex++;
                        if ($output === false) {
                            if ($rule[$stringIndex] === '<' || $rule[$stringIndex] === '"' || $rule[$stringIndex] === '(') {
                                $newRule = substr($rule, $stringIndex);
                                $xmlVariable = $this->_parseInput($newRule);
                                $nextIndex = strlen($newRule);
                            }
                            $output = $xmlVariable;
                            $stringIndex += $nextIndex;
                        } else {
                            $xmlVariable = $this->_getSubstring($rule, array('AND', 'OR'), $stringIndex, $nextIndex);
                            $stringIndex = $nextIndex;
                        }
                        break;
                    case '{' : // string between '{' and '}' considered as system or class function
                        //eg . {date.dmY} here date is a function and dmY is param
                        $stringIndex++;
                        $xmlVariable = $this->_getSubstring($rule, '}', $stringIndex, $nextIndex);
                        $stringIndex = $nextIndex;

                        $functionArg = explode('.', $xmlVariable);
                        $functionName = array_shift($functionArg);


                        if (count($functionArg) > 0) {
                            for ($functionArgIndex = 0, $functionArgMaxIndex = count($functionArg); $functionArgIndex < $functionArgMaxIndex; $functionArgIndex++) {

                                if ($functionArg[$functionArgIndex][0] === '<' || $functionArg[$functionArgIndex][0] === '"' || $functionArg[$functionArgIndex][0] === '(') {
                                    $functionArg[$functionArgIndex] = $this->_parseInput($functionArg[$functionArgIndex]);
                                }
                            }
                            $output .= call_user_func_array($functionName, $functionArg);
                        } else {
                            $output .= $functionName();
                        }
                    default:
                        $stringIndex++;
                }
            }
            return $output;
        }

        /**
         * This is used to create a substring
         * @param $subject
         * @param $needle
         * @param $startIndex
         * @param $nextIndex
         * @return bool|string
         */
        private function _getSubstring($subject, $needle, $startIndex, &$nextIndex)
        {
            $nextIndex = 0;
            if (is_array($needle) === true) {
                $input = substr($subject, $startIndex);
                $pattern = '/' . implode('|', array_map(function ($str) {
                        $pregQuote = preg_quote($str, '/');
                        if ($str === '=') {
                            $pregQuote = '(?<!=)' . $pregQuote . '(?!=)';
                        }
                        return $pregQuote;
                    }
                        , $needle)) . '/';
                if (preg_match($pattern, $input, $matches, PREG_OFFSET_CAPTURE)) {
                    $nextIndex = $startIndex + $matches[0][1];
                } else {
                    $nextIndex = strlen($subject);
                }
            } else {
                $nextIndex = strpos($subject, $needle, $startIndex);
            }

            $substring = substr($subject, $startIndex, $nextIndex - $startIndex);
            return $substring;
        }

        /**
         * This function will execute the XPATH on context
         * @param $xpath
         * @return string
         */
        private function _getValueFromXMLContext($xpath)
        {
            $output = '';
            for ($contextIndex = 0, $contextIndexMax = count($this->_sContext); $contextIndex < $contextIndexMax; $contextIndex++) {
                $xmlElement = null;
                try {
                    $xmlElement = $this->_sContext[$contextIndex]->xpath('//' . $xpath);
                } catch (\Exception $exception) {
                    //Invalid XPath
                }
                if (is_array($xmlElement) && count($xmlElement) > 0) {
                    $output = (string)$xmlElement[0];
                    break;
                }
            }
            return $output;
        }

        /**
         * This will return the value of a rule/variable
         * @param $variableName
         * @return null/string
         */
        public function getValue($variableName)
        {
            if (array_key_exists($variableName, $this->_aVariable)) {
                return $this->_aVariable[$variableName]['value'];
            }
            return null;
        }
    }
}