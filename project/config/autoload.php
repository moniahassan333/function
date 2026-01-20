<?php
spl_autoload_register(function($class){
    $class = str_replace("\\","/",$class); // يحول Core\Database → Core/Database
    $file = __DIR__ . "/../app/" . $class . ".php"; // صح المسار
    if(file_exists($file)){
        require_once $file;
    } else {
        die("❌ Class file not found: $file");
    }
});

