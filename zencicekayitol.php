<html>
<head>
<title>ZENCİ.COM | ZENCİLERİN TEK ADRESİ</title>
<?php
$host = 'sql311.infinityfree.com';
$db = 'if0_36231368_zenci';
$user = 'if0_36231368';
$pass = 'CHwJPAM8KPLjE';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
     echo 'Bağlantı Hatası:' . $e->getmMessage();
}

$email = $_POST['email'];
$password = $_POST['password'];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO uyeler (email, sifre) VALUES (:email, :password)";
$stmt = $conn->prepare($query);
$stmt->execute(array(':email' => $email, ':password' => $hashedPassword));

echo 'Kayıt işlemi başarılı. Şimdi <a href="login.php">giriş yapabilirsiniz</a>.';
?>

</html>
</head>