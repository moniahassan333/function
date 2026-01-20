<?php
namespace app\Models;

use PDO;

class User {
    private $db;
    private $table = "users";

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function create($name,$email,$phone,$password){
        $stmt=$this->db->prepare("INSERT INTO {$this->table} (name,email,phone,password) VALUES (?,?,?,?)");
        $stmt->execute([$name,$email,$phone,$password]);
    }

    public function getByEmail($email){
        $stmt=$this->db->prepare("SELECT * FROM {$this->table} WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email){
        $stmt=$this->db->prepare("SELECT id FROM {$this->table} WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch() ? true : false;
    }
}
