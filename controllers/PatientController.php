<?php
// controllers/PatientController.php
class PatientController {
    private Patient $model;
    private PDO     $db;

    public function __construct() { $this->model=new Patient(); $this->db=Database::getInstance(); }

    public function dashboard(): void {
        Auth::require();
        $stats   = $this->model->stats();
        $recent  = $this->model->paginate([],1,6)['data'];
        $depts   = $this->db->query(
            "SELECT d.name, COUNT(p.id) as count FROM departments d
             LEFT JOIN patients p ON p.department_id=d.id GROUP BY d.id ORDER BY count DESC"
        )->fetchAll();
        $activity= $this->db->query(
            "SELECT l.*,u.name as user_name FROM activity_log l
             LEFT JOIN users u ON u.id=l.user_id ORDER BY l.created_at DESC LIMIT 8"
        )->fetchAll();
        require __DIR__.'/../views/dashboard.php';
    }

    public function index(): void {
        Auth::require();
        $filters = [
            'search'       => trim($_GET['search']??''),
            'status'       => $_GET['status']??'',
            'department_id'=> $_GET['department_id']??'',
            'gender'       => $_GET['gender']??'',
            'blood_group'  => $_GET['blood_group']??'',
            'doctor_id'    => $_GET['doctor_id']??'',
            'sort'         => $_GET['sort']??'admitted_at',
            'order'        => $_GET['order']??'DESC',
        ];
        $page    = max(1,(int)($_GET['page']??1));
        $result  = $this->model->paginate($filters,$page);
        $depts   = $this->db->query("SELECT * FROM departments ORDER BY name")->fetchAll();
        $doctors = $this->db->query("SELECT * FROM users WHERE role='doctor' AND is_active=1 ORDER BY name")->fetchAll();
        require __DIR__.'/../views/patients/index.php';
    }

    public function create(): void {
        Auth::requireRole('admin','receptionist','doctor');
        $depts   = $this->db->query("SELECT * FROM departments ORDER BY name")->fetchAll();
        $doctors = $this->db->query("SELECT * FROM users WHERE role='doctor' AND is_active=1 ORDER BY name")->fetchAll();
        $errors  = $_SESSION['form_errors']??[]; $old=$_SESSION['form_old']??[];
        unset($_SESSION['form_errors'],$_SESSION['form_old']);
        require __DIR__.'/../views/patients/create.php';
    }

    public function store(): void {
        Auth::requireRole('admin','receptionist','doctor');
        $data   = array_map(fn($v)=>is_string($v)?trim($v):$v, $_POST);
        $errors = $this->validate($data);
        if ($errors) {
            $_SESSION['form_errors']=$errors; $_SESSION['form_old']=$data;
            setFlash('error','Please fix the errors below.');
            redirect(BASE_URL.'/patients/create');
        }

        if (!empty($_FILES['photo']['name'])) {
            try { $data['photo']=$this->uploadFile($_FILES['photo'],'photos',
                explode(',',Database::env('ALLOWED_PHOTO_TYPES','image/jpeg,image/png'))); }
            catch(RuntimeException $e) { setFlash('error',$e->getMessage()); redirect(BASE_URL.'/patients/create'); }
        }

        $id=$this->model->create($data);
        $this->log('create','patient',$id,"Admitted patient: {$data['first_name']} {$data['last_name']}");
        setFlash('success','Patient admitted successfully!');
        redirect(BASE_URL.'/patients');
    }

    public function show(int $id): void {
        Auth::require();
        $patient=$this->model->find($id);
        if (!$patient) { setFlash('error','Patient not found.'); redirect(BASE_URL.'/patients'); }
        $records=$this->model->getMedicalRecords($id);
        require __DIR__.'/../views/patients/show.php';
    }

    public function edit(int $id): void {
        Auth::requireRole('admin','receptionist','doctor');
        $patient=$this->model->find($id);
        if (!$patient) { setFlash('error','Patient not found.'); redirect(BASE_URL.'/patients'); }
        $depts   = $this->db->query("SELECT * FROM departments ORDER BY name")->fetchAll();
        $doctors = $this->db->query("SELECT * FROM users WHERE role='doctor' AND is_active=1 ORDER BY name")->fetchAll();
        $errors  = $_SESSION['form_errors']??[]; $old=$_SESSION['form_old']??$patient;
        unset($_SESSION['form_errors'],$_SESSION['form_old']);
        require __DIR__.'/../views/patients/edit.php';
    }

    public function update(int $id): void {
        Auth::requireRole('admin','receptionist','doctor');
        $patient=$this->model->find($id);
        if (!$patient) { setFlash('error','Patient not found.'); redirect(BASE_URL.'/patients'); }

        $data   = array_map(fn($v)=>is_string($v)?trim($v):$v,$_POST);
        $errors = $this->validate($data,$id);
        if ($errors) {
            $_SESSION['form_errors']=$errors; $_SESSION['form_old']=$data;
            setFlash('error','Please fix the errors below.');
            redirect(BASE_URL."/patients/$id/edit");
        }

        if (!empty($_FILES['photo']['name'])) {
            if ($patient['photo']) $this->deleteFile($patient['photo']);
            try { $data['photo']=$this->uploadFile($_FILES['photo'],'photos',
                explode(',',Database::env('ALLOWED_PHOTO_TYPES','image/jpeg,image/png'))); }
            catch(RuntimeException $e) { setFlash('error',$e->getMessage()); redirect(BASE_URL."/patients/$id/edit"); }
        }

        // Auto set discharged_at
        if ($data['status']==='discharged' && empty($patient['discharged_at']))
            $data['discharged_at']=date('Y-m-d H:i:s');
        if ($data['status']!=='discharged') $data['discharged_at']=null;

        $this->model->update($id,$data);
        $this->log('update','patient',$id,"Updated patient: {$data['first_name']} {$data['last_name']}");
        setFlash('success','Patient record updated successfully!');
        redirect(BASE_URL.'/patients/'.$id);
    }

