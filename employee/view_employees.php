<?php
declare(strict_types=1);
$pdo = new PDO("mysql:host=localhost;dbname=employee_system;charset=utf8","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>عرض الموظفين</title>
<style>
body{font-family:Arial;background:#f3f4f6;padding:20px}
h1,h2{text-align:center}
.table-container{width:90%; margin:20px auto;}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 5px 15px rgba(0,0,0,0.1);}
th,td{padding:10px;border:1px solid #ccc;text-align:center;}
th{background:#667eea;color:white;}
tr:nth-child(even){background:#f9f9f9;}
</style>
</head>
<body>
<h1>📋 قائمة الموظفين</h1>
<?php
$queries=[
    "الموظفون الدائمون"=>"SELECT * FROM permanent_employees",
    "الموظفون بعقد"=>"SELECT * FROM contract_employees",
    "المدراء"=>"SELECT * FROM managers"
];

foreach($queries as $title=>$sql){
    echo "<div class='table-container'><h2>$title</h2>";
    $rows=$pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    if(!$rows){echo "<p style='text-align:center'>لا يوجد بيانات</p></div>"; continue;}
    echo "<table><tr>";
    foreach(array_keys($rows[0]) as $col) echo "<th>$col</th>";
    echo "</tr>";
    foreach($rows as $row){
        echo "<tr>";
        foreach($row as $val) echo "<td>".htmlspecialchars((string)$val)."</td>";
        echo "</tr>";
    }
    echo "</table></div>";
}
?>
</body>
</html>

