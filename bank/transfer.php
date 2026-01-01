

<?php
require_once 'db.php';

/* =========================
   Logging Function
   ========================= */
function writeLog($message) {
    $file = 'error.log';
    $date = date("Y-m-d H:i:s");
    file_put_contents($file, "[$date] $message" . PHP_EOL, FILE_APPEND);
}

$message = "";
$message_color = "red";

if (isset($_POST['transfer'])) {

    // =========================
    // Secure Input Validation
    // =========================
    $source_id = filter_input(INPUT_POST, 'source_account', FILTER_VALIDATE_INT);
    $target_id = filter_input(INPUT_POST, 'target_account', FILTER_VALIDATE_INT);
    $amount    = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

    if ($source_id === false || $target_id === false || $amount === false) {
        writeLog("Tampering detected | Invalid input type");
        die("طلب غير صالح");
    }

    if ($amount <= 0) {
        writeLog("Blocked | Invalid amount: $amount");
        $message = "المبلغ غير صالح!";
    } elseif ($source_id === $target_id) {
        writeLog("Blocked | Same account used | ID: $source_id");
        $message = "لا يمكن التحويل لنفس الحساب!";
    } else {

        try {
            // =========================
            // Start Secure Transaction
            // =========================
            $pdo->beginTransaction();

            // Lock source account
            $stmt = $pdo->prepare("SELECT balance FROM account WHERE id = ? FOR UPDATE");
            $stmt->execute([$source_id]);
            $source_balance = $stmt->fetchColumn();

            if ($source_balance === false) {
                writeLog("Blocked | Source account not found | ID: $source_id");
                throw new Exception("الحساب المصدر غير موجود");
            }

            if ($source_balance < $amount) {
                writeLog("Blocked | Insufficient balance | ID: $source_id | Amount: $amount");
                throw new Exception("الرصيد غير كافي");
            }

            // Lock target account
            $stmt = $pdo->prepare("SELECT id FROM account WHERE id = ? FOR UPDATE");
            $stmt->execute([$target_id]);
            if (!$stmt->fetch()) {
                writeLog("Blocked | Target account not found | ID: $target_id");
                throw new Exception("الحساب المستهدف غير موجود");
            }

            // Deduct from source
            $stmt = $pdo->prepare("UPDATE account SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $source_id]);

            // Add to target
            $stmt = $pdo->prepare("UPDATE account SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $target_id]);

            // Log transaction
            $stmt = $pdo->prepare("
                INSERT INTO transaction (source_account, target_account, amount)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$source_id, $target_id, $amount]);

            $pdo->commit();

            $message = "تم التحويل بنجاح";
            $message_color = "green";

        } catch (Exception $e) {
            $pdo->rollBack();
            writeLog("Transfer failed | " . $e->getMessage());
            $message = $e->getMessage();
        }
    }
}

// =========================
// Fetch Accounts & Transfers
// =========================
$accounts = $pdo->query("SELECT * FROM account")->fetchAll(PDO::FETCH_ASSOC);

$transactions = $pdo->query("
    SELECT t.id, a1.name AS source_name, a2.name AS target_name,
           t.amount, t.transaction_date
    FROM transaction t
    JOIN account a1 ON t.source_account = a1.id
    JOIN account a2 ON t.target_account = a2.id
    ORDER BY t.transaction_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>نظام تحويل الأموال</title>
<style>
body{font-family:Arial;background:#f0f2f5;margin:0}
.container{width:90%;max-width:900px;margin:30px auto;background:#fff;
padding:20px;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,.1)}
h2{text-align:center;color:#19395c}
form{display:flex;flex-direction:column;gap:12px}
select,input{padding:10px;border-radius:8px;border:1px solid #ccc}
input[type=submit]{background:#19395c;color:#fff;border:none;cursor:pointer}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:10px;border:1px solid #ddd;text-align:center}
th{background:#19395c;color:#fff}
.message{text-align:center;padding:10px;border-radius:8px;margin-bottom:15px}
</style>
</head>

<body>
<div class="container">
<h2>نظام تحويل الأموال</h2>

<?php if($message): ?>
<div class="message" style="color:<?= $message_color ?>"><?= $message ?></div>
<?php endif; ?>

<form method="post">
<select name="source_account" required>
<option value="">من الحساب</option>
<?php foreach($accounts as $a): ?>
<option value="<?= $a['id'] ?>"><?= $a['name'] ?></option>
<?php endforeach; ?>
</select>

<select name="target_account" required>
<option value="">إلى الحساب</option>
<?php foreach($accounts as $a): ?>
<option value="<?= $a['id'] ?>"><?= $a['name'] ?></option>
<?php endforeach; ?>
</select>


<input type="number" name="amount" step="0.01" min="1" required>
<input type="submit" name="transfer" value="تحويل">
</form>

<h2>الحسابات</h2>
<table>
<tr><th>الاسم</th><th>الرصيد</th></tr>
<?php foreach($accounts as $a): ?>
<tr><td><?= $a['name'] ?></td><td><?= $a['balance'] ?></td></tr>
<?php endforeach; ?>
</table>

<h2>سجل التحويلات</h2>
<table>
<tr><th>من</th><th>إلى</th><th>المبلغ</th><th>التاريخ</th></tr>
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