    public function delete(int $id): void {
        Auth::requireRole('admin');
        $patient=$this->model->find($id);
        if (!$patient) { setFlash('error','Patient not found.'); redirect(BASE_URL.'/patients'); }
        if ($patient['photo']) $this->deleteFile($patient['photo']);
        $name=$patient['first_name'].' '.$patient['last_name'];
        $this->model->delete($id);
        $this->log('delete','patient',$id,"Deleted patient: $name");
        setFlash('success',"Patient \"$name\" record deleted.");
        redirect(BASE_URL.'/patients');
    }

    // POST /patients/:id/records — Add medical record
    public function addRecord(int $id): void {
        Auth::requireRole('admin','doctor');
        $patient=$this->model->find($id);
        if (!$patient) { setFlash('error','Patient not found.'); redirect(BASE_URL.'/patients'); }

        $data = array_map(fn($v)=>is_string($v)?trim($v):$v,$_POST);
        $data['patient_id']=$id;

        if (!empty($_FILES['report_file']['name'])) {
            $allowed=['application/pdf','image/jpeg','image/png'];
            try { $data['report_file']=$this->uploadFile($_FILES['report_file'],'reports',$allowed); }
            catch(RuntimeException $e) { setFlash('error',$e->getMessage()); redirect(BASE_URL.'/patients/'.$id); }
        }

        $this->model->addMedicalRecord($data);
        $this->log('record','patient',$id,"Added medical record for patient #{$patient['patient_id']}");
        setFlash('success','Medical record added successfully!');
        redirect(BASE_URL.'/patients/'.$id);
    }

    // GET /patients/export
    public function export(): void {
        Auth::requireRole('admin','doctor');
        $filters=['status'=>$_GET['status']??'','department_id'=>$_GET['department_id']??''];
        $patients=$this->model->getAll($filters);

        if (!is_dir(EXPORT_DIR)) mkdir(EXPORT_DIR,0755,true);
        $filename='patients_'.date('Y-m-d_His').'.csv';
        $fp=fopen(EXPORT_DIR.$filename,'w');
        fwrite($fp,"\xEF\xBB\xBF"); // BOM for Excel

        fputcsv($fp,['Patient ID','First Name','Last Name','Age','Gender','Blood Group',
            'Phone','Email','City','State','Department','Doctor','Status','Admitted On','Discharged On']);

        foreach ($patients as $p) {
            fputcsv($fp,[
                $p['patient_id'],$p['first_name'],$p['last_name'],
                calcAge($p['date_of_birth']),$p['gender'],$p['blood_group'],
                $p['phone'],$p['email']??'',$p['city']??'',$p['state']??'',
                $p['dept_name']??'',$p['doctor_name']??'',$p['status'],
                date('d M Y',strtotime($p['admitted_at'])),
                $p['discharged_at']?date('d M Y',strtotime($p['discharged_at'])):'',
            ]);
        }
        fclose($fp);
        $this->log('export','patient',null,"Exported ".count($patients)." patient records to CSV.");

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Length: '.filesize(EXPORT_DIR.$filename));
        readfile(EXPORT_DIR.$filename);
        unlink(EXPORT_DIR.$filename);
        exit;
    }

    // ── Helpers ─────────────────────────────────────────
    private function validate(array $d, int $excludeId=0): array {
        $errors=[];
        if (empty($d['first_name']))     $errors['first_name']    ='First name is required.';
        if (empty($d['last_name']))      $errors['last_name']     ='Last name is required.';
        if (empty($d['date_of_birth']))  $errors['date_of_birth'] ='Date of birth is required.';
        if (empty($d['gender']))         $errors['gender']        ='Gender is required.';
        if (empty($d['phone']))          $errors['phone']         ='Phone number is required.';
        if (!empty($d['email'])&&!filter_var($d['email'],FILTER_VALIDATE_EMAIL))
            $errors['email']='Enter a valid email address.';
        return $errors;
    }

    private function uploadFile(array $file, string $sub, array $allowed): string {
        if ($file['size']>MAX_FILE_SIZE) throw new RuntimeException('File too large. Max 5MB allowed.');
        $mime=mime_content_type($file['tmp_name']);
        if (!in_array($mime,$allowed)) throw new RuntimeException("File type not allowed.");
        $dir=UPLOAD_DIR.$sub.'/';
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $ext=pathinfo($file['name'],PATHINFO_EXTENSION);
        $name=$sub.'_'.uniqid().'.'.strtolower($ext);
        move_uploaded_file($file['tmp_name'],$dir.$name);
        return $sub.'/'.$name;
    }

    private function deleteFile(string $path): void {
        $full=UPLOAD_DIR.$path;
        if (file_exists($full)) unlink($full);
    }

    private function log(string $action, string $entity, ?int $id, string $desc): void {
        $this->db->prepare(
            "INSERT INTO activity_log (user_id,action,entity,entity_id,description) VALUES (?,?,?,?,?)"
        )->execute([Auth::id(),$action,$entity,$id,$desc]);
    }
}
