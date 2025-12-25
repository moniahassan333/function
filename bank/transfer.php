

<?php
// استدعاء ملف الاتصال
require_once 'db.php';

/* =========================
   دالة تسجيل الأخطاء (logs)
   ========================= */
function writeLog($message) {
    $logFile = 'error.log'; // ملف السجلات
    $date = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$date] $message" . PHP_EOL, FILE_APPEND);
}

$message = "";
$message_color = "red";

if (isset($_POST['transfer'])) {
    $source_id = $_POST['source_account'] ?? '';
    $target_id = $_POST['target_account'] ?? '';
    $amount = $_POST['amount'] ?? 0;

    if (empty($source_id) || empty($target_id)) {
        writeLog("Transfer blocked | Reason: Missing account selection | Source: $source_id | Target: $target_id");
        $message = "يجب اختيار الحساب المصدر والحساب المستهدف!";
    } elseif ($source_id == $target_id) {
        writeLog("Transfer blocked | Reason: Same account selected | Source: $source_id");
        $message = "لا يمكن التحويل إلى نفس الحساب!";
    } else {
        // جلب رصيد الحساب المصدر
        $stmt = $pdo->prepare("SELECT balance FROM account WHERE id = ?");
        $stmt->execute([$source_id]);
        $source_balance = $stmt->fetchColumn();

        if ($source_balance <= 0) {
            writeLog("Transfer blocked | Reason: Empty balance | Source: $source_id");
            $message = "الرصيد في الحساب المرسل فارغ!";
        } elseif ($source_balance < $amount) {
            writeLog("Transfer blocked | Reason: Insufficient balance | Source: $source_id | Amount: $amount");
            $message = "الرصيد غير كافي لإجراء التحويل!";
        } else {
            // بدء المعاملة
            $pdo->beginTransaction();
            try {
                // خصم المبلغ من الحساب المصدر
                $stmt = $pdo->prepare("UPDATE account SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$amount, $source_id]);

                // إضافة المبلغ للحساب المستهدف
                $stmt = $pdo->prepare("UPDATE account SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$amount, $target_id]);

                // تسجيل عملية التحويل
                $stmt = $pdo->prepare("INSERT INTO transaction (source_account, target_account, amount) VALUES (?, ?, ?)");
                $stmt->execute([$source_id, $target_id, $amount]);

                // تأكيد العملية
                $pdo->commit();

                $message = "تم التحويل بنجاح!";
                $message_color = "green";

            } catch (Exception $e) {
                $pdo->rollBack();

                writeLog("Transfer failed | Source: $source_id | Target: $target_id | Amount: $amount | Error: " . $e->getMessage());

                $message = "حدث خطأ أثناء تنفيذ التحويل، حاول مرة أخرى لاحقًا.";
            }
        }
    }
}

// جلب الحسابات
$accounts = $pdo->query("SELECT * FROM account")->fetchAll(PDO::FETCH_ASSOC);

// جلب سجل التحويلات
$transactions = $pdo->query("
    SELECT t.id, a1.name AS source_name, a2.name AS target_name, t.amount, t.transaction_date
    FROM transaction t
    JOIN account a1 ON t.source_account = a1.id
    JOIN account a2 ON t.target_account = a2.id
    ORDER BY t.transaction_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>نظام تحويل الأموال</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; direction: rtl; margin: 0; padding: 0; }
.container { width: 90%; max-width: 900px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #333; margin-bottom: 20px; }
form { display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px; }
select, input[type="number"] { padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px; }
input[type="submit"] { background: #174053ff; color: #fff; padding: 12px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
input[type="submit"]:hover { background: #2b3061ff; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
table th, table td { padding: 10px; border: 1px solid #ddd; text-align: center; }
table th { background: #19395cff; color: #fff; }
.message { text-align: center; padding: 10px; font-weight: bold; border-radius: 8px; margin-bottom: 20px; }
.message[style*="green"] { background: #d4edda; color: #155724; }
.message[style*="red"] { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>
<div class="container">
<h2>نظام تحويل الأموال</h2>

<?php if($message): ?>
<div class="message" style="color: <?= $message_color ?>;"><?= $message ?></div>
<?php endif; ?>

<form method="post">
<label>من الحساب:</label>
<select name="source_account" required>
<option value="">اختر الحساب المصدر</option>
<?php foreach($accounts as $acc): ?>
<option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
<?php endforeach; ?>
</select>

<label>إلى الحساب:</label>
<select name="target_account" required>
<option value="">اختر الحساب المستهدف</option>
<?php foreach($accounts as $acc): ?>
<option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
<?php endforeach; ?>
</select>

<label>المبلغ:</label>
<input type="number" name="amount" step="0.01" min="1" required>

<input type="submit" name="transfer" value="تحويل الأموال">
</form>

<h2>الحسابات</h2>
<table>
<tr>
<th>اسم الحساب</th>
<th>الرصيد</th>
</tr>
<?php foreach($accounts as $acc): ?>
<tr>
<td><?= $acc['name'] ?></td>
<td><?= $acc['balance'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2>سجل التحويلات</h2>
<table>
<tr>
<th>من</th>
<th>إلى</th>
<th>المبلغ</th>
<th>التاريخ</th>
</tr>
<?php foreach($transactions as $t): ?>
<tr>
<td><?= $t['source_name'] ?></td>
<td><?= $t['target_name'] ?></td>
<td><?= $t['amount'] ?></td>
<td><?= $t['transaction_date'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>


