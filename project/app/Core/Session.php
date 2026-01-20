<?php
namespace App\Core;

class Session {
    public function __construct(){
        if(session_status()===PHP_SESSION_NONE){
            session_start();
        }
    }

    public function create($id,$name){
        session_regenerate_id(true);
        $_SESSION['user_id']=$id;
        $_SESSION['user_name']=$name;
        $_SESSION['logged_in']=true;
    }

    public function destroy(){
        $_SESSION=[];
        if(ini_get("session.use_cookies")){
            $params=session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,$params["path"],$params["domain"],$params["secure"],$params["httponly"]);
        }
        session_destroy();
    }

    public function isLogged(){
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in']===true;
    }

    public function userName(){
        return $_SESSION['user_name'] ?? '';
    }
}
