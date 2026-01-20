<?php
declare(strict_types=1);
$pdo = new PDO("mysql:host=localhost;dbname=employee_system;charset=utf8","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

function redirectWithError(string $msg){ header("Location:index.html?error=".urlencode($msg)); exit;}
function redirectWithSuccess(string $msg){ header("Location:index.html?success=".urlencode($msg)); exit;}
function cleanText($text):string{ if(!preg_match('/^[a-zA-Zا-ي\s]+$/u',$text)) redirectWithError("ممنوع إدخال رموز"); return trim($text);}
function cleanNumber($num):float{ if(!is_numeric($num)||$num<0) redirectWithError("ممنوع إدخال قيم سالبة"); return (float)$num; }

if(isset($_POST['add'])){
    $id=(int)cleanNumber($_POST['id']);
    $name=cleanText($_POST['name']);
    $type=$_POST['type'];
    try{
        if($type==='permanent'){
            $salary=isset($_POST['salary'])?cleanNumber($_POST['salary']):0;
            $stmt=$pdo->prepare("INSERT INTO permanent_employees (id,name,base_salary) VALUES (?,?,?)");
            $stmt->execute([$id,$name,$salary]);
        }elseif($type==='contract'){
            $hours=isset($_POST['hours'])?(int)cleanNumber($_POST['hours']):0;
            $rate=isset($_POST['rate'])?cleanNumber($_POST['rate']):0;
            $stmt=$pdo->prepare("INSERT INTO contract_employees (id,name,hours,rate) VALUES (?,?,?,?)");
            $stmt->execute([$id,$name,$hours,$rate]);
        }elseif($type==='manager'){
            $salary=isset($_POST['salary'])?cleanNumber($_POST['salary']):0;
            $bonus=isset($_POST['bonus'])?cleanNumber($_POST['bonus']):0;
            $stmt=$pdo->prepare("INSERT INTO managers (id,name,base_salary,bonus) VALUES (?,?,?,?)");
            $stmt->execute([$id,$name,$salary,$bonus]);
        }else redirectWithError("نوع الموظف غير صحيح");
        redirectWithSuccess("تمت الإضافة بنجاح");
    }catch(PDOException $e){
        redirectWithError("رقم الموظف موجود مسبقًا");
    }
}

if(isset($_POST['delete_id'])){
    $id=(int)cleanNumber($_POST['delete_id']);
    $type=$_POST['delete_type'];
    $tables=["permanent"=>"permanent_employees","contract"=>"contract_employees","manager"=>"managers"];
    if(!isset($tables[$type])) redirectWithError("نوع غير صحيح");
    $stmt=$pdo->prepare("DELETE FROM {$tables[$type]} WHERE id=?");
    $stmt->execute([$id]);
    redirectWithSuccess("تم الحذف");
}
