<?php
// middleware/Auth.php
class Auth {
    public static function check(): bool  { return isset($_SESSION['user_id']); }
    public static function user(): ?array { return $_SESSION['user'] ?? null; }
    public static function id(): ?int     { return $_SESSION['user_id'] ?? null; }
    public static function role(): string { return $_SESSION['user']['role'] ?? 'receptionist'; }
    public static function isAdmin(): bool  { return self::role() === 'admin'; }
    public static function isDoctor(): bool { return in_array(self::role(), ['admin','doctor']); }
    public static function canEdit(): bool  { return in_array(self::role(), ['admin','doctor','receptionist']); }

    public static function require(): void {
        if (!self::check()) { setFlash('error','Please login to continue.'); redirect(BASE_URL.'/login'); }
    }
    public static function requireRole(string ...$roles): void {
        self::require();
        if (!in_array(self::role(), $roles)) {
            setFlash('error','You do not have permission to access this page.');
            redirect(BASE_URL.'/dashboard');
        }
    }
    public static function login(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role'],'specialization'=>$user['specialization']];
    }
    public static function logout(): void { session_unset(); session_destroy(); }
}
