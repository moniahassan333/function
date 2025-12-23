

<?php
// الاتصال بقاعدة البيانات
$host = "localhost";
$db = "bank_system";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

// الرسائل
$message = "";
$message_color = "red";

if (isset($_POST['transfer'])) {
    $source_id = $_POST['source_account'] ?? '';
    $target_id = $_POST['target_account'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    if (!$source_id || !$target_id) {
        $message = "يرجى اختيار الحساب المصدر والمستهدف.";
    } elseif ($source_id == $target_id) {
        $message = "لا يمكن تحويل الأموال إلى نفس الحساب.";
    } else {
        $stmt = $pdo->prepare("SELECT balance FROM account WHERE id = ?");
        $stmt->execute([$source_id]);
        $source_balance = $stmt->fetchColumn();

        if ($source_balance < $amount || $amount <= 0) {
            $message = "الرصيد غير كافي لإتمام التحويل.";
        } else {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE account SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$amount, $source_id]);

                $stmt = $pdo->prepare("UPDATE account SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$amount, $target_id]);

                $stmt = $pdo->prepare("INSERT INTO transaction (source_account, target_account, amount) VALUES (?, ?, ?)");
                $stmt->execute([$source_id, $target_id, $amount]);

                $pdo->commit();
                $message = "تم التحويل بنجاح!";
                $message_color = "green";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "فشل التحويل: " . $e->getMessage();
            }
        }
    }
}

// جلب الحسابات
$accounts = $pdo->query("SELECT * FROM account")->fetchAll(PDO::FETCH_ASSOC);

// جلب سجل التحويلات
$transactions = $pdo->query("
    SELECT t.id, a1.name AS source_name, a1.account_number AS source_number,
           a2.name AS target_name, a2.account_number AS target_number,
           t.amount, t.transaction_date
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
<title>نظام تحويل الأموال الأكاديمي</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    margin: 0;
    padding: 0;
    direction: rtl;
}
.container {
    max-width: 1000px;
    margin: 40px auto;
    background-color: #fff;
    padding: 30px 40px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #333;
    margin-bottom: 25px;
}
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 40px;
}
select, input[type="number"] {
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
}
input[type="submit"] {
    background-color: #007bff;
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}
input[type="submit"]:hover {
    background-color: #0056b3;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}
table th, table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}
table th {
    background-color: #28a745;
    color: #fff;
}
.message {
    text-align: center;
    padding: 12px;
    font-weight: bold;
    border-radius: 8px;
    margin-bottom: 20px;
}
.message.green {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.message.red {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
</head>
<body>
<div class="container">
    <h2>نظام تحويل الأموال الأكاديمي</h2>

    <?php if($message): ?>
        <div class="message <?= $message_color ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <label>من الحساب:</label>
        <select name="source_account" required>
            <option value="">اختر الحساب المصدر</option>
            <?php foreach($accounts as $acc): ?>
                <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?> (<?= $acc['account_number'] ?>)</option>
            <?php endforeach; ?>
        </select>

        <label>إلى الحساب:</label>
        <select name="target_account" required>
            <option value="">اختر الحساب المستهدف</option>
            <?php foreach($accounts as $acc): ?>
                <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?> (<?= $acc['account_number'] ?>)</option>
            <?php endforeach; ?>
        </select>

        <label>المبلغ:</label>
        <input type="number" name="amount" step="0.01" min="1" required>

        <input type="submit" name="transfer" value="تحويل الأموال">
    </form>

    <h2>الحسابات</h2>
    <table>
        <tr>
            <th>رقم الحساب</th>
            <th>الاسم</th>
            <th>الرصيد</th>
        </tr>
        <?php foreach($accounts as $acc): ?>
        <tr>
            <td><?= $acc['account_number'] ?></td>
            <td><?= $acc['name'] ?></td>
            <td><?= number_format($acc['balance'], 2) ?> ريال</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>سجل التحويلات</h2>
    <table>
        <tr>
            <th>من</th>
            <th>رقم الحساب</th>
            <th>إلى</th>
            <th>رقم الحساب</th>
            <th>المبلغ</th>
            <th>التاريخ</th>
        </tr>
        <?php foreach($transactions as $t): ?>
        <tr>
            <td><?= $t['source_name'] ?></td>
            <td><?= $t['source_number'] ?></td>
            <td><?= $t['target_name'] ?></td>
            <td><?= $t['target_number'] ?></td>
            <td><?= number_format($t['amount'], 2) ?> ريال</td>
            <td><?= $t['transaction_date'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>


