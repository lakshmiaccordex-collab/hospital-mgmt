<?php
// index.php — Main Router
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/middleware/Auth.php';
require_once __DIR__ . '/models/Patient.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/PatientController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim(preg_replace('#/hospital-mgmt#', '', $uri), '/') ?: '/';
$parts  = explode('/', trim($uri, '/'));

$auth    = new AuthController();
$patient = new PatientController();

// ── AUTH ROUTES ───────────────────────────────────────────
if ($uri === '/' || $uri === '/login') {
    $method === 'POST' ? $auth->login() : $auth->loginForm();
    exit;
}
if ($uri === '/logout') { $auth->logout(); exit; }

// ── DASHBOARD ────────────────────────────────────────────
if ($uri === '/dashboard') { $patient->dashboard(); exit; }

// ── PATIENT ROUTES ────────────────────────────────────────
if ($parts[0] === 'patients') {
    $seg    = $parts[1] ?? null;
    $action = $parts[2] ?? null;

    // /patients/:id/records — Add medical record
    if ($seg && is_numeric($seg) && $action === 'records' && $method === 'POST') {
        $patient->addRecord((int)$seg); exit;
    }
    // /patients/:id/update
    if ($seg && is_numeric($seg) && $action === 'update' && $method === 'POST') {
        $patient->update((int)$seg); exit;
    }
    // /patients/:id/delete
    if ($seg && is_numeric($seg) && $action === 'delete' && $method === 'POST') {
        $patient->delete((int)$seg); exit;
    }
    // /patients/export
    if ($seg === 'export') { $patient->export(); exit; }
    // /patients/create
    if ($seg === 'create' && $method === 'GET') { $patient->create(); exit; }
    // /patients/store
    if ($seg === 'store' && $method === 'POST') { $patient->store(); exit; }
    // /patients/:id/edit
    if ($seg && is_numeric($seg) && $action === 'edit') { $patient->edit((int)$seg); exit; }
    // /patients/:id
    if ($seg && is_numeric($seg)) { $patient->show((int)$seg); exit; }
    // /patients
    $patient->index(); exit;
}

// ── FALLBACK ─────────────────────────────────────────────
redirect(BASE_URL . '/dashboard');
