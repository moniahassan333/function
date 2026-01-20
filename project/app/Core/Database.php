<?php
namespace app\Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;

    public static function connect(){
        if(self::$instance === null){
            try{
                $host = 'localhost';
                $dbname = 'insta';
                $user = 'root';
                $pass = '';
                self::$instance = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e){
                die(json_encode(['success'=>false,'message'=>'Database connection failed: '.$e->getMessage()]));
            }
        }
        return self::$instance;
    }
}


