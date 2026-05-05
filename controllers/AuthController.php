<?php
// controllers/AuthController.php
class AuthController {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function loginForm(): void {
        if (Auth::check()) redirect(BASE_URL.'/dashboard');
        require __DIR__.'/../views/auth/login.php';
    }

    public function login(): void {
        $email = trim($_POST['email']??'');
        $pass  = $_POST['password']??'';
        if (!$email||!$pass) { setFlash('error','Email and password required.'); redirect(BASE_URL.'/login'); }

        $s=$this->db->prepare("SELECT * FROM users WHERE email=? AND is_active=1");
        $s->execute([$email]); $user=$s->fetch();

        if (!$user||!password_verify($pass,$user['password'])) {
            setFlash('error','Invalid email or password.'); redirect(BASE_URL.'/login');
        }
        Auth::login($user);
        $this->log('login','user',$user['id'],"User {$user['name']} logged in.");
        setFlash('success',"Welcome back, Dr. {$user['name']}!");
        redirect(BASE_URL.'/dashboard');
    }

    public function logout(): void {
        $this->log('logout','user',Auth::id(),"User logged out.");
        Auth::logout();
        setFlash('success','Logged out successfully.');
        redirect(BASE_URL.'/login');
    }

    private function log(string $action, string $entity, ?int $id, string $desc): void {
        $this->db->prepare(
            "INSERT INTO activity_log (user_id,action,entity,entity_id,description) VALUES (?,?,?,?,?)"
        )->execute([Auth::id(),$action,$entity,$id,$desc]);
    }
}
