<?php
// ==============================================
// MUST Hostel Booking System - Configuration
// ==============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'must_hostel');

define('SITE_NAME', 'MUST Hostel Booking System');
define('SITE_URL', 'http://localhost/must_hostel');
define('MAX_CAPACITY', 500);

// Email settings (use PHPMailer or built-in mail())
define('MAIL_FROM', 'noreply@must.ac.mw');
define('MAIL_FROM_NAME', 'MUST Hostel Office');
define('ACCOMMODATION_FEE', 180000); // MWK

// PDO Connection
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die("<div style='font-family:monospace;color:red;padding:20px'>
                 <b>Database Error:</b> " . $e->getMessage() . "<br>
                 Please check your database configuration in includes/config.php
                 </div>");
        }
    }
    return $pdo;
}

// Generate email from reg number
function genEmail($regNumber) {
    return strtolower(trim($regNumber)) . '@must.ac.mw';
}

// Generate invoice number
function genInvoiceNumber() {
    return 'INV-MUST-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Generate receipt number
function genReceiptNumber() {
    return 'RCP-MUST-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Send email (basic PHP mail wrapper)
function sendEmail($to, $subject, $htmlBody, $type = 'general') {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
    $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
    
    $result = @mail($to, $subject, $htmlBody, $headers);
    
    // Log the email
    try {
        $db = getDB();
        $db->prepare("INSERT INTO email_logs (recipient_email, subject, type, status) VALUES (?,?,?,?)")
           ->execute([$to, $subject, $type, $result ? 'sent' : 'failed']);
    } catch(Exception $e) {}
    
    return $result;
}

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auth helpers
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin($role = null) {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
    if ($role && $_SESSION['role'] !== $role) {
        // Check array of roles
        if (is_array($role) && !in_array($_SESSION['role'], $role)) {
            header('Location: ' . SITE_URL . '/index.php?error=unauthorized');
            exit;
        } elseif (!is_array($role)) {
            header('Location: ' . SITE_URL . '/index.php?error=unauthorized');
            exit;
        }
    }
}

function currentUser() {
    return $_SESSION ?? [];
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Sanitize input
function clean($str) {
    return htmlspecialchars(strip_tags(trim($str)));
}
?>
