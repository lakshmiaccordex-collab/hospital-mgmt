<?php $pageTitle='Admit New Patient'; require __DIR__.'/../partials/header.php';?>

<div style="max-width:900px">
  <div class="flex justify-between items-center" style="margin-bottom:1.25rem">
    <h2 style="font-size:1.1rem;font-weight:600">🏥 Admit New Patient</h2>
    <a href="<?=BASE_URL?>/patients" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
  </div>

  <form method="POST" action="<?=BASE_URL?>/patients/store" enctype="multipart/form-data">

    <!-- Personal Information -->
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><span class="card-title">👤 Personal Information</span></div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-group">
            <label>First Name <span class="req">*</span></label>
            <input type="text" name="first_name" value="<?=e($old['first_name']??'')?>" class="<?=isset($errors['first_name'])?'is-invalid':''?>" required>
            <?php if(isset($errors['first_name'])):?><span class="form-error"><?=e($errors['first_name'])?></span><?php endif;?>
          </div>
          <div class="form-group">
            <label>Last Name <span class="req">*</span></label>
            <input type="text" name="last_name" value="<?=e($old['last_name']??'')?>" class="<?=isset($errors['last_name'])?'is-invalid':''?>" required>
            <?php if(isset($errors['last_name'])):?><span class="form-error"><?=e($errors['last_name'])?></span><?php endif;?>
          </div>
          <div class="form-group">
            <label>Date of Birth <span class="req">*</span></label>
            <input type="date" name="date_of_birth" value="<?=e($old['date_of_birth']??'')?>" class="<?=isset($errors['date_of_birth'])?'is-invalid':''?>" required>
          </div>
          <div class="form-group">
            <label>Gender <span class="req">*</span></label>
            <select name="gender" class="<?=isset($errors['gender'])?'is-invalid':''?>" required>
              <option value="">Select Gender</option>
              <?php foreach(['male'=>'Male','female'=>'Female','other'=>'Other'] as $v=>$l):?>
              <option value="<?=$v?>" <?=($old['gender']??'')===$v?'selected':''?>><?=$l?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label>Blood Group</label>
            <select name="blood_group">
              <?php foreach(['Unknown','A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg):?>
              <option value="<?=$bg?>" <?=($old['blood_group']??'Unknown')===$bg?'selected':''?>><?=$bg?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
            <span style="font-size:.72rem;color:var(--gray-400)">JPG/PNG/WEBP, max 5MB</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Information -->
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><span class="card-title">📞 Contact Information</span></div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-group">
            <label>Phone <span class="req">*</span></label>
            <input type="tel" name="phone" value="<?=e($old['phone']??'')?>" class="<?=isset($errors['phone'])?'is-invalid':''?>" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?=e($old['email']??'')?>" class="<?=isset($errors['email'])?'is-invalid':''?>">
            <?php if(isset($errors['email'])):?><span class="form-error"><?=e($errors['email'])?></span><?php endif;?>
          </div>
          <div class="form-group">
            <label>City</label>
            <input type="text" name="city" value="<?=e($old['city']??'')?>">
          </div>
          <div class="form-group">
            <label>State</label>
            <input type="text" name="state" value="<?=e($old['state']??'')?>">
          </div>
          <div class="form-group full">
            <label>Address</label>
            <textarea name="address"><?=e($old['address']??'')?></textarea>
          </div>
          <div class="form-group">
            <label>Emergency Contact Name</label>
            <input type="text" name="emergency_contact_name" value="<?=e($old['emergency_contact_name']??'')?>">
          </div>
          <div class="form-group">
            <label>Emergency Contact Phone</label>
            <input type="tel" name="emergency_contact_phone" value="<?=e($old['emergency_contact_phone']??'')?>">
          </div>
        </div>
      </div>
    </div>

    <!-- Medical Information -->
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><span class="card-title">🩺 Medical Information</span></div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-group">
            <label>Department</label>
            <select name="department_id">
              <option value="">Select Department</option>
              <?php foreach($depts as $d):?>
              <option value="<?=$d['id']?>" <?=($old['department_id']??'')==$d['id']?'selected':''?>><?=e($d['name'])?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label>Assigned Doctor</label>
            <select name="assigned_doctor_id">
              <option value="">Select Doctor</option>
              <?php foreach($doctors as $doc):?>
              <option value="<?=$doc['id']?>" <?=($old['assigned_doctor_id']??'')==$doc['id']?'selected':''?>><?=e($doc['name'])?> <?=$doc['specialization']?'('.$doc['specialization'].')':''?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status">
              <?php foreach(['active'=>'Active','critical'=>'Critical','under_observation'=>'Under Observation'] as $v=>$l):?>
              <option value="<?=$v?>" <?=($old['status']??'active')===$v?'selected':''?>><?=$l?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="form-group full">
            <label>Notes</label>
            <textarea name="notes" placeholder="Initial diagnosis, symptoms, allergies..."><?=e($old['notes']??'')?></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus"></i> Admit Patient</button>
      <a href="<?=BASE_URL?>/patients" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__.'/../partials/footer.php';?>
