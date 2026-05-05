<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle??'Hospital Management System') ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0d6e6e;--primary-dark:#095858;--primary-light:#e6f4f4;
  --red:#dc2626;--green:#16a34a;--yellow:#d97706;--blue:#2563eb;--purple:#7c3aed;
  --gray-50:#f9fafb;--gray-100:#f3f4f6;--gray-200:#e5e7eb;
  --gray-400:#9ca3af;--gray-600:#4b5563;--gray-800:#1f2937;
  --sidebar:260px;--header:64px;
}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--gray-50);color:var(--gray-800);display:flex;min-height:100vh}

/* Sidebar */
.sidebar{width:var(--sidebar);background:#0a3d3d;color:#94a3b8;position:fixed;top:0;left:0;height:100vh;display:flex;flex-direction:column;z-index:100}
.sidebar-brand{padding:1.25rem 1.5rem;border-bottom:1px solid #1a5555}
.sidebar-brand h1{color:#fff;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:.5rem}
.sidebar-brand h1 span{color:#4dd9ac}
.sidebar-brand p{font-size:.72rem;color:#64748b;margin-top:.2rem}
.sidebar-nav{flex:1;padding:.75rem 0;overflow-y:auto}
.nav-label{font-size:.62rem;text-transform:uppercase;letter-spacing:.1em;color:#334155;padding:.5rem 1.5rem .2rem}
.nav-item{display:flex;align-items:center;gap:.75rem;padding:.6rem 1.5rem;color:#94a3b8;text-decoration:none;font-size:.85rem;transition:all .15s}
.nav-item:hover,.nav-item.active{background:#1a5555;color:#fff}
.nav-item.active{border-right:3px solid #4dd9ac}
.nav-item i{width:18px;text-align:center;font-size:.9rem}
.sidebar-footer{padding:1rem 1.5rem;border-top:1px solid #1a5555;font-size:.8rem}
.sidebar-footer .sname{color:#fff;font-weight:600}
.sidebar-footer .srole{color:#64748b;text-transform:capitalize;font-size:.75rem}

/* Main */
.main{margin-left:var(--sidebar);flex:1;display:flex;flex-direction:column;min-height:100vh}
.header{height:var(--header);background:#fff;border-bottom:1px solid var(--gray-200);display:flex;align-items:center;justify-content:space-between;padding:0 1.5rem;position:sticky;top:0;z-index:50}
.header-title{font-size:1rem;font-weight:600;color:var(--gray-800)}
.header-right{display:flex;align-items:center;gap:1rem}
.btn-logout{background:none;border:1px solid var(--gray-200);padding:.4rem .85rem;border-radius:6px;cursor:pointer;font-size:.82rem;color:var(--gray-600);text-decoration:none;transition:all .15s}
.btn-logout:hover{background:var(--red);color:#fff;border-color:var(--red)}
.content{flex:1;padding:1.5rem}

/* Flash */
.flash{padding:.85rem 1.25rem;border-radius:8px;margin-bottom:1.25rem;font-size:.875rem;display:flex;align-items:center;gap:.5rem}
.flash-success{background:#f0fdf4;border:1px solid #bbf7d0;color:var(--green)}
.flash-error{background:#fef2f2;border:1px solid #fecaca;color:var(--red)}

/* Cards */
.card{background:#fff;border-radius:10px;border:1px solid var(--gray-200)}
.card-header{padding:1rem 1.25rem;border-bottom:1px solid var(--gray-100);display:flex;align-items:center;justify-content:space-between}
.card-body{padding:1.25rem}
.card-title{font-size:.95rem;font-weight:600}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem}
.stat-card{background:#fff;border-radius:10px;padding:1.1rem;border:1px solid var(--gray-200);display:flex;align-items:center;gap:.85rem}
.stat-icon{width:46px;height:46px;border-radius:10px;display:grid;place-items:center;font-size:1.1rem;flex-shrink:0}
.si-teal{background:#e6f4f4;color:var(--primary)}
.si-green{background:#f0fdf4;color:var(--green)}
.si-red{background:#fef2f2;color:var(--red)}
.si-yellow{background:#fffbeb;color:var(--yellow)}
.si-blue{background:#eff6ff;color:var(--blue)}
.si-purple{background:#ede9fe;color:var(--purple)}
.stat-val{font-size:1.5rem;font-weight:700;line-height:1}
.stat-lbl{font-size:.75rem;color:var(--gray-400);margin-top:.15rem}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;border-radius:6px;font-size:.85rem;font-weight:500;cursor:pointer;border:none;text-decoration:none;transition:all .15s}
.btn-primary{background:var(--primary);color:#fff}.btn-primary:hover{background:var(--primary-dark)}
.btn-success{background:var(--green);color:#fff}.btn-success:hover{background:#15803d}
.btn-danger{background:var(--red);color:#fff}.btn-danger:hover{background:#b91c1c}
.btn-secondary{background:var(--gray-100);color:var(--gray-600);border:1px solid var(--gray-200)}.btn-secondary:hover{background:var(--gray-200)}
.btn-outline{background:transparent;border:1px solid var(--primary);color:var(--primary)}.btn-outline:hover{background:var(--primary);color:#fff}
.btn-sm{padding:.3rem .65rem;font-size:.78rem}

/* Table */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;font-size:.85rem}
th{background:var(--gray-50);padding:.7rem 1rem;text-align:left;font-weight:600;font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:var(--gray-600);border-bottom:2px solid var(--gray-200);white-space:nowrap}
td{padding:.7rem 1rem;border-bottom:1px solid var(--gray-100);vertical-align:middle}
tr:hover td{background:var(--gray-50)}

/* Status badges */
.badge{display:inline-block;padding:.2rem .65rem;border-radius:20px;font-size:.72rem;font-weight:600;white-space:nowrap}
.badge-active{background:#dcfce7;color:var(--green)}
.badge-critical{background:#fee2e2;color:var(--red)}
.badge-discharged{background:var(--gray-100);color:var(--gray-600)}
.badge-under_observation{background:#fef3c7;color:var(--yellow)}
.badge-admin{background:#ede9fe;color:var(--purple)}
.badge-doctor{background:#dbeafe;color:var(--blue)}
.badge-receptionist{background:var(--gray-100);color:var(--gray-600)}

/* Blood group */
.blood{display:inline-block;padding:.15rem .5rem;border-radius:4px;font-size:.75rem;font-weight:700;background:#fee2e2;color:var(--red)}

/* Form */
.form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem}
.form-group{display:flex;flex-direction:column;gap:.3rem}
.form-group.full{grid-column:1/-1}
label{font-size:.82rem;font-weight:500;color:var(--gray-600)}
label .req{color:var(--red)}
input,select,textarea{padding:.55rem .85rem;border:1px solid var(--gray-200);border-radius:6px;font-size:.875rem;width:100%;transition:border .15s;background:#fff;color:var(--gray-800)}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(13,110,110,.1)}
.form-error{font-size:.75rem;color:var(--red)}
input.is-invalid,select.is-invalid{border-color:var(--red)}
textarea{resize:vertical;min-height:80px}

/* Filters */
.filters{display:flex;flex-wrap:wrap;gap:.65rem;align-items:flex-end;margin-bottom:1.25rem}
.filters input,.filters select{max-width:180px}

/* Pagination */
.pagination{display:flex;align-items:center;gap:.3rem;justify-content:flex-end;margin-top:1rem}
.page-link{padding:.35rem .7rem;border:1px solid var(--gray-200);border-radius:6px;font-size:.82rem;color:var(--gray-600);text-decoration:none;transition:all .15s}
.page-link:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-link.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-link.disabled{opacity:.4;pointer-events:none}

/* Patient photo */
.patient-photo{width:38px;height:38px;border-radius:50%;object-fit:cover}
.patient-initials{width:38px;height:38px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem}

/* Detail */
.detail-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem}
.detail-item label{font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;color:var(--gray-400);font-weight:600}
.detail-item p{font-size:.92rem;margin-top:.2rem}

/* Activity */
.activity-item{display:flex;gap:.75rem;padding:.55rem 0;border-bottom:1px solid var(--gray-100);font-size:.82rem}
.activity-dot{width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:5px;flex-shrink:0}

.flex{display:flex}.gap-2{gap:.5rem}.items-center{align-items:center}.justify-between{justify-content:space-between}
.text-muted{color:var(--gray-400)}.mt-1{margin-top:.5rem}.section-divider{border:none;border-top:1px solid var(--gray-100);margin:1.25rem 0}
</style>
</head>
<body>
<?php if(Auth::check()): ?>
<?php $cp=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH); ?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <h1><i class="fa fa-hospital"></i> <span>MediCare</span> HMS</h1>
    <p>Hospital Management System</p>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Main</div>
    <a href="<?=BASE_URL?>/dashboard" class="nav-item <?=str_contains($cp,'dashboard')?'active':''?>"><i class="fa fa-gauge"></i> Dashboard</a>
    <div class="nav-label">Patients</div>
    <a href="<?=BASE_URL?>/patients" class="nav-item <?=str_contains($cp,'patients')&&!str_contains($cp,'create')?'active':''?>"><i class="fa fa-bed-pulse"></i> All Patients</a>
    <?php if(Auth::canEdit()):?>
    <a href="<?=BASE_URL?>/patients/create" class="nav-item <?=str_contains($cp,'create')?'active':''?>"><i class="fa fa-user-plus"></i> Admit Patient</a>
    <?php endif;?>
    <?php if(Auth::isDoctor()):?>
    <a href="<?=BASE_URL?>/patients/export" class="nav-item"><i class="fa fa-file-csv"></i> Export CSV</a>
    <?php endif;?>
    <div class="nav-label">Account</div>
    <a href="<?=BASE_URL?>/logout" class="nav-item"><i class="fa fa-right-from-bracket"></i> Logout</a>
  </nav>
  <div class="sidebar-footer">
    <div class="sname"><?=e(Auth::user()['name'])?></div>
    <div class="srole"><?=e(Auth::role())?> <?=Auth::user()['specialization']?'· '.e(Auth::user()['specialization']):''?></div>
  </div>
</aside>
<?php endif;?>
<div class="main">
  <header class="header">
    <div class="header-title">🏥 <?=e($pageTitle??'MediCare HMS')?></div>
    <?php if(Auth::check()):?>
    <div class="header-right">
      <span style="font-size:.8rem;color:var(--gray-400)"><i class="fa fa-circle" style="color:#22c55e;font-size:.5rem"></i> <?=e(Auth::user()['email'])?></span>
      <a href="<?=BASE_URL?>/logout" class="btn-logout"><i class="fa fa-right-from-bracket"></i> Logout</a>
    </div>
    <?php endif;?>
  </header>
  <div class="content">
    <?php $flash=getFlash(); if($flash):?>
    <div class="flash flash-<?=e($flash['type'])?>"><i class="fa fa-<?=$flash['type']==='success'?'check-circle':'exclamation-circle'?>"></i> <?=e($flash['message'])?></div>
    <?php endif;?>
