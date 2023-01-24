<?php
function convertTimeFormatInString($string) {
//This function first uses regular expressions to match the time in the string, if it finds a match, it converts the time to the format you specified, then it replaces the original time with the converted time in the string and return the modified string. If the function does not find any time in the string, it will return the original string.

    preg_match('/([01][0-9]|2[0-3]):[0-5][0-9]/', $string, $matches);
    if (count($matches) > 0) {
        $time = $matches[0];
        $time_parts = explode(':', $time);
        $hours = intval($time_parts[0]);
        $minutes = intval($time_parts[1]);
        $suffix = 'AM';
        if ($hours >= 12) {
            $suffix = 'PM';
            $hours = $hours % 12;
        }
        if ($hours == 0) {
            $hours = 12;
        }
        $converted_time = sprintf('%d:%02d %s', $hours, $minutes, $suffix);
        return preg_replace("/$time/", $converted_time, $string);
    }
    else {
        return $string;
    }
}
?>