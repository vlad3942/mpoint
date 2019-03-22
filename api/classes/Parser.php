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

namespace mPoint\Core {
    /**
     * Class Parser
     * @package mPoint\Core
     */
    class Parser
    {
        /**
         * @var int
         */
        private $_iVariableIndex = -1;

        /**
         * @var array
         */
        private $_aVariable = array();

        /**
         * @var array
         */
        private $_aRule = array();

        /**
         * @var array
         */
        private $_aVariableUsageCount = array();

        /**
         * @var array
         */
        private $_sContext = array();

        /**
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
         * @return bool|string
         */
        public function parse()
        {
            foreach ($this->_aVariable as $name => $values) {
                $index = $values['index'];
                for ($ruleIndex = 0; $ruleIndex <= $this->_iVariableIndex; $ruleIndex++) {
                    $rule = $this->_aRule[$ruleIndex];
                    if (strpos($rule, '<' . $name . '>') !== false) {
                        $this->_aVariableUsageCount[$index]++;
                    }
                }
            }

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
                    case '(' :
                        $stringIndex++;
                        $xmlVariable = $this->_getSubstring($rule, ')', $stringIndex, $nextIndex);

                        if ($rule[$stringIndex] === '<' || $rule[$stringIndex] === '"') {
                            $xmlVariable = $this->_parseInput($xmlVariable);
                        }
                        $stringIndex = $nextIndex;
                        $xmlVariable = str_replace('.', '/', $xmlVariable);
                        $output .= $this->_getValueFromXMLContext($xmlVariable);
                        break;

                    case '<' :
                        $stringIndex++;
                        $xmlVariable = $this->_getSubstring($rule, '>', $stringIndex, $nextIndex);
                        $stringIndex = $nextIndex;
                        $output .= $this->_aVariable[$xmlVariable]['value'];
                        break;

                    case '"' :
                        $stringIndex++;
                        $xmlVariable = $this->_getSubstring($rule, '"', $stringIndex, $nextIndex);
                        $stringIndex = $nextIndex + 1;
                        $output .= $xmlVariable;
                        break;

                    case strpos($currentString, '==') === 0;
                        $stringIndex += 2;
                        $nextIndex = $stringIndex;

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

                    case strpos($currentString, 'AND') === 0;
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

                    case strpos($currentString, 'OR') === 0;
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

                    case '=' :
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

                    case ':' :
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
                    case '{' :
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