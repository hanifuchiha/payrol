	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1>Proses Payroll</h1>
			</div>
		</section>
		<section class="content">
			<div class="container-fluid">
				<?php if ($this->session->flashdata('success')): ?>
				<div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
				<?php endif; ?>
				<?php if ($this->session->flashdata('error')): ?>
				<div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
				<?php endif; ?>
				<div class="alert alert-info">Periode payroll <?php echo sprintf('%02d/%04d', $selected_month, $selected_year); ?>. Perhitungan otomatis menggunakan data bulan sebelumnya. Baris "Belum disimpan" adalah pratinjau; pilih Proses untuk menyimpan. Payroll yang sudah disimpan memakai nilai tersimpan; gunakan Edit untuk menghitung ulang.</div>
				<?php foreach ($calculation_errors as $calculation_error): ?><div class="alert alert-danger"><?php echo html_escape($calculation_error); ?></div><?php endforeach; ?>
				<form method="get" action="<?php echo site_url('payrolls'); ?>" class="form-inline mb-3">
					<label for="filter_month" class="mr-2">Bulan</label><select id="filter_month" name="month" class="form-control mr-3"><?php for ($m=1; $m<=12; $m++): ?><option value="<?php echo $m; ?>" <?php echo $m === (int) $selected_month ? 'selected' : ''; ?>><?php echo $m; ?></option><?php endfor; ?></select>
					<label for="filter_year" class="mr-2">Tahun</label><input id="filter_year" name="year" type="number" min="1900" max="9998" class="form-control mr-3" value="<?php echo (int) $selected_year; ?>"><button type="submit" class="btn btn-primary">Tampilkan</button>
				</form>
				<div class="row">
					<div class="col-md-3">
						<div class="info-box bg-info">
							<span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Periode</span>
								<span class="info-box-number"><?php echo $summary['period']; ?></span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="info-box bg-success">
							<span class="info-box-icon"><i class="fas fa-users"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Karyawan</span>
								<span class="info-box-number"><?php echo $summary['total_employee']; ?></span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="info-box bg-warning">
							<span class="info-box-icon"><i class="fas fa-wallet"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Gaji Kotor</span>
								<span class="info-box-number">Rp <?php echo number_format($summary['total_gross'], 0, ',', '.'); ?></span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="info-box bg-danger">
							<span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Sisa Gaji</span>
								<span class="info-box-number">Rp <?php echo number_format($summary['total_net'], 0, ',', '.'); ?></span>
							</div>
						</div>
					</div>
				</div>

				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Daftar Proses Payroll</h3>
						<div class="card-tools">
							<a href="<?php echo site_url('payrolls/create?month='.(int) $selected_month.'&year='.(int) $selected_year); ?>" class="btn btn-primary btn-sm">Tambah</a>
						</div>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Karyawan</th>
									<th>Periode</th>
									<th>Gaji Pokok</th>
									<th>Bonus Lainnya</th><th>Bonus Sholat</th><th>Lembur</th><th>Uang Harian</th><th>Sudah Ditransfer</th><th>Tambahan Keuangan</th><th>Potongan Keuangan</th>
									<th>Potongan</th>
										<th>Kasbon</th>
										<th>Gaji Kotor</th>
									<th>Total Potongan</th>
									<th>Sisa Gaji</th>
									<th>Status</th>
									<th width="18%">Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($payrolls)): ?><tr><td colspan="18" class="text-center">Belum ada payroll pada periode ini. Pilih Tambah untuk menghitung payroll karyawan.</td></tr><?php endif; ?>
								<?php foreach ($payrolls as $payroll): ?>
								<tr>
									<td><?php echo $payroll['employee_name']; ?></td>
									<td><?php echo str_pad($payroll['payroll_month'], 2, '0', STR_PAD_LEFT).'/'.$payroll['payroll_year']; ?></td>
									<td>Rp <?php echo number_format($payroll['base_salary'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($payroll['bonus'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($payroll['prayer_bonus'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($payroll['overtime_pay'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($payroll['daily_pay'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($payroll['weekly_daily_paid'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($payroll['finance_addition'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($payroll['finance_deduction'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($payroll['deduction'], 0, ',', '.'); ?></td>
										<td>Rp <?php echo number_format($payroll['kasbon'], 0, ',', '.'); ?></td>
										<td>Rp <?php echo number_format($payroll['gross_salary'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($payroll['total_deduction'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($payroll['net_salary'], 0, ',', '.'); ?></td>
									<td><?php echo $payroll['status']; ?></td>
									<td style="white-space: normal;">
										<?php if (empty($payroll['id'])): ?>
										<a href="<?php echo site_url('payrolls/create?month='.(int) $selected_month.'&year='.(int) $selected_year.'&employee_id='.(int) $payroll['employee_id']); ?>" class="btn btn-primary btn-sm">Proses</a>
										<?php else: ?>
<?php if ($payroll['status'] !== 'Disetujui'): ?>
										<a href="<?php echo site_url('payrolls/edit/'.$payroll['id']); ?>" class="btn btn-warning btn-sm" style="margin-bottom:4px;">Edit</a>
<?php endif; ?>
										<a href="<?php echo site_url('reports/slip_pdf/'.$payroll['id']); ?>" class="btn btn-info btn-sm" target="_blank" style="margin-bottom:4px;">PDF</a>
										<?php if ($payroll['status'] === 'Disetujui'): ?>
<form method="post" action="<?php echo site_url('payrolls/send_whatsapp/'.$payroll['id']); ?>" class="d-inline">
<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
<button type="submit" class="btn btn-success btn-sm" style="margin-bottom:4px;">Kirim WA</button>
</form>
										<?php endif; ?>
<?php if ($payroll['status'] !== 'Disetujui'): ?>
<form method="post" action="<?php echo site_url('payrolls/delete/'.$payroll['id']); ?>" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
<button type="submit" class="btn btn-danger btn-sm" style="margin-bottom:4px;">Hapus</button>
</form>
<?php endif; ?>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table><?php $paid_total = 0; foreach ($payrolls as $row) { $paid_total += (float) $row['weekly_daily_paid']; } ?>
						<div class="p-3 border-top"><table class="table table-bordered mb-0">
							<tr><th>Total Gaji</th><td class="text-right">Rp <?php echo number_format((float) $summary['total_net'] + $paid_total, 0, ',', '.'); ?></td></tr>
							<tr><th>Sudah Ditransfer</th><td class="text-right">Rp <?php echo number_format($paid_total, 0, ',', '.'); ?></td></tr>
							<tr class="table-active"><th>Yang Dibayarkan</th><td class="text-right font-weight-bold">Rp <?php echo number_format($summary['total_net'], 0, ',', '.'); ?></td></tr>
						</table></div>
					</div>
				</div>
			</div>
		</section>
	</div>
