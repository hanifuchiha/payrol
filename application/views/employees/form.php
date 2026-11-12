	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1><?php echo $page_title; ?></h1>
			</div>
		</section>
		<section class="content">
			<div class="container-fluid">
				<?php if ($this->session->flashdata('error')): ?>
				<div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
				<?php endif; ?>
				<div class="card">
					<div class="card-body">
						<p class="text-muted">Semua kolom opsional. Nominal yang kosong disimpan sebagai Rp0.</p>
						<form method="post" action="<?php echo site_url($action); ?>">
								<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
							<div class="form-group">
								<label>NIK</label>
								<input type="text" name="nik" class="form-control" value="<?php echo set_value('nik', isset($employee['nik']) ? $employee['nik'] : ''); ?>">
							</div>
							<div class="form-group">
								<label>Nama</label>
								<input type="text" name="name" class="form-control" value="<?php echo set_value('name', isset($employee['name']) ? $employee['name'] : ''); ?>">
							</div>
							<div class="form-group">
								<label>Nomor Telepon</label>
								<input type="text" name="phone" class="form-control" value="<?php echo set_value('phone', isset($employee['phone']) ? $employee['phone'] : ''); ?>">
							</div>
							<div class="form-group">
								<label>NPWP</label>
								<input type="text" name="npwp" class="form-control" value="<?php echo set_value('npwp', isset($employee['npwp']) ? $employee['npwp'] : ''); ?>">
							</div>
							<div class="form-group">
								<label>BPJS</label>
								<input type="text" name="bpjs" class="form-control" value="<?php echo set_value('bpjs', isset($employee['bpjs']) ? $employee['bpjs'] : ''); ?>">
							</div>
								<div class="form-group">
									<label>Divisi & Jabatan</label>
									<select name="department_id" class="form-control">
									<option value="">Pilih</option>
									<?php foreach ($departments as $department): ?>
									<option value="<?php echo $department['id']; ?>" <?php echo set_select('department_id', $department['id'], isset($employee['department_id']) && (int) $employee['department_id'] === (int) $department['id']); ?>>
										<?php echo (strtolower(trim((string) $department['division_name'])) === 'absensi' ? 'Operasional' : $department['division_name']).' - '.$department['position_name']; ?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<label>Uang Harian per Hari (Rp)</label>
								<input type="number" name="daily_allowance" min="0" step="1" class="form-control" value="<?php echo html_escape(set_value('daily_allowance', isset($employee['daily_allowance']) ? $employee['daily_allowance'] : '')); ?>">
								<small class="form-text text-muted">Dikalikan jumlah hari hadir bulan sebelumnya secara otomatis di payroll.</small>
							</div>
							<div class="form-group">
								<label>Gaji Pokok</label>
								<?php $base_salary_value = set_value('base_salary', isset($employee['base_salary']) ? $employee['base_salary'] : ''); ?>
								<input type="text" id="base_salary_display" class="form-control" value="" inputmode="numeric" autocomplete="off">
								<input type="hidden" name="base_salary" id="base_salary" value="<?php echo html_escape($base_salary_value); ?>">
							</div>
								<div class="form-group">
									<label>Status Kerja</label>
									<select name="employment_status" id="employment_status" class="form-control">
										<option value="">Belum diisi</option>
										<option value="Tetap" <?php echo set_select('employment_status', 'Tetap', isset($employee['employment_status']) && $employee['employment_status'] === 'Tetap'); ?>>Tetap</option>
										<option value="Kontrak" <?php echo set_select('employment_status', 'Kontrak', isset($employee['employment_status']) && $employee['employment_status'] === 'Kontrak'); ?>>Kontrak</option>
                                        <option value="PKL" <?php echo set_select('employment_status', 'PKL', isset($employee['employment_status']) && $employee['employment_status'] === 'PKL'); ?>>PKL</option>
									</select>
								</div>
							<div class="form-group">
								<label>Tanggal Masuk</label>
								<input type="date" name="join_date" class="form-control" value="<?php echo set_value('join_date', isset($employee['join_date']) ? $employee['join_date'] : ''); ?>">
							</div>
							<div id="contract-fields" style="display:none;">
								<div class="form-group">
									<label>Tanggal Awal Kontrak</label>
									<input type="date" name="contract_start_date" id="contract_start_date" class="form-control" value="<?php echo set_value('contract_start_date', isset($employee['contract_start_date']) ? $employee['contract_start_date'] : ''); ?>">
								</div>
								<div class="form-group">
									<label>Tanggal Akhir Kontrak</label>
									<input type="date" name="contract_end_date" id="contract_end_date" class="form-control" value="<?php echo set_value('contract_end_date', isset($employee['contract_end_date']) ? $employee['contract_end_date'] : ''); ?>">
								</div>
								<div class="form-group">
									<label>Sisa Kontrak</label>
									<input type="text" id="contract_remaining" class="form-control" value="<?php echo isset($employee['contract_remaining_text']) ? html_escape($employee['contract_remaining_text']) : '-'; ?>" readonly>
								</div>
							</div>
								<button type="submit" class="btn btn-primary">Simpan</button>
								<a href="<?php echo site_url('employees'); ?>" class="btn btn-secondary">Kembali</a>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var baseSalaryInput = document.getElementById('base_salary');
	var baseSalaryDisplay = document.getElementById('base_salary_display');
	var statusInput = document.getElementById('employment_status');
	var contractFields = document.getElementById('contract-fields');
	var contractStartInput = document.getElementById('contract_start_date');
	var contractEndInput = document.getElementById('contract_end_date');
	var contractRemainingInput = document.getElementById('contract_remaining');

	if (!baseSalaryInput || !baseSalaryDisplay) {
		return;
	}

	function digitsOnly(value) {
		return (value || '').replace(/\D+/g, '');
	}

	function numericString(value) {
		var raw = String(value || '').trim();
		if (!raw) {
			return '';
		}
		if (/^\d+(\.\d+)?$/.test(raw)) {
			return String(Math.round(parseFloat(raw)));
		}
		return digitsOnly(raw);
	}

	function formatRupiah(value) {
		var digits = numericString(value);
		if (!digits) {
			return '';
		}

		return 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(digits, 10));
	}

	function syncDisplayFromHidden() {
		baseSalaryDisplay.value = formatRupiah(baseSalaryInput.value);
	}

	baseSalaryDisplay.addEventListener('input', function () {
		baseSalaryInput.value = numericString(baseSalaryDisplay.value);
		syncDisplayFromHidden();
	});

	baseSalaryDisplay.addEventListener('blur', syncDisplayFromHidden);

	syncDisplayFromHidden();

	function formatRemainingText(startValue, endValue) {
		if (!startValue || !endValue) {
			return '-';
		}

		var start = new Date(startValue + 'T00:00:00');
		var end = new Date(endValue + 'T00:00:00');
		var today = new Date();
		today.setHours(0, 0, 0, 0);

		if (isNaN(start.getTime()) || isNaN(end.getTime()) || start > end) {
			return '-';
		}
		if (end < today) {
			return 'Habis';
		}

		var diffMs = end.getTime() - today.getTime();
		var diffDays = Math.floor(diffMs / 86400000);
		if (diffDays === 0) {
			return 'Hari ini';
		}

		var months = (end.getFullYear() - today.getFullYear()) * 12 + (end.getMonth() - today.getMonth());
		var anchor = new Date(today.getTime());
		anchor.setMonth(anchor.getMonth() + months);
		if (anchor > end) {
			months -= 1;
			anchor = new Date(today.getTime());
			anchor.setMonth(anchor.getMonth() + months);
		}
		var remainDays = Math.floor((end.getTime() - anchor.getTime()) / 86400000);
		var parts = [];
		if (months > 0) {
			parts.push(months + ' bulan');
		}
		if (remainDays > 0) {
			parts.push(remainDays + ' hari');
		}
		if (!parts.length) {
			parts.push(diffDays + ' hari');
		}

		return parts.join(' ') + ' lagi';
	}

	function syncContractFields() {
		if (!statusInput || !contractFields) {
			return;
		}
		var isContract = statusInput.value === 'Kontrak';
		contractFields.style.display = isContract ? '' : 'none';
		if (!isContract) {
			if (contractStartInput) contractStartInput.value = '';
			if (contractEndInput) contractEndInput.value = '';
			if (contractRemainingInput) contractRemainingInput.value = '-';
			return;
		}
		if (contractRemainingInput) {
			contractRemainingInput.value = formatRemainingText(
				contractStartInput ? contractStartInput.value : '',
				contractEndInput ? contractEndInput.value : ''
			);
		}
	}

	if (statusInput) {
		statusInput.addEventListener('change', syncContractFields);
	}
	if (contractStartInput) {
		contractStartInput.addEventListener('change', syncContractFields);
	}
	if (contractEndInput) {
		contractEndInput.addEventListener('change', syncContractFields);
	}

	syncContractFields();
});
</script>
