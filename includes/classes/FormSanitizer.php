<?php
class FormSanitizer{

    public static function sanitizeFromString($intutText){
        $intutText = strip_tags($intutText);
        $intutText =str_replace("","",$intutText);
        //$intutText = trim($intutText);
        $intutText = strtolower($intutText);
        $intutText = ucfirst($intutText);
        return $intutText;
    }

    public static function sanitizeFromUsername($intutText){
        $intutText = strip_tags($intutText);
        $intutText =str_replace(" ","",$intutText);
        
        return $intutText;
    }

    public static function sanitizeFromPassword($intutText){
        $intutText = strip_tags($intutText);
        
        return $intutText;
    }

    public static function sanitizeFromEmail($intutText){
        $intutText = strip_tags($intutText);
        $intutText =str_replace(" ","",$intutText);
        
        return $intutText;
    }
}

$f

?>