<html>
<head>
<title>ZENCİ.COM | ZENCİLERİN TEK ADRESİ</title>
<?php
session_start();
$host = 'sql311.infinityfree.com';
$db = 'if0_36231368_zenci';
$user = 'if0_36231368';
$pass = 'CHwJPAM8KPLjE';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'Bağlantı hatası: ' . $e->getMessage();
}

$email = $_POST['email'];
$password = $_POST['password'];

$query = "SELECT * FROM uyeler WHERE email = :email";
$stmt = $conn->prepare($query);
$stmt->execute(array(':email' => $email));
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    if (password_verify($password, $user['sifre'])) {
        $_SESSION['email'] = $email;
        echo 'Giriş başarılı. Hoş geldiniz, ' . $email . '!<br>';
        echo 'Ana sayfaya yönlendiriliyorsunuz...';
        header('Refresh: 3; URL=anasayfa.php');
        exit();
    }
}

echo 'E-posta veya şifre yanlış. Lütfen tekrar deneyin.';
?>
</head>
</html>