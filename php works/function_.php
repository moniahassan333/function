

<?php
header('Content-Type: text/html; charset=utf-8');


//---------------------------
//     30 دالة نصوص
//---------------------------

// 1) strlen() - تحسب طول النص
echo strlen("Hello") . "<br><br>";

// 2) str_word_count() - عدد الكلمات
echo str_word_count("Hello world") . "<br><br>";

// 3) strrev() - تعكس النص
echo strrev("Hello") . "<br><br>";

// 4) strpos() - موقع كلمة داخل النص
echo strpos("Hello world","world") . "<br><br>";

// 5) str_replace() - استبدال كلمة
echo str_replace("world","PHP","Hello world") . "<br><br>";

// 6) strtolower() - أحرف صغيرة
echo strtolower("HELLO") . "<br><br>";

// 7) strtoupper() - أحرف كبيرة
echo strtoupper("hello") . "<br><br>";

// 8) ucfirst() - أول حرف كبير
echo ucfirst("hello world") . "<br><br>";

// 9) ucwords() - أول حرف من كل كلمة كبير
echo ucwords("hello world") . "<br><br>";

// 10) trim() - إزالة الفراغات
echo trim("  hello  ") . "<br><br>";

// 11) ltrim() - إزالة فراغ اليسار
echo ltrim("  hello") . "<br><br>";

// 12) rtrim() - إزالة فراغ اليمين
echo rtrim("hello  ") . "<br><br>";

// 13) explode() - تحويل نص إلى مصفوفة
print_r(explode(",", "a,b,c"));
echo "<br><br>";

// 14) implode() - دمج مصفوفة إلى نص
echo implode("-", ["a","b","c"]) . "<br><br>";

// 15) substr() - جزء من النص
echo substr("Hello",1,3) . "<br><br>";

// 16) str_repeat() - تكرار نص
echo str_repeat("Hi ",3) . "<br><br>";

// 17) strcmp() - مقارنة نصين
echo strcmp("A","B") . "<br><br>";

// 18) strcasecmp() - مقارنة بدون حساسية
echo strcasecmp("hello","HELLO") . "<br><br>";

// 19) number_format() - تنسيق أرقام
echo number_format(1000000) . "<br><br>";

// 20) md5() - تشفير MD5
echo md5("password") . "<br><br>";

// 21) sha1() - تشفير SHA1
echo sha1("hello") . "<br><br>";

// 22) htmlspecialchars() - تحويل HTML إلى نص
echo htmlspecialchars("<b>Hi</b>") . "<br><br>";

// 23) nl2br() - تحويل \n إلى <br>
echo nl2br("Hello\nWorld") . "<br><br>";

// 24) addslashes() - إضافة شرطات حماية
echo addslashes("Ali's book") . "<br><br>";

// 25) stripslashes() - إزالة الشرطات
echo stripslashes("Ali\'s book") . "<br><br>";

// 26) wordwrap() - تقسيم النص لأسطر
echo wordwrap("Hello world here",5,"\n") . "<br><br>";

// 27) chr() - رقم إلى حرف
echo chr(65) . "<br><br>";

// 28) ord() - حرف إلى رقم
echo ord("A") . "<br><br>";

// 29) htmlentities() - تحويل HTML لرموز
echo htmlentities("<div>Hello</div>") . "<br><br>";

// 30) parse_str() - تحويل نص GET لمصفوفة
parse_str("name=Ali&age=20", $x);
print_r($x);
echo "<br><br>";




//---------------------------
//     30 دالة مصفوفات
//---------------------------

// 1) count() - عدد عناصر المصفوفة
echo count([1,2,3]) . "<br><br>";

// 2) array_push() - إضافة عنصر للنهاية
$arr = [1,2];
array_push($arr, 3);
print_r($arr); echo "<br><br>";

// 3) array_pop() - حذف آخر عنصر
$arr = [1,2,3];
array_pop($arr);
print_r($arr); echo "<br><br>";

// 4) array_shift() - حذف أول عنصر
$arr = [1,2,3];
array_shift($arr);
print_r($arr); echo "<br><br>";

// 5) array_unshift() - إضافة عنصر للبداية
$arr = [2,3];
array_unshift($arr,1);
print_r($arr); echo "<br><br>";

// 6) in_array() - التحقق من وجود قيمة
echo in_array(2,[1,2,3]) . "<br><br>";

// 7) array_search() - موقع عنصر
echo array_search(3,[1,2,3]) . "<br><br>";

// 8) array_merge() - دمج مصفوفات
print_r(array_merge([1,2],[3,4])); echo "<br><br>";

// 9) array_slice() - جزء من المصفوفة
print_r(array_slice([1,2,3,4],1,2)); echo "<br><br>";

// 10) array_splice() - حذف/استبدال
$arr = [1,2,3,4];
array_splice($arr,1,2);
print_r($arr); echo "<br><br>";

// 11) array_reverse() - عكس
print_r(array_reverse([1,2,3])); echo "<br><br>";

// 12) array_unique() - إزالة المكرر
print_r(array_unique([1,1,2,2,3])); echo "<br><br>";

// 13) sort() - ترتيب تصاعدي
$arr = [3,1,2];
sort($arr);
print_r($arr); echo "<br><br>";

// 14) rsort() - ترتيب تنازلي
$arr = [1,3,2];
rsort($arr);
print_r($arr); echo "<br><br>";

// 15) asort() - ترتيب حسب القيم
$arr = ["a"=>3,"b"=>1];
asort($arr);
print_r($arr); echo "<br><br>";

// 16) ksort() - ترتيب حسب المفاتيح
$arr = ["b"=>2,"a"=>1];
ksort($arr);
print_r($arr); echo "<br><br>";

// 17) array_keys() - إرجاع جميع المفاتيح
print_r(array_keys(["a"=>1,"b"=>2])); echo "<br><br>";

// 18) array_values() - إرجاع القيم
print_r(array_values(["a"=>1,"b"=>2])); echo "<br><br>";

// 19) array_fill() - إنشاء مصفوفة بقيم مكررة
print_r(array_fill(0,3,"Hi")); echo "<br><br>";

// 20) array_sum() - مجموع عناصر المصفوفة
echo array_sum([1,2,3]) . "<br><br>";

// 21) array_product() - حاصل ضرب العناصر
echo array_product([2,3,4]) . "<br><br>";

// 22) array_rand() - اختيار عنصر عشوائي
echo array_rand([1,2,3]); echo "<br><br>";

// 23) array_map() - تطبيق دالة على كل عنصر
print_r(array_map("strtoupper", ["a","b"])); echo "<br><br>";

// 24) array_filter() - تصفية العناصر
print_r(array_filter([1,2,3,4], fn($x)=>$x>2)); echo "<br><br>";

// 25) array_reduce() - دمج العناصر في قيمة واحدة
echo array_reduce([1,2,3], fn($c,$v)=>$c+$v); echo "<br><br>";

// 26) array_chunk() - تقسيم المصفوفة
print_r(array_chunk([1,2,3,4],2)); echo "<br><br>";

// 27) compact() - إنشاء مصفوفة من متغيرات
$a=1;$b=2;
print_r(compact("a","b")); echo "<br><br>";

// 28) range() - إنشاء أرقام متسلسلة
print_r(range(1,5)); echo "<br><br>";

// 29) list() - تفريغ عناصر المصفوفة لمتغيرات
list($x,$y)= [10,20];
echo $x." - ".$y."<br><br>";

// 30) array_key_exists() - التحقق من وجود مفتاح
echo array_key_exists("name", ["name"=>"Ali"]) . "<br><br>";

?>


