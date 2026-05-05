<?php
// config/app.php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

define('BASE_URL',      Database::env('APP_URL', 'http://localhost/hospital-mgmt'));
define('UPLOAD_DIR',    __DIR__ . '/../' . Database::env('UPLOAD_DIR', 'uploads/'));
define('EXPORT_DIR',    __DIR__ . '/../' . Database::env('EXPORT_DIR', 'exports/'));
define('MAX_FILE_SIZE', (int) Database::env('MAX_FILE_SIZE', '5242880'));
define('PER_PAGE', 10);

function setFlash(string $type, string $msg): void { $_SESSION['flash'] = ['type'=>$type,'message'=>$msg]; }
function getFlash(): ?array {
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f;
}
function e(mixed $v): string { return htmlspecialchars((string)($v??''), ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): void { header("Location: $url"); exit; }
function asset(string $p): string { return BASE_URL . '/' . ltrim($p, '/'); }
function calcAge(string $dob): int {
    return (int) (new DateTime($dob))->diff(new DateTime())->y;
}
