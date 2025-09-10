<?php
// Bu kısmı kendi veritabanı bilgilerinizle güncelleyin
$host = 'localhost';        // Veritabanı sunucu adresi
$dbname = 'login_system';   // Veritabanı adı
$username = 'root';         // Veritabanı kullanıcı adı
$password = '';             // Veritabanı şifresi

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Veritabanı tablosu oluştur (eğer yoksa)
$createTable = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$pdo->exec($createTable);

// Form işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($name && $email && $password && strlen($password) >= 6) {
            try {
                // E-posta kontrolü
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if (!$stmt->fetch()) {
                    // Kullanıcı kaydet
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $email, $hashedPassword]);
                    $message = "Kayıt başarılı! Giriş yapabilirsiniz.";
                    $messageType = "success";
                } else {
                    $message = "Bu e-posta adresi zaten kullanılıyor.";
                    $messageType = "error";
                }
            } catch (PDOException $e) {
                $message = "Kayıt sırasında bir hata oluştu.";
                $messageType = "error";
            }
        } else {
            $message = "Lütfen tüm alanları doldurun ve şifre en az 6 karakter olsun.";
            $messageType = "error";
        }
    }
    
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($email && $password) {
            try {
                $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $message = "Giriş başarılı! Hoşgeldin " . $user['name'];
                    $messageType = "success";
                } else {
                    $message = "E-posta veya şifre hatalı.";
                    $messageType = "error";
                }
            } catch (PDOException $e) {
                $message = "Giriş sırasında bir hata oluştu.";
                $messageType = "error";
            }
        } else {
            $message = "Lütfen tüm alanları doldurun.";
            $messageType = "error";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>SPEXRON | Animated Login Page</title>
</head>
<body>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <h1>Üye Ol</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>Kayıt olmak için e-posta ve şifrenizi yazın</span>
                <input type="text" name="name" placeholder="İsim" required>
                <input type="email" name="email" placeholder="E-posta" required>
                <input type="password" name="password" placeholder="Şifre" required>
                <button type="submit">Üye Ol</button>
            </form>
        </div>

        <div class="form-container sign-in">
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <h1>Giriş Yap</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>E-posta ve şifrenizi yazın</span>
                <input type="email" name="email" placeholder="E-posta" required>
                <input type="password" name="password" placeholder="Şifre" required>
                <a href="#">Şifremi Unuttum</a>
                <button type="submit">Giriş Yap</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Tekrar Hoşgeldin!</h1>
                    <p>Tüm site özelliklerini kullanmak için kişisel bilgilerinizi girin.</p>
                    <button class="hidden" id="login">Giriş Yap</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Merhaba!</h1>
                    <p>Tüm site özelliklerini kullanmak için kaydolun</p>
                    <button class="hidden" id="register">Kayıt Ol</button>
                </div>
            </div>
        </div>
    </div>


    <?php if (isset($message)): ?>
    <div class="message <?php echo $messageType; ?>" style="
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        max-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        background-color: <?php echo $messageType === 'success' ? '#4CAF50' : '#f44336'; ?>;
    ">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <script>
        setTimeout(() => {
            const message = document.querySelector('.message');
            if (message) message.remove();
        }, 3000);
    </script>
    <?php endif; ?>

    <script src="script.js"></script>   
</body>
</html>

<!-- CODED AND DESIGNED BY SPEXRON -->