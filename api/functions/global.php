<?php
/*
 * This package in the list of generic library functions
 * */

/*
* This function is used to overload the getenv() PHP function.
* */
function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    return $value;
}


?>
