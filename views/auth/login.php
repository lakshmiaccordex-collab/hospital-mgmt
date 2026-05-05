<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — MediCare HMS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:linear-gradient(135deg,#0a3d3d 0%,#0d6e6e 100%);min-height:100vh;display:grid;place-items:center}
.login-box{background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,.2);padding:2.5rem;width:100%;max-width:420px}
.login-logo{text-align:center;margin-bottom:2rem}
.login-logo .icon{width:64px;height:64px;background:#e6f4f4;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;color:#0d6e6e;margin-bottom:.75rem}
.login-logo h1{font-size:1.4rem;font-weight:700;color:#0a3d3d}
.login-logo p{font-size:.85rem;color:#94a3b8;margin-top:.25rem}
.flash{padding:.75rem 1rem;border-radius:8px;font-size:.875rem;margin-bottom:1.25rem}
.flash-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
.flash-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a}
.form-group{margin-bottom:1rem}
label{font-size:.85rem;font-weight:500;color:#475569;display:block;margin-bottom:.4rem}
.input-wrap{position:relative}
.input-wrap i{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.9rem}
input{width:100%;padding:.65rem .85rem .65rem 2.5rem;border:1px solid #e2e8f0;border-radius:8px;font-size:.9rem;transition:border .15s}
input:focus{outline:none;border-color:#0d6e6e;box-shadow:0 0 0 3px rgba(13,110,110,.12)}
.btn-login{width:100%;padding:.75rem;background:#0d6e6e;color:#fff;border:none;border-radius:8px;font-size:.95rem;font-weight:600;cursor:pointer;margin-top:.5rem;transition:background .15s}
.btn-login:hover{background:#095858}
.demo-box{margin-top:1.5rem;padding:1rem;background:#f8fafc;border-radius:8px;font-size:.8rem;color:#64748b}
.demo-box strong{color:#1e293b;display:block;margin-bottom:.4rem}
.demo-row{display:flex;justify-content:space-between;padding:.2rem 0;border-bottom:1px solid #f1f5f9}
.role-tag{font-weight:600;color:#0d6e6e}
</style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">
    <div class="icon"><i class="fa fa-hospital"></i></div>
    <h1>MediCare HMS</h1>
    <p>Hospital Patient Management System</p>
  </div>

  <?php $flash=getFlash(); if($flash):?>
  <div class="flash flash-<?=e($flash['type'])?>"><?=e($flash['message'])?></div>
  <?php endif;?>

  <form method="POST" action="<?=BASE_URL?>/login">
    <div class="form-group">
      <label>Email Address</label>
      <div class="input-wrap">
        <i class="fa fa-envelope"></i>
        <input type="email" name="email" placeholder="doctor@hospital.com" required autofocus>
      </div>
    </div>
    <div class="form-group">
      <label>Password</label>
      <div class="input-wrap">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
    </div>
    <button type="submit" class="btn-login"><i class="fa fa-right-to-bracket"></i> Sign In</button>
  </form>

  <div class="demo-box">
    <strong>Demo Credentials (password: password)</strong>
    <div class="demo-row"><span class="role-tag">Admin</span><span>admin@hospital.com</span></div>
    <div class="demo-row"><span class="role-tag">Doctor</span><span>ramesh@hospital.com</span></div>
    <div class="demo-row"><span class="role-tag">Receptionist</span><span>mary@hospital.com</span></div>
  </div>
</div>
</body>
</html>
