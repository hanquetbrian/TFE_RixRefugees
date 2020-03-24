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

/**
 * return a format date to be displayed
 * @param string $date date in yyyy-MM-dd format
 * @return bool
 */
function formatStrDate($date) {
    //TODO add the year at the end only when the year is different
    if (!is_string($date)) {return false;}
    $formatDate = date("w-j-m", strtotime($date));
    $splitFormatDate = explode("-", $formatDate);
    $day="";
    switch ($splitFormatDate[0]) {
        case "0":
            $day = "Dimanche";
            break;
        case "1":
            $day = "Lundi";
            break;
        case "2":
            $day = "Mardi";
            break;
        case "3":
            $day = "Mercredi";
            break;
        case "4":
            $day = "Jeudi";
            break;
        case "5":
            $day = "Vendredi";
            break;
        case "6":
            $day = "Samedi";
            break;
    }
    return $day . " " . $splitFormatDate[1] . "/" . $splitFormatDate[2];
}