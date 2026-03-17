<?php
// install.php - Professional Web-based setup

session_start();

// If already installed, don't allow access
if (file_exists('../config/installed.txt') && file_exists('../.env')) {
    die("Sistem zaten kurulu. Yeniden kurmak için .env ve config/installed.txt dosyalarını silin.");
}

/**
 * XSS Protection helper
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// CSRF Token Generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF doğrulaması başarısız!");
    }

    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $site_url = rtrim($_POST['site_url'], '/') . '/';
    
    $admin_user = $_POST['admin_user'];
    $admin_pass = password_hash($_POST['admin_pass'], PASSWORD_BCRYPT);

    try {
        // 1. Test Database Connection
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 2. Create Database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name` ");
        
        // 3. Import SQL
        if (file_exists('database.sql')) {
            $sql = file_get_contents('database.sql');
            $pdo->exec($sql);
        } else {
            throw new Exception("database.sql dosyası bulunamadı!");
        }
        
        // 4. Create initial admin (Upsert)
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password = VALUES(password)");
        $stmt->execute([$admin_user, $admin_pass]);
        
        // 5. Generate .env file
        $env_content = "DB_HOST=$db_host\n";
        $env_content .= "DB_NAME=$db_name\n";
        $env_content .= "DB_USER=$db_user\n";
        $env_content .= "DB_PASS=$db_pass\n";
        $env_content .= "SITE_URL=$site_url\n";
        
        file_put_contents('../.env', $env_content);
        
        // 6. Mark as installed
        if (!is_dir('../config')) mkdir('../config', 0755, true);
        file_put_contents('../config/installed.txt', date('Y-m-d H:i:s'));
        
        $status = "success";
        $message = "Kurulum başarıyla tamamlandı!";
    } catch (Exception $e) {
        $status = "error";
        $message = "Hata: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurulum Sihirbazı - Apartman Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; min-height: 100vh; padding: 20px 0; }
        .install-card { max-width: 600px; margin: auto; padding: 2.5rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); background: #fff; }
        .form-label { font-weight: 600; color: #4a5568; }
        .btn-primary { padding: 12px; border-radius: 10px; font-weight: 700; background: #4361ee; border: none; }
        .btn-primary:hover { background: #3730a3; }
        .alert { border-radius: 12px; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-card">
            <div class="text-center mb-5">
                <i class="fas fa-tools text-primary mb-3" style="font-size: 3rem;"></i>
                <h2 class="fw-bold">Kurulum Sihirbazı</h2>
                <p class="text-muted">Sistemi saniyeler içinde hazır hale getirelim.</p>
            </div>

            <?php if ($status === "success"): ?>
                <div class="alert alert-success shadow-sm p-4 text-center">
                    <i class="fas fa-check-circle fs-1 mb-3"></i>
                    <h4>Tebrikler!</h4>
                    <p><?php echo e($message); ?></p>
                    <hr>
                    <p class="mb-3 text-secondary small">Güvenliğiniz için <b>install/</b> klasörünü silmeniz önerilir.</p>
                    <a href="../public/" class="btn btn-primary w-100">Paneli Başlat <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            <?php else: ?>
                <?php if ($message): ?>
                    <div class="alert alert-danger shadow-sm">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?php echo e($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <h5 class="mb-3 border-bottom pb-2 mt-4"><i class="fas fa-database me-2"></i> Veritabanı Ayarları</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DB Host</label>
                            <input type="text" name="db_host" class="form-control" value="localhost" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DB Adı</label>
                            <input type="text" name="db_name" class="form-control" value="apartman_yonetim_db" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DB Kullanıcı</label>
                            <input type="text" name="db_user" class="form-control" value="root" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DB Şifre</label>
                            <input type="password" name="db_pass" class="form-control">
                        </div>
                    </div>

                    <h5 class="mb-3 border-bottom pb-2 mt-4"><i class="fas fa-user-shield me-2"></i> Yönetici Hesabı</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kullanıcı Adı</label>
                            <input type="text" name="admin_user" class="form-control" placeholder="admin" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="admin_pass" class="form-control" required>
                        </div>
                    </div>

                    <h5 class="mb-3 border-bottom pb-2 mt-4"><i class="fas fa-link me-2"></i> Site Erişimi</h5>
                    <div class="mb-4">
                        <label class="form-label">Site URL</label>
                        <input type="text" name="site_url" class="form-control" value="http://your-domain.com/public/" required>
                        <div class="form-text small">Sonunda "/" karakteri olduğundan emin olun.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm">
                        Kurulumu Başlat <i class="fas fa-rocket ms-2"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
