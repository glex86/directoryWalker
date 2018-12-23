<?php

namespace directoryWalker;

/**
 * Include + Exclude ruleset based matcher with regexp and bogus glob
 */
class selector {

    public static $delimiter = '~';
    private $includesArray = [];
    private $excludesArray = [];
    private $includesRegexp = [];
    private $excludesRegexp = [];


    /**
     * Create selector class
     * @param array $includes Patterns for matching includes
     * @param array $excludes Patterns for matching excludes
     */
    public function __construct($includes = false, $excludes = false) {
        $this->addIncludes($includes);
        $this->addExcludes($excludes);
    }


    /**
     * Validates the given patterns
     * @param Array $patterns Patterns to be validated
     * @return boolean|string Returns with a string containing the error message or with ture
     */
    public function isValid($patterns) {
        if (is_string($patterns) && strlen($patterns)) {
            $patterns = [$patterns];
        }

        if (!is_array($patterns)) {
            return 'The expression needs to be an array or string';
        }

        foreach ($patterns as $value) {
            if ($this->prepare($value, false) === false) {
                return "Invalid expression: '{$value}'";
            }
        }
        return true;
    }


    /**
     * Convert bogus glob patterns to regexp and validates regexp syntax
     * @param string $value Filter pattern
     * @param boolean $throwException Return false or throw exception when syntax error detected
     * @return boolean|string The prepared pattern or false when error occured
     */
    private function prepare($value, $throwException = true) {
        if (substr($value, 0, 1) == self::$delimiter && substr($value, -1) == self::$delimiter) {
            //Regular expression
            $value = substr($value, 1, -1);
        }
        else {
            //Wildcard expression
            $value = preg_quote($value, self::$delimiter);
            $value = '^'.str_replace(array("\*", "\?", "#"), array(".*", ".", "[0-9]"), $value).'$';
        }

        //Check expression
        if (@preg_match(self::$delimiter.$value.self::$delimiter, null) === false) {
            if ($throwException) {
                throw new \Exception("[SELECTOR] Invalid regular expression: ({$value})");
            }
            else {
                return false;
            }
        }

        return "({$value})";
    }

    /**
     * Add Exclude ruleset
     * @param mixed $additional Rule/Ruleset needs to be added
     * @return boolean
     */
    public function addExcludes($additional) {
        if (is_string($additional) && strlen($additional)) {
            $additional = array($additional);
        }

        if (!is_array($additional) || !count($additional)) {
            return false;
        }

        foreach ($additional as $value) {
            $this->excludesArray[]  = $value;
            $this->excludesRegexp[] = $this->prepare($value);
        }
    }

    /**
     * Add Include ruleset
     * @param mixed $additional Rule/Ruleset needs to be added
     * @return boolean
     */
    public function addIncludes($additional) {
        if (is_string($additional) && strlen($additional)) {
            $additional = array($additional);
        }

        if (!is_array($additional) || !count($additional)) {
            return false;
        }

        foreach ($additional as $value) {
            $this->includesArray[]  = $value;
            $this->includesRegexp[] = $this->prepare($value);
        }
    }


    /**
     * Get Includes ruleset as string
     * @param string $separator Item separator
     * @return string
     */
    public function getIncludesStr($separator = "n") {
        if (!count($this->includesArray)) {
            return 'All';
        }

        return implode($separator, $this->includesArray);
    }


    /**
     * Get Excludes ruleset as string
     * @param string $separator Item separator
     * @return string
     */
    public function getExcludesStr($separator = "n") {
        if (!count($this->excludesArray)) {
            return 'None';
        }

        return implode($separator, $this->excludesArray);
    }


    /**
     * Apply excludes filter to the $item
     * @param string $item
     * @return boolean
     */
    private function checkExcludes($item) {
        if (!count($this->excludesArray)) {
            return true;
        }

        return !preg_match(self::$delimiter.implode('|', $this->excludesRegexp).self::$delimiter.'is', $item);
    }


    /**
     * Apply includes filter to the $item
     * @param string $item
     * @return boolean
     */
    private function checkIncludes($item) {
        if (!count($this->includesArray)) {
            return true;
        }

        return preg_match(self::$delimiter.implode('|', $this->includesRegexp).self::$delimiter.'is', $item);
    }


    /**
     * Apply Include/Exclude filters to the $item
     * @param string $item
     * @return boolean
     */
    public function isGood($item) {
        return $this->checkExcludes($item) && $this->checkIncludes($item);
    }
}
