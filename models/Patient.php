<?php
// models/Patient.php
class Patient {
    private PDO $db;

    public function __construct() { $this->db = Database::getInstance(); }

    public function paginate(array $f = [], int $page = 1, int $pp = PER_PAGE): array {
        $where = ['1=1']; $params = [];

        if (!empty($f['search'])) {
            $where[]  = "MATCH(p.first_name,p.last_name,p.patient_id,p.phone,p.city) AGAINST(? IN BOOLEAN MODE)";
            $params[] = $f['search'] . '*';
        }
        if (!empty($f['status']))      { $where[] = "p.status = ?";        $params[] = $f['status']; }
        if (!empty($f['department_id'])){ $where[] = "p.department_id = ?"; $params[] = $f['department_id']; }
        if (!empty($f['gender']))      { $where[] = "p.gender = ?";         $params[] = $f['gender']; }
        if (!empty($f['blood_group'])) { $where[] = "p.blood_group = ?";    $params[] = $f['blood_group']; }
        if (!empty($f['doctor_id']))   { $where[] = "p.assigned_doctor_id = ?"; $params[] = $f['doctor_id']; }

        // Doctors only see their own patients
        if (Auth::role() === 'doctor') {
            $where[]  = "p.assigned_doctor_id = ?";
            $params[] = Auth::id();
        }

        $w     = implode(' AND ', $where);
        $sort  = in_array($f['sort'] ?? '', ['first_name','admitted_at','status','patient_id']) ? $f['sort'] : 'admitted_at';
        $order = strtoupper($f['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $off   = ($page - 1) * $pp;

        $c = $this->db->prepare("SELECT COUNT(*) FROM patients p WHERE $w");
        $c->execute($params);
        $total = (int)$c->fetchColumn();

        $params[] = $pp; $params[] = $off;
        $stmt = $this->db->prepare(
            "SELECT p.*, CONCAT(p.first_name,' ',p.last_name) as full_name,
                    d.name as dept_name, u.name as doctor_name
             FROM patients p
             LEFT JOIN departments d ON d.id = p.department_id
             LEFT JOIN users u ON u.id = p.assigned_doctor_id
             WHERE $w ORDER BY p.$sort $order LIMIT ? OFFSET ?"
        );
        $stmt->execute($params);

        return ['data'=>$stmt->fetchAll(),'total'=>$total,'per_page'=>$pp,
                'current_page'=>$page,'total_pages'=>(int)ceil($total/$pp)];
    }

    public function find(int $id): ?array {
        $s = $this->db->prepare(
            "SELECT p.*, d.name as dept_name, u.name as doctor_name, u.specialization as doctor_spec
             FROM patients p
             LEFT JOIN departments d ON d.id=p.department_id
             LEFT JOIN users u ON u.id=p.assigned_doctor_id
             WHERE p.id=?"
        );
        $s->execute([$id]); return $s->fetch() ?: null;
    }

    public function findByPatientId(string $pid): ?array {
        $s = $this->db->prepare("SELECT * FROM patients WHERE patient_id=?");
        $s->execute([$pid]); return $s->fetch() ?: null;
    }

    public function create(array $d): int {
        $pid = $this->generatePatientId();
        $s = $this->db->prepare(
            "INSERT INTO patients (patient_id,first_name,last_name,date_of_birth,gender,blood_group,
             phone,email,address,city,state,emergency_contact_name,emergency_contact_phone,
             department_id,assigned_doctor_id,status,photo,notes,created_by)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $s->execute([$pid,$d['first_name'],$d['last_name'],$d['date_of_birth'],$d['gender'],
            $d['blood_group']??'Unknown',$d['phone'],$d['email']??null,$d['address']??null,
            $d['city']??null,$d['state']??null,$d['emergency_contact_name']??null,
            $d['emergency_contact_phone']??null,$d['department_id']??null,
            $d['assigned_doctor_id']??null,$d['status']??'active',
            $d['photo']??null,$d['notes']??null,Auth::id()]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $d): void {
        $fields=[]; $params=[];
        $allowed=['first_name','last_name','date_of_birth','gender','blood_group','phone','email',
                  'address','city','state','emergency_contact_name','emergency_contact_phone',
                  'department_id','assigned_doctor_id','status','photo','notes','discharged_at'];
        foreach ($allowed as $f) {
            if (array_key_exists($f,$d)) { $fields[]="$f=?"; $params[]=$d[$f]; }
        }
        if (empty($fields)) return;
        $params[]=$id;
        $this->db->prepare("UPDATE patients SET ".implode(',',$fields)." WHERE id=?")->execute($params);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM patients WHERE id=?")->execute([$id]);
    }

    public function getAll(array $f=[]): array {
        $where=['1=1']; $params=[];
        if (!empty($f['status']))       { $where[]="status=?";       $params[]=$f['status']; }
        if (!empty($f['department_id'])){ $where[]="department_id=?";$params[]=$f['department_id']; }
        if (Auth::role()==='doctor')    { $where[]="assigned_doctor_id=?"; $params[]=Auth::id(); }
        $s=$this->db->prepare(
            "SELECT p.*,CONCAT(first_name,' ',last_name) as full_name,
             d.name as dept_name, u.name as doctor_name
             FROM patients p
             LEFT JOIN departments d ON d.id=p.department_id
             LEFT JOIN users u ON u.id=p.assigned_doctor_id
             WHERE ".implode(' AND ',$where)." ORDER BY admitted_at DESC"
        );
        $s->execute($params); return $s->fetchAll();
    }

    public function getMedicalRecords(int $patientId): array {
        $s=$this->db->prepare(
            "SELECT mr.*, u.name as doctor_name FROM medical_records mr
             LEFT JOIN users u ON u.id=mr.doctor_id
             WHERE mr.patient_id=? ORDER BY mr.visit_date DESC"
        );
        $s->execute([$patientId]); return $s->fetchAll();
    }

    public function addMedicalRecord(array $d): int {
        $s=$this->db->prepare(
            "INSERT INTO medical_records (patient_id,doctor_id,diagnosis,prescription,report_file,visit_date,next_visit_date,notes)
             VALUES (?,?,?,?,?,?,?,?)"
        );
        $s->execute([$d['patient_id'],Auth::id(),$d['diagnosis']??null,$d['prescription']??null,
            $d['report_file']??null,$d['visit_date'],
            $d['next_visit_date']??null,$d['notes']??null]);
        return (int)$this->db->lastInsertId();
    }

    public function stats(): array {
        $s=[]; $db=$this->db;
        $s['total']            = (int)$db->query("SELECT COUNT(*) FROM patients")->fetchColumn();
        $s['active']           = (int)$db->query("SELECT COUNT(*) FROM patients WHERE status='active'")->fetchColumn();
        $s['critical']         = (int)$db->query("SELECT COUNT(*) FROM patients WHERE status='critical'")->fetchColumn();
        $s['discharged']       = (int)$db->query("SELECT COUNT(*) FROM patients WHERE status='discharged'")->fetchColumn();
        $s['under_observation']= (int)$db->query("SELECT COUNT(*) FROM patients WHERE status='under_observation'")->fetchColumn();
        $s['today']            = (int)$db->query("SELECT COUNT(*) FROM patients WHERE DATE(admitted_at)=CURDATE()")->fetchColumn();
        return $s;
    }

    private function generatePatientId(): string {
        $last = $this->db->query("SELECT patient_id FROM patients ORDER BY id DESC LIMIT 1")->fetchColumn();
        $num  = $last ? ((int)substr($last, 4)) + 1 : 1;
        return 'PID-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
