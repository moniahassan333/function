
<?php

echo "<pre>";
print_r($_SERVER);
echo "</pre>";



echo "نوع الطلب: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "عنوان الصفحة: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "IP المستخدم: " . $_SERVER['REMOTE_ADDR'] . "<br>";
echo "اسم السكربت: " . $_SERVER['SCRIPT_NAME'] . "<br>";




?>
