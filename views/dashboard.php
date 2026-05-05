<?php $pageTitle='Dashboard'; require __DIR__.'/../partials/header.php'; ?>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-icon si-teal"><i class="fa fa-bed-pulse"></i></div><div><div class="stat-val"><?=$stats['total']?></div><div class="stat-lbl">Total Patients</div></div></div>
  <div class="stat-card"><div class="stat-icon si-green"><i class="fa fa-user-check"></i></div><div><div class="stat-val"><?=$stats['active']?></div><div class="stat-lbl">Active</div></div></div>
  <div class="stat-card"><div class="stat-icon si-red"><i class="fa fa-heart-pulse"></i></div><div><div class="stat-val"><?=$stats['critical']?></div><div class="stat-lbl">Critical</div></div></div>
  <div class="stat-card"><div class="stat-icon si-yellow"><i class="fa fa-eye"></i></div><div><div class="stat-val"><?=$stats['under_observation']?></div><div class="stat-lbl">Under Observation</div></div></div>
  <div class="stat-card"><div class="stat-icon si-blue"><i class="fa fa-door-open"></i></div><div><div class="stat-val"><?=$stats['discharged']?></div><div class="stat-lbl">Discharged</div></div></div>
  <div class="stat-card"><div class="stat-icon si-purple"><i class="fa fa-calendar-day"></i></div><div><div class="stat-val"><?=$stats['today']?></div><div class="stat-lbl">Admitted Today</div></div></div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.25rem">

  <!-- Recent Patients -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Recent Patients</span>
      <a href="<?=BASE_URL?>/patients" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Patient</th><th>Dept</th><th>Blood</th><th>Status</th><th>Admitted</th></tr></thead>
        <tbody>
        <?php foreach($recent as $p):?>
        <tr>
          <td>
            <div class="flex items-center gap-2">
              <div class="patient-initials"><?=strtoupper(substr($p['first_name'],0,1).substr($p['last_name'],0,1))?></div>
              <div>
                <div style="font-weight:500"><?=e($p['first_name'].' '.$p['last_name'])?></div>
                <div style="font-size:.75rem;color:var(--gray-400)"><?=e($p['patient_id'])?> · <?=calcAge($p['date_of_birth'])?>y · <?=ucfirst($p['gender'])?></div>
              </div>
            </div>
          </td>
          <td style="font-size:.8rem"><?=e($p['dept_name']??'—')?></td>
          <td><span class="blood"><?=e($p['blood_group'])?></span></td>
          <td><span class="badge badge-<?=$p['status']?>"><?=ucfirst(str_replace('_',' ',$p['status']))?></span></td>
          <td style="font-size:.78rem;color:var(--gray-400)"><?=date('d M Y',strtotime($p['admitted_at']))?></td>
        </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:1.25rem">
    <!-- By Department -->
    <div class="card">
      <div class="card-header"><span class="card-title">By Department</span></div>
      <div class="card-body" style="padding:.75rem 1rem">
        <?php foreach($depts as $d):?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.4rem 0;border-bottom:1px solid var(--gray-100)">
          <span style="font-size:.85rem"><?=e($d['name'])?></span>
          <strong style="color:var(--primary)"><?=$d['count']?></strong>
        </div>
        <?php endforeach;?>
      </div>
    </div>

    <!-- Activity Log -->
    <div class="card">
      <div class="card-header"><span class="card-title">Recent Activity</span></div>
      <div class="card-body" style="padding:.75rem 1rem">
        <?php foreach($activity as $log):?>
        <div class="activity-item">
          <div class="activity-dot"></div>
          <div>
            <div><?=e($log['description'])?></div>
            <div style="font-size:.72rem;color:var(--gray-400)"><?=e($log['user_name']??'System')?> · <?=date('d M, H:i',strtotime($log['created_at']))?></div>
          </div>
        </div>
        <?php endforeach;?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__.'/../partials/footer.php';?>
