<?php
namespace app\Core;

class Validator {
    public static function clean($data){
        return htmlspecialchars(trim($data),ENT_QUOTES,'UTF-8');
    }

    public static function validateName($name){
        // الاسم فقط أحرف ومسافات
        return preg_match("/^[\p{Arabic}a-zA-Z\s]{2,50}$/u",$name);
    }

    public static function validateEmail($email){
        return filter_var($email,FILTER_VALIDATE_EMAIL);
    }

    public static function validatePhone($phone){
        $phone = preg_replace('/[^0-9]/','',$phone);
        return strlen($phone)===9 && in_array(substr($phone,0,2),['70','71','73','77','78']);
    }

    public static function validatePassword($pass){
        return strlen($pass)>=6;
    }
}
