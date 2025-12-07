

<?php

// عرض التاريخ والوقت الحالي
echo date("Y-m-d H:i:s") . "<br><br>";



// إرجاع الوقت الحالي بنظام timestamp
echo time() . "<br><br>";



// إنشاء وقت معيّن باستخدام mktime
echo mktime(10, 30, 0, 12, 4, 2025) . "<br><br>";



// تحويل نص إلى وقت باستخدام strtotime
echo strtotime("next Friday") . "<br>";
echo strtotime("2025-12-30") . "<br><br>";



// استخدام كائن DateTime لعرض الوقت
$date = new DateTime();
echo $date->format("Y-m-d H:i") . "<br><br>";



// إضافة مدة زمنية على تاريخ (مثال: إضافة 3 أيام)
$date2 = new DateTime();
$date2->add(new DateInterval("P3D"));
echo $date2->format("Y-m-d") . "<br><br>";



// حساب الفرق بين تاريخين
$d1 = new DateTime("2025-12-01");
$d2 = new DateTime("2025-12-10");
$diff = $d1->diff($d2);
echo $diff->days . " يوم" . "<br><br>";



// مثال شامل
echo "التاريخ الحالي: " . date("Y-m-d H:i:s") . "<br><br>";

$timestamp = time();
echo "الوقت بنظام timestamp: " . $timestamp . "<br><br>";

$custom = mktime(8, 0, 0, 12, 25, 2025);
echo "تاريخ معيّن: " . date("Y-m-d", $custom) . "<br><br>";

echo "تحويل نص لتاريخ: " . date("Y-m-d", strtotime("next Monday")) . "<br><br>";

$date3 = new DateTime();
$date3->add(new DateInterval("P1W")); // إضافة أسبوع
echo "بعد أسبوع: " . $date3->format("Y-m-d") . "<br><br>";

?>


