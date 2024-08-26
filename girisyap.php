<?php
session_start();
require 'config.php';

// Oturum açılmışsa, zaten giriş yapılmış demektir, ana sayfaya yönlendir.
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// "Beni Hatırla" kutucuğu işaretli mi kontrol edilir.
$remember_checked = isset($_COOKIE['remember_me']) && $_COOKIE['remember_me'] == 'true';

// "Beni Hatırla" kutucuğu işaretliyse, kullanıcı otomatik olarak giriş yapılır.
if ($remember_checked && isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    $username = $_COOKIE['username'];
    $password = $_COOKIE['password'];

    $users = readJSON('zenciler.json');
    foreach ($users as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Ana sayfaya yönlendirilir.
            header('Location: index.php');
            exit;
        }
    }
}

// POST ile form gönderildiğinde giriş kontrolü yapılır.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $users = readJSON('zenciler.json');
    $banned_users = readJSON('banned_users.json');

    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    // Kullanıcı adı ve şifre kontrolü yapılır.
    $login_successful = false;
    foreach ($users as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            // Kullanıcı banlanmış mı kontrol edilir.
            foreach ($banned_users as $banned_user) {
                if ($banned_user['user_id'] == $user['id']) {
                    echo "Bu Zenci Banlanmıştır!";
                    exit;
                }
            }

            // Oturum değişkenleri atanır.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // "Beni Hatırla" kutucuğu işaretliyse, çerezler oluşturulur.
            if ($remember_me) {
                setcookie('username', $username, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/");
                setcookie('remember_me', 'true', time() + (86400 * 30), "/");
            } else {
                // "Beni Hatırla" işaretlenmemişse, çerezler silinir.
                setcookie('username', '', time() - 3600, "/");
                setcookie('password', '', time() - 3600, "/");
                setcookie('remember_me', '', time() - 3600, "/");
            }

            $login_successful = true;
            break;
        }
    }

    // Giriş başarısız ise JavaScript ile popup gösterilecek.
    if (!$login_successful) {
        echo "<script>
                alert('Rumuz veya Şifre Yanlış!');
                window.location.href = 'girisyap.php';
              </script>";
        exit;
    }

    // Ana sayfaya yönlendirilir.
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ZENCİ.COM | GİRİŞ YAP</title>
    <link rel="icon" href="Z.png"/>
	    <style>
        /* Ekstra CSS popup için */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .popup.show {
            display: block;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
    </style>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('Adli.png');
            background-size: cover;
        }

        .header {
            background-color: rgba(0, 0, 0, 1);
            text-align: center;
            padding: 20px 0;
        }

        .logo-container img {
            max-width: 30%;
        }

        .black-bar {
            height: 1px;
            background-color: #000;
        }

        .login-form-container {
            text-align: center;
            padding: 20px 0;
        }

        .login-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(0, 0, 0, 0.7);
            color: rgba(255, 255, 255, 0.8);
        }

        .login-form h2 {
            color: #fff;
        }

        .login-form label {
            display: block;
            font-weight: bold;
            color: #fff;
            text-align: left;
        }

        .login-form input {
            width: calc(100% - 10px);
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        .login-form input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            padding: 5px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
	.neon-text {
            text-align: center;
            font-size: 24px;
            color: #fff;
            text-transform: uppercase;
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            animation: neon 1.5s ease-in-out infinite alternate;
        }

        @keyframes neon {
            0% {
                text-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px #fff;
            }
            100% {
                text-shadow: 0 0 5px #6aff6a, 0 0 10px #6aff6a, 0 0 15px #6aff6a;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <a href="index.php"><img src="banner.png" alt="BANNER" title="Geri Dönebilirsin Delikanlı"></a>
        </div>
        <div class="black-bar"></div>
    </div>

    <div class="login-form-container">
        <div class="login-form">
            <h2>Giremeyiş Formu</h2>
			<main>
            <form action="girisyap.php" method="post">
                <div class="form-group">
                    <label for="username">Zenci Rumuzun:</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Z-Posta Adresin:</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class "form-group">
                    <label for="password">Şifren:</label>
                    <input type="password" name="password" id="password" required>
					
                </div>
				
				<p><div class "form-group">
                <input type="checkbox" name="remember_me">Unutma Soracam Ben Bunları
            </div></p>

                <input type="submit" value="Tıkla ve Hayatsızlık Reenkarnasyonunu Yaşa">
            </form>
			<!-- Popup mesaj -->
        <div id="popup" class="popup">
            <span class="close-btn" onclick="closePopup()">X</span>
            <p>Rumuz veya Şifre Yanlış!</p>
        </div>
    </main>
	  <div class="neon-text">ARAMIZA TEKRARDAN HOŞ GELDİN ZENCİ HAZRETLERİ!</div>
        </div>
    </div>
	    <footer>
        <center><p>&copy; 2024</p></center>
    </footer>

    <!-- JavaScript ile popup yönetimi -->
    <script>
        // Popupı göster
        function showPopup() {
            document.getElementById('popup').classList.add('show');
        }

        // Popupı kapat
        function closePopup() {
            document.getElementById('popup').classList.remove('show');
        }

        // PHP tarafında hata durumu oluştuğunda popup göster
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !$login_successful): ?>
            showPopup();
        <?php endif; ?>
    </script>
</body>
</html>
