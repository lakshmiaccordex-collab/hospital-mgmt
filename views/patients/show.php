<?php $pageTitle='Patient — '.e($patient['first_name'].' '.$patient['last_name']); require __DIR__.'/../partials/header.php';?>

<div class="flex justify-between items-center" style="margin-bottom:1.25rem">
  <h2 style="font-size:1.1rem;font-weight:600">🩺 Patient Record</h2>
  <div class="flex gap-2">
    <?php if(Auth::canEdit()):?>
    <a href="<?=BASE_URL?>/patients/<?=$patient['id']?>/edit" class="btn btn-outline btn-sm"><i class="fa fa-pen"></i> Edit</a>
    <?php endif;?>
    <?php if(Auth::isAdmin()):?>
    <form method="POST" action="<?=BASE_URL?>/patients/<?=$patient['id']?>/delete" onsubmit="return confirm('Permanently delete this patient record?')">
      <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
    </form>
    <?php endif;?>
    <a href="<?=BASE_URL?>/patients" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<!-- Patient Header Card -->
<div class="card" style="margin-bottom:1.25rem">
  <div class="card-body">
    <div class="flex items-center gap-2" style="flex-wrap:wrap">
      <?php if($patient['photo']):?>
      <img src="<?=asset('uploads/'.$patient['photo'])?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--gray-200)">
      <?php else:?>
      <div style="width:80px;height:80px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:700;border:3px solid var(--gray-200)">
        <?=strtoupper(substr($patient['first_name'],0,1).substr($patient['last_name'],0,1))?>
      </div>
      <?php endif;?>
      <div style="flex:1">
        <div style="font-size:1.3rem;font-weight:700"><?=e($patient['first_name'].' '.$patient['last_name'])?></div>
        <div style="color:var(--gray-400);font-size:.85rem;margin-top:.2rem">
          <?=e($patient['patient_id'])?> &nbsp;·&nbsp;
          <?=calcAge($patient['date_of_birth'])?> years &nbsp;·&nbsp;
          <?=ucfirst($patient['gender'])?>  &nbsp;·&nbsp;
          <span class="blood"><?=e($patient['blood_group'])?></span>
        </div>
      </div>
      <span class="badge badge-<?=$patient['status']?>" style="font-size:.85rem;padding:.35rem .9rem">
        <?=ucfirst(str_replace('_',' ',$patient['status']))?>
      </span>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.25rem">
  <div style="display:flex;flex-direction:column;gap:1.25rem">

    <!-- Personal Details -->
    <div class="card">
      <div class="card-header"><span class="card-title">👤 Personal Details</span></div>
      <div class="card-body">
        <div class="detail-grid">
          <div class="detail-item"><label>Phone</label><p><?=e($patient['phone'])?></p></div>
          <div class="detail-item"><label>Email</label><p><?=e($patient['email']??'—')?></p></div>
          <div class="detail-item"><label>Date of Birth</label><p><?=date('d M Y',strtotime($patient['date_of_birth']))?></p></div>
          <div class="detail-item"><label>City</label><p><?=e($patient['city']??'—')?></p></div>
          <div class="detail-item"><label>State</label><p><?=e($patient['state']??'—')?></p></div>
          <div class="detail-item"><label>Address</label><p><?=e($patient['address']??'—')?></p></div>
          <div class="detail-item"><label>Emergency Contact</label><p><?=e($patient['emergency_contact_name']??'—')?></p></div>
          <div class="detail-item"><label>Emergency Phone</label><p><?=e($patient['emergency_contact_phone']??'—')?></p></div>
        </div>
      </div>
    </div>

    <!-- Medical Records -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">📋 Medical Records (<?=count($records)?>)</span>
        <?php if(Auth::isDoctor()):?>
        <button onclick="document.getElementById('addRecordForm').style.display=document.getElementById('addRecordForm').style.display==='none'?'block':'none'" class="btn btn-primary btn-sm">
          <i class="fa fa-plus"></i> Add Record
        </button>
        <?php endif;?>
      </div>

      <?php if(Auth::isDoctor()):?>
      <!-- Add Record Form -->
      <div id="addRecordForm" style="display:none;padding:1.25rem;border-bottom:1px solid var(--gray-100);background:var(--gray-50)">
        <form method="POST" action="<?=BASE_URL?>/patients/<?=$patient['id']?>/records" enctype="multipart/form-data">
          <div class="form-grid" style="margin-bottom:1rem">
            <div class="form-group">
              <label>Visit Date <span class="req">*</span></label>
              <input type="date" name="visit_date" value="<?=date('Y-m-d')?>" required>
            </div>
            <div class="form-group">
              <label>Next Visit Date</label>
              <input type="date" name="next_visit_date">
            </div>
            <div class="form-group full">
              <label>Diagnosis</label>
              <textarea name="diagnosis" placeholder="Enter diagnosis..."></textarea>
            </div>
            <div class="form-group full">
              <label>Prescription</label>
              <textarea name="prescription" placeholder="Medicines, dosage, instructions..."></textarea>
            </div>
            <div class="form-group">
              <label>Report/Lab File</label>
              <input type="file" name="report_file" accept=".pdf,image/jpeg,image/png">
              <span style="font-size:.72rem;color:var(--gray-400)">PDF/JPG/PNG, max 5MB</span>
            </div>
            <div class="form-group full">
              <label>Notes</label>
              <textarea name="notes" placeholder="Additional notes..."></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-floppy-disk"></i> Save Record</button>
        </form>
      </div>
      <?php endif;?>

      <div class="card-body" style="padding:0">
        <?php if(empty($records)):?>
        <div style="text-align:center;padding:2rem;color:var(--gray-400)">
          <i class="fa fa-file-medical" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
          No medical records yet.
        </div>
        <?php else:?>
        <?php foreach($records as $r):?>
        <div style="padding:1.1rem 1.25rem;border-bottom:1px solid var(--gray-100)">
          <div class="flex justify-between items-center" style="margin-bottom:.6rem">
            <strong style="font-size:.9rem">📅 <?=date('d M Y',strtotime($r['visit_date']))?></strong>
            <div style="font-size:.78rem;color:var(--gray-400)">
              Dr. <?=e($r['doctor_name']??'Unknown')?>
              <?php if($r['next_visit_date']):?>
              &nbsp;·&nbsp; Next: <?=date('d M Y',strtotime($r['next_visit_date']))?>
              <?php endif;?>
            </div>
          </div>
          <?php if($r['diagnosis']):?>
          <div style="margin-bottom:.5rem"><span style="font-size:.75rem;font-weight:600;color:var(--gray-400)">DIAGNOSIS</span><p style="font-size:.875rem;margin-top:.2rem"><?=nl2br(e($r['diagnosis']))?></p></div>
          <?php endif;?>
          <?php if($r['prescription']):?>
          <div style="margin-bottom:.5rem"><span style="font-size:.75rem;font-weight:600;color:var(--gray-400)">PRESCRIPTION</span><p style="font-size:.875rem;margin-top:.2rem;white-space:pre-line"><?=e($r['prescription'])?></p></div>
          <?php endif;?>
          <?php if($r['report_file']):?>
          <a href="<?=asset('uploads/'.$r['report_file'])?>" target="_blank" class="btn btn-secondary btn-sm" style="margin-top:.4rem"><i class="fa fa-file"></i> View Report</a>
          <?php endif;?>
        </div>
        <?php endforeach;?>
        <?php endif;?>
      </div>
    </div>
  </div>

  <!-- Right sidebar -->
  <div style="display:flex;flex-direction:column;gap:1.25rem">
    <div class="card">
      <div class="card-header"><span class="card-title">🏥 Medical Info</span></div>
      <div class="card-body">
        <div style="display:flex;flex-direction:column;gap:.75rem">
          <div class="detail-item"><label>Department</label><p><?=e($patient['dept_name']??'—')?></p></div>
          <div class="detail-item"><label>Assigned Doctor</label><p><?=e($patient['doctor_name']??'—')?></p></div>
          <?php if($patient['doctor_spec']):?>
          <div class="detail-item"><label>Specialization</label><p><?=e($patient['doctor_spec'])?></p></div>
          <?php endif;?>
          <div class="detail-item"><label>Admitted On</label><p><?=date('d M Y, H:i',strtotime($patient['admitted_at']))?></p></div>
          <?php if($patient['discharged_at']):?>
          <div class="detail-item"><label>Discharged On</label><p><?=date('d M Y, H:i',strtotime($patient['discharged_at']))?></p></div>
          <?php endif;?>
        </div>
      </div>
    </div>

    <?php if($patient['notes']):?>
    <div class="card">
      <div class="card-header"><span class="card-title">📝 Notes</span></div>
      <div class="card-body"><p style="font-size:.875rem;white-space:pre-line"><?=e($patient['notes'])?></p></div>
    </div>
    <?php endif;?>
  </div>
</div>

<?php require __DIR__.'/../partials/footer.php';?>
