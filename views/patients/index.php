<?php $pageTitle='Patients'; require __DIR__.'/../partials/header.php';?>

<!-- Filters -->
<div class="card" style="margin-bottom:1.25rem">
  <div class="card-body" style="padding:.85rem 1.25rem">
    <form method="GET" action="<?=BASE_URL?>/patients">
      <div class="filters">
        <div style="flex:1;min-width:200px">
          <input type="text" name="search" value="<?=e($filters['search'])?>" placeholder="🔍 Search name, ID, phone...">
        </div>
        <select name="status">
          <option value="">All Status</option>
          <?php foreach(['active','critical','under_observation','discharged'] as $s):?>
          <option value="<?=$s?>" <?=$filters['status']===$s?'selected':''?>><?=ucfirst(str_replace('_',' ',$s))?></option>
          <?php endforeach;?>
        </select>
        <select name="department_id">
          <option value="">All Departments</option>
          <?php foreach($depts as $d):?>
          <option value="<?=$d['id']?>" <?=$filters['department_id']==$d['id']?'selected':''?>><?=e($d['name'])?></option>
          <?php endforeach;?>
        </select>
        <select name="gender">
          <option value="">All Gender</option>
          <option value="male"   <?=$filters['gender']==='male'?'selected':''?>>Male</option>
          <option value="female" <?=$filters['gender']==='female'?'selected':''?>>Female</option>
          <option value="other"  <?=$filters['gender']==='other'?'selected':''?>>Other</option>
        </select>
        <select name="blood_group">
          <option value="">Blood Group</option>
          <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg):?>
          <option value="<?=$bg?>" <?=$filters['blood_group']===$bg?'selected':''?>><?=$bg?></option>
          <?php endforeach;?>
        </select>
        <?php if(Auth::isAdmin()):?>
        <select name="doctor_id">
          <option value="">All Doctors</option>
          <?php foreach($doctors as $doc):?>
          <option value="<?=$doc['id']?>" <?=$filters['doctor_id']==$doc['id']?'selected':''?>><?=e($doc['name'])?></option>
          <?php endforeach;?>
        </select>
        <?php endif;?>
        <select name="sort">
          <option value="admitted_at" <?=$filters['sort']==='admitted_at'?'selected':''?>>Sort: Admitted</option>
          <option value="first_name"  <?=$filters['sort']==='first_name'?'selected':''?>>Sort: Name</option>
          <option value="status"      <?=$filters['sort']==='status'?'selected':''?>>Sort: Status</option>
          <option value="patient_id"  <?=$filters['sort']==='patient_id'?'selected':''?>>Sort: ID</option>
        </select>
        <select name="order">
          <option value="DESC" <?=$filters['order']==='DESC'?'selected':''?>>Desc</option>
          <option value="ASC"  <?=$filters['order']==='ASC'?'selected':''?>>Asc</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
        <a href="<?=BASE_URL?>/patients" class="btn btn-secondary btn-sm"><i class="fa fa-rotate-left"></i> Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Patients <span style="font-size:.78rem;color:var(--gray-400);font-weight:400">(<?=$result['total']?> found)</span></span>
    <div class="flex gap-2">
      <?php if(Auth::isDoctor()):?>
      <a href="<?=BASE_URL?>/patients/export?<?=http_build_query(['status'=>$filters['status'],'department_id'=>$filters['department_id']])?>" class="btn btn-success btn-sm"><i class="fa fa-download"></i> Export CSV</a>
      <?php endif;?>
      <?php if(Auth::canEdit()):?>
      <a href="<?=BASE_URL?>/patients/create" class="btn btn-primary btn-sm"><i class="fa fa-user-plus"></i> Admit Patient</a>
      <?php endif;?>
    </div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>#</th><th>Patient</th><th>Age/Gender</th><th>Blood</th><th>Phone</th><th>Department</th><th>Doctor</th><th>Status</th><th>Admitted</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php if(empty($result['data'])):?>
      <tr><td colspan="10" style="text-align:center;padding:2.5rem;color:var(--gray-400)">
        <i class="fa fa-bed-pulse" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
        No patients found.
      </td></tr>
      <?php else:?>
      <?php foreach($result['data'] as $p):?>
      <tr>
        <td style="font-size:.75rem;color:var(--gray-400)"><?=e($p['patient_id'])?></td>
        <td>
          <div class="flex items-center gap-2">
            <?php if($p['photo']):?>
            <img src="<?=asset('uploads/'.$p['photo'])?>" class="patient-photo" alt="">
            <?php else:?>
            <div class="patient-initials"><?=strtoupper(substr($p['first_name'],0,1).substr($p['last_name'],0,1))?></div>
            <?php endif;?>
            <div>
              <div style="font-weight:500"><?=e($p['first_name'].' '.$p['last_name'])?></div>
              <div style="font-size:.75rem;color:var(--gray-400)"><?=e($p['email']??'')?></div>
            </div>
          </div>
        </td>
        <td><?=calcAge($p['date_of_birth'])?>y / <?=ucfirst($p['gender'])?></td>
        <td><span class="blood"><?=e($p['blood_group'])?></span></td>
        <td><?=e($p['phone'])?></td>
        <td style="font-size:.82rem"><?=e($p['dept_name']??'—')?></td>
        <td style="font-size:.82rem"><?=e($p['doctor_name']??'—')?></td>
        <td><span class="badge badge-<?=$p['status']?>"><?=ucfirst(str_replace('_',' ',$p['status']))?></span></td>
        <td style="font-size:.78rem;color:var(--gray-400)"><?=date('d M Y',strtotime($p['admitted_at']))?></td>
        <td>
          <div class="flex gap-2">
            <a href="<?=BASE_URL?>/patients/<?=$p['id']?>" class="btn btn-secondary btn-sm" title="View"><i class="fa fa-eye"></i></a>
            <?php if(Auth::canEdit()):?>
            <a href="<?=BASE_URL?>/patients/<?=$p['id']?>/edit" class="btn btn-outline btn-sm" title="Edit"><i class="fa fa-pen"></i></a>
            <?php endif;?>
            <?php if(Auth::isAdmin()):?>
            <form method="POST" action="<?=BASE_URL?>/patients/<?=$p['id']?>/delete" onsubmit="return confirm('Delete this patient record?')">
              <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
            </form>
            <?php endif;?>
          </div>
        </td>
      </tr>
      <?php endforeach;?>
      <?php endif;?>
      </tbody>
    </table>
  </div>

  <?php if($result['total_pages']>1):?>
  <div style="padding:1rem 1.25rem;border-top:1px solid var(--gray-100)">
    <?php $q=array_merge($filters); $bq=http_build_query($q);?>
    <div style="display:flex;justify-content:space-between;align-items:center">
      <span style="font-size:.82rem;color:var(--gray-400)">
        Showing <?=(($result['current_page']-1)*$result['per_page'])+1?>–<?=min($result['current_page']*$result['per_page'],$result['total'])?> of <?=$result['total']?>
      </span>
      <div class="pagination">
        <a href="?<?=$bq?>&page=<?=max(1,$result['current_page']-1)?>" class="page-link <?=$result['current_page']<=1?'disabled':''?>"><i class="fa fa-chevron-left"></i></a>
        <?php for($pg=max(1,$result['current_page']-2);$pg<=min($result['total_pages'],$result['current_page']+2);$pg++):?>
        <a href="?<?=$bq?>&page=<?=$pg?>" class="page-link <?=$pg===$result['current_page']?'active':''?>"><?=$pg?></a>
        <?php endfor;?>
        <a href="?<?=$bq?>&page=<?=min($result['total_pages'],$result['current_page']+1)?>" class="page-link <?=$result['current_page']>=$result['total_pages']?'disabled':''?>"><i class="fa fa-chevron-right"></i></a>
      </div>
    </div>
  </div>
  <?php endif;?>
</div>

<?php require __DIR__.'/../partials/footer.php';?>
