<?php
function utf8Encode($string) {
    //this replaces the deprecated utf8Encode function
    return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    //return mb_convert_encoding($string, 'UTF-8', mb_list_encodings()); //though documentation tells, that it anticipates the encoding well, it really doesn't work! use above only!
}
?>