<?php
/**
 * Check if the date is in the correct format
 * @param string $date date in yyyy-MM-dd format
 * @return bool return true if the date is in correct format
 */
function checkStrDate($date) {
    if (!is_string($date)) {return false;}
    return preg_match("/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/", $date);
}