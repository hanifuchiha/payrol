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
							<form method="post" action="<?php echo site_url($action); ?>">
								<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
								<?php $current_payroll_id = isset($payroll['id']) ? (int) $payroll['id'] : 0; ?>
								<div class="form-group">
									<label>Karyawan</label>
									<select name="employee_id" class="form-control">
									<option value="">Pilih</option>
									<?php foreach ($employees as $employee): ?>
									<option value="<?php echo $employee['id']; ?>" data-base-salary="<?php echo $employee['base_salary']; ?>" <?php echo set_select('employee_id', $employee['id'], (int) ($payroll['employee_id'] ?? $default_employee ?? 0) === (int) $employee['id']); ?>>
										<?php echo $employee['nik'].' - '.$employee['name']; ?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label>Bulan</label>
									<input type="number" name="payroll_month" min="1" max="12" class="form-control" value="<?php echo set_value('payroll_month', isset($payroll['payroll_month']) ? $payroll['payroll_month'] : ($default_month ?? date('n'))); ?>">
								</div>
								<div class="form-group col-md-6">
									<label>Tahun</label>
									<input type="number" name="payroll_year" class="form-control" value="<?php echo set_value('payroll_year', isset($payroll['payroll_year']) ? $payroll['payroll_year'] : ($default_year ?? date('Y'))); ?>">
								</div>
							</div>
							<div class="form-group">
								<label>Gaji Pokok</label>
								<?php
								$base_salary_value = '';
								$selected_employee_id = set_value('employee_id', isset($payroll['employee_id']) ? $payroll['employee_id'] : ($default_employee ?? ''));
								foreach ($employees as $salary_employee) {
									if ((string) $salary_employee['id'] === (string) $selected_employee_id) {
										$base_salary_value = $salary_employee['base_salary'];
										break;
									}
								}
								?>
								<input type="text" id="base_salary_display" class="form-control" value="<?php echo $base_salary_value === '' ? '' : 'Rp '.number_format((float) $base_salary_value, 0, ',', '.'); ?>" readonly>
								<input type="hidden" name="base_salary" id="base_salary" class="calc-payroll" value="<?php echo html_escape($base_salary_value); ?>">
								<small class="form-text text-muted">Otomatis dari Gaji Pokok di Data Karyawan. Ubah gaji melalui menu Data Karyawan.</small>
							</div>
							<div class="form-group">
								<label>Bonus Lainnya</label>
								<?php $bonus_value = set_value('bonus', isset($payroll['bonus']) ? $payroll['bonus'] : '0'); ?>
								<input type="text" id="bonus_display" class="form-control calc-payroll-display" value="" inputmode="numeric" autocomplete="off">
								<input type="hidden" name="bonus" id="bonus" class="calc-payroll" value="<?php echo html_escape($bonus_value); ?>">
							</div>
							<div class="form-group">
								<label>Bonus Sholat Otomatis</label>
								<input type="text" id="prayer_bonus_display" class="form-control" readonly>
								<small id="prayer_bonus_detail" class="form-text text-muted" role="status">Pilih karyawan dan periode payroll.</small>
							</div>
							<div class="form-group"><label>Uang Lembur (ACC Tahap 2)</label><input type="text" id="overtime_pay_display" class="form-control" readonly><small id="overtime_detail" class="form-text text-muted"></small></div>
							<div class="form-group"><label>Uang Harian</label><input type="text" id="daily_pay_display" class="form-control" readonly><small id="daily_detail" class="form-text text-muted"></small></div>
							
							<div class="form-group"><label>Transaksi Keuangan Karyawan</label><div id="finance_detail" class="table-responsive"></div><small class="form-text text-muted">Bulan sebelumnya. Pengeluaran menambah penerimaan; pemasukan menjadi potongan. Pembayaran gaji seluruh karyawan dikecualikan.</small></div>

							<div class="form-group">
								<label>Potongan</label>
								<?php $deduction_value = set_value('deduction', isset($payroll['deduction']) ? $payroll['deduction'] : '0'); ?>
								<input type="text" id="deduction_display" class="form-control calc-payroll-display" value="" inputmode="numeric" autocomplete="off">
								<input type="hidden" name="deduction" id="deduction" class="calc-payroll" value="<?php echo html_escape($deduction_value); ?>">
							</div>
								<div class="form-group">
									<label>Cicilan Kasbon</label>
									<?php $kasbon_value = set_value('kasbon', isset($payroll['kasbon']) ? $payroll['kasbon'] : '0'); ?>
									<input type="text" id="kasbon_display" class="form-control calc-payroll-display" value="" inputmode="numeric" autocomplete="off">
									<input type="hidden" name="kasbon" id="kasbon" value="<?php echo html_escape($kasbon_value); ?>">
									<small class="form-text text-muted">Isi jumlah cicilan kasbon yang ingin dipotong pada payroll ini.</small>
								</div>
								<div class="form-row">
									<div class="form-group col-md-4">
										<label>Total Hutang Kasbon</label>
										<input type="text" id="kasbon_total_preview" class="form-control" value="" readonly>
									</div>
									<div class="form-group col-md-4">
										<label>Sudah Dibayar</label>
										<input type="text" id="kasbon_paid_preview" class="form-control" value="" readonly>
									</div>
									<div class="form-group col-md-4">
										<label>Sisa Hutang Kasbon</label>
										<input type="text" id="kasbon_remaining_preview" class="form-control" value="" readonly>
									</div>
								</div>
							<div class="border rounded p-3 mb-3 bg-light">
								<div class="form-group"><label>Total Gaji</label><input type="text" id="gross_salary_preview" class="form-control" readonly></div>
								<div class="form-group"><label>Sudah Ditransfer</label><input type="text" id="weekly_daily_paid_display" class="form-control" readonly></div>
								<div class="form-group mb-0"><label>Yang Dibayarkan</label><input type="text" id="net_salary_preview" class="form-control font-weight-bold" readonly></div>
							</div>
							<div class="form-group">
								<label>Status</label>
								<select name="status" class="form-control">
									<option value="Draft" <?php echo set_select('status', 'Draft', isset($payroll['status']) && $payroll['status'] === 'Draft'); ?>>Draft</option>
									<option value="Diproses" <?php echo set_select('status', 'Diproses', isset($payroll['status']) && $payroll['status'] === 'Diproses'); ?>>Diproses</option>
									<option value="Disetujui" <?php echo set_select('status', 'Disetujui', isset($payroll['status']) && $payroll['status'] === 'Disetujui'); ?>>Disetujui</option>
								</select>
							</div>
							<button type="submit" class="btn btn-primary">Simpan</button>
							<a href="<?php echo site_url('payrolls'); ?>" class="btn btn-secondary">Kembali</a>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var prayerAmount = 0;
	var overtimeAmount = 0;
	var dailyAmount = 0;
	var financeAddition = 0;
	var financeDeduction = 0;
	var prayerRequest = 0;
	var prayerDisplay = document.getElementById('prayer_bonus_display');
	var prayerDetail = document.getElementById('prayer_bonus_detail');
	var submitButton = document.querySelector('form button[type="submit"]');
	var employeeSelect = document.querySelector('select[name="employee_id"]');
	var monthInput = document.querySelector('input[name="payroll_month"]');
	var yearInput = document.querySelector('input[name="payroll_year"]');
	var baseSalaryInput = document.getElementById('base_salary');
	var bonusInput = document.getElementById('bonus');
	var deductionInput = document.getElementById('deduction');
		var kasbonInput = document.getElementById('kasbon');
		var baseSalaryDisplay = document.getElementById('base_salary_display');
		var bonusDisplay = document.getElementById('bonus_display');
		var deductionDisplay = document.getElementById('deduction_display');
		var kasbonDisplay = document.getElementById('kasbon_display');
		var kasbonTotalPreview = document.getElementById('kasbon_total_preview');
		var kasbonPaidPreview = document.getElementById('kasbon_paid_preview');
		var kasbonRemainingPreview = document.getElementById('kasbon_remaining_preview');
		var grossPreview = document.getElementById('gross_salary_preview');
		var netPreview = document.getElementById('net_salary_preview');
		var currentKasbonRemaining = <?php echo json_encode((float) ($current_kasbon_remaining ?? 0)); ?>;
		var currentPayrollId = <?php echo (int) $current_payroll_id; ?>;

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

		function numberValue(input) {
			return parseFloat(input.value || 0) || 0;
		}

		function updateKasbonSummary(total, paid, remaining) {
			if (kasbonTotalPreview) {
				kasbonTotalPreview.value = formatRupiah(String(total || 0));
			}
			if (kasbonPaidPreview) {
				kasbonPaidPreview.value = formatRupiah(String(paid || 0));
			}
			if (kasbonRemainingPreview) {
				var currentInstallment = numberValue(kasbonInput);
				var nextRemaining = Math.max(0, (remaining || 0) - currentInstallment);
				kasbonRemainingPreview.value = formatRupiah(String(nextRemaining));
			}
		}

	function syncDisplay(displayInput, hiddenInput) {
		displayInput.value = formatRupiah(hiddenInput.value);
	}

			function bindCurrencyInput(displayInput, hiddenInput) {
				displayInput.addEventListener('input', function () {
					if (/-/.test(displayInput.value)) {
						displayInput.setCustomValidity('Nominal tidak boleh negatif.');
						return;
					}
					displayInput.setCustomValidity('');
					hiddenInput.value = numericString(displayInput.value);
					if (hiddenInput === kasbonInput) {
						var maxKasbon = parseFloat(window.kasbonMetaRemaining || currentKasbonRemaining || 0) || 0;
						if (numberValue(hiddenInput) > maxKasbon) {
						hiddenInput.value = String(maxKasbon);
					}
				}
				syncDisplay(displayInput, hiddenInput);
				updatePreview();
			});

		displayInput.addEventListener('blur', function () {
			syncDisplay(displayInput, hiddenInput);
		});

		syncDisplay(displayInput, hiddenInput);
	}

		function updatePreview() {
			var gross = numberValue(baseSalaryInput) + numberValue(bonusInput) + prayerAmount + overtimeAmount + dailyAmount + financeAddition;
			var net = gross - numberValue(deductionInput) - numberValue(kasbonInput) - financeDeduction - dailyAmount;
			grossPreview.value = formatRupiah(String(net + dailyAmount));
			netPreview.value = formatRupiah(String(net));
			syncDisplay(kasbonDisplay, kasbonInput);
			updateKasbonSummary(window.kasbonMetaTotal || 0, window.kasbonMetaPaid || 0, window.kasbonMetaRemaining || currentKasbonRemaining || 0);
		}

		function updateKasbon() {
			var employeeId = employeeSelect.value;

			if (!employeeId) {
				window.kasbonMetaTotal = 0;
				window.kasbonMetaPaid = 0;
				window.kasbonMetaRemaining = 0;
				kasbonInput.value = 0;
				updatePreview();
				return;
			}

			fetch('<?php echo site_url('payrolls/kasbon_total'); ?>?employee_id=' + employeeId + '&payroll_id=' + currentPayrollId)
				.then(function (response) { return response.json(); })
				.then(function (data) {
					window.kasbonMetaTotal = parseFloat(data.total || 0) || 0;
					window.kasbonMetaPaid = parseFloat(data.paid || 0) || 0;
					window.kasbonMetaRemaining = parseFloat(data.remaining || 0) || 0;
					if (numberValue(kasbonInput) > window.kasbonMetaRemaining) {
						kasbonInput.value = window.kasbonMetaRemaining;
					}
					updatePreview();
				});
		}

		function updatePrayerBonus() {
			var requestId = ++prayerRequest;
			prayerAmount = overtimeAmount = dailyAmount = financeAddition = financeDeduction = 0;
			document.getElementById('overtime_pay_display').value = formatRupiah('0');
			document.getElementById('daily_pay_display').value = formatRupiah('0');
			document.getElementById('weekly_daily_paid_display').value = formatRupiah('0');
			document.getElementById('overtime_detail').textContent = '';
			document.getElementById('daily_detail').textContent = '';
			document.getElementById('finance_detail').textContent = '';
			prayerDisplay.value = formatRupiah('0');
			updatePreview();
			if (!employeeSelect.value) {
				prayerDetail.textContent = 'Pilih karyawan dan periode payroll.';
				submitButton.disabled = true;
				return;
			}
			submitButton.disabled = true;
			prayerDetail.textContent = 'Menghitung bonus, lembur, uang harian, dan transaksi keuangan...';
			fetch('<?php echo site_url('payrolls/prayer_bonus'); ?>?employee_id=' + encodeURIComponent(employeeSelect.value) + '&month=' + encodeURIComponent(monthInput.value) + '&year=' + encodeURIComponent(yearInput.value))
				.then(function (response) {
					return response.json().then(function (data) {
						if (!response.ok || data.error) throw new Error(data.error || 'Gagal memuat bonus sholat.');
						return data;
					});
				})
				.then(function (data) {
					if (requestId !== prayerRequest) return;
					prayerAmount = Number(data.amount) || 0;
					prayerDisplay.value = formatRupiah(String(prayerAmount));
					prayerDetail.textContent = 'Absen ' + data.period + (data.rank ? ' | Peringkat ' + data.rank + ' | ' + data.count + ' × Rp5.000' : ' | Tidak masuk 3 terbanyak, bonus Rp0.');
					var attendance = data.attendance;
					overtimeAmount = Number(attendance.overtime_pay) || 0;
					dailyAmount = Number(attendance.daily_pay) || 0;
					financeAddition = Number(data.finance.addition) || 0;
					financeDeduction = Number(data.finance.deduction) || 0;
					document.getElementById('overtime_pay_display').value = formatRupiah(String(overtimeAmount));
					document.getElementById('daily_pay_display').value = formatRupiah(String(dailyAmount));
					document.getElementById('weekly_daily_paid_display').value = formatRupiah(String(dailyAmount));
					document.getElementById('overtime_detail').textContent = attendance.overtime_days + ' hari, ' + attendance.overtime_minutes + ' menit | Rp10.000/jam, maksimal Rp50.000 per hari | ' + attendance.period;
					document.getElementById('daily_detail').textContent = attendance.attendance_days + ' hari hadir × ' + formatRupiah(String(attendance.daily_rate)) + ' | ' + attendance.period + (attendance.matched ? '' : ' | Belum ditemukan pasangan karyawan di gudang.');
					var detail = document.getElementById('finance_detail');
					detail.textContent = '';
					if (!data.finance.items.length) detail.textContent = 'Tidak ada transaksi keuangan pada periode ini.';
					data.finance.items.forEach(function (item) {
						var line = document.createElement('div');
						line.textContent = item.date + ' | ' + item.description + ' | ' + (item.effect === 'addition' ? '+' : '−') + formatRupiah(String(item.amount));
						detail.appendChild(line);
					});
					submitButton.disabled = false;
					updatePreview();
				})
				.catch(function (error) {
					if (requestId !== prayerRequest) return;
					prayerDetail.textContent = error.message || 'Gagal memuat bonus sholat. Silakan coba lagi.';
				});
		}

		if (employeeSelect) {
			employeeSelect.addEventListener('change', function () {
				var option = employeeSelect.options[employeeSelect.selectedIndex];
				baseSalaryInput.value = option && option.dataset.baseSalary ? numericString(option.dataset.baseSalary) : '';
				syncDisplay(baseSalaryDisplay, baseSalaryInput);
				updateKasbon();
				updatePrayerBonus();
			});
		}

		syncDisplay(baseSalaryDisplay, baseSalaryInput);
		bindCurrencyInput(bonusDisplay, bonusInput);
		bindCurrencyInput(deductionDisplay, deductionInput);
		bindCurrencyInput(kasbonDisplay, kasbonInput);

		[monthInput, yearInput].forEach(function (input) {
			input.addEventListener('input', updatePrayerBonus);
		});

		syncDisplay(kasbonDisplay, kasbonInput);
		updateKasbon();
		updatePrayerBonus();
});
</script>
