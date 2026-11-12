	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1>Slip Gaji</h1>
			</div>
		</section>
		<section class="content">
			<div class="container-fluid">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Slip Gaji <?php echo $slip['employee_name']; ?></h3>
						<div class="card-tools">
							<button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">Print</button>
						</div>
					</div>
					<div class="card-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<strong>NIK:</strong> <?php echo $slip['nik']; ?><br>
								<strong>Nama:</strong> <?php echo $slip['employee_name']; ?><br>
								<strong>Divisi:</strong> <?php echo strtolower(trim((string) $slip['division_name'])) === 'absensi' ? 'Operasional' : $slip['division_name']; ?><br>
								<strong>Jabatan:</strong> <?php echo $slip['position_name']; ?>
							</div>
							<div class="col-md-6">
								<strong>Periode:</strong> <?php echo str_pad($slip['payroll_month'], 2, '0', STR_PAD_LEFT).'/'.$slip['payroll_year']; ?><br>
								<strong>Status:</strong> <?php echo $slip['status']; ?><br>
								<strong>Status Kerja:</strong> <?php echo $slip['employment_status']; ?>
							</div>
						</div>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Komponen</th>
									<th class="text-right">Nominal</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Gaji Pokok</td>
									<td class="text-right">Rp <?php echo number_format($slip['base_salary'], 0, ',', '.'); ?></td>
								</tr>
								<tr>
									<td>Bonus Lainnya</td>
									<td class="text-right">Rp <?php echo number_format($slip['bonus'], 0, ',', '.'); ?></td>
								</tr>
								<tr><td>Bonus Sholat<?php if (!empty($slip['prayer_rank'])): ?> (<?php echo html_escape($slip['prayer_period']); ?>, peringkat <?php echo (int) $slip['prayer_rank']; ?>, <?php echo (int) $slip['prayer_count']; ?> × Rp5.000)<?php endif; ?></td><td class="text-right">Rp <?php echo number_format($slip['prayer_bonus'], 0, ',', '.'); ?></td></tr>
				<tr><td>Uang Lembur (<?php echo (int) $slip['overtime_days']; ?> hari, <?php echo (int) $slip['overtime_minutes']; ?> menit; maksimal Rp50.000/hari)</td><td class="text-right">Rp <?php echo number_format($slip['overtime_pay'], 0, ',', '.'); ?></td></tr>
				<tr><td>Uang Harian (<?php echo (int) $slip['attendance_days']; ?> hari × Rp <?php echo number_format($slip['daily_rate'], 0, ',', '.'); ?>)</td><td class="text-right">Rp <?php echo number_format($slip['daily_pay'], 0, ',', '.'); ?></td></tr>
				
				<tr><td>Tambahan Transaksi Keuangan</td><td class="text-right">Rp <?php echo number_format($slip['finance_addition'], 0, ',', '.'); ?></td></tr>
				<tr><td>Potongan Transaksi Keuangan</td><td class="text-right">Rp <?php echo number_format($slip['finance_deduction'], 0, ',', '.'); ?></td></tr>
				<tr>
									<td>Potongan</td>
									<td class="text-right">Rp <?php echo number_format($slip['deduction'], 0, ',', '.'); ?></td>
								</tr>
									<tr>
										<td>Kasbon</td>
										<td class="text-right">Rp <?php echo number_format($slip['kasbon'], 0, ',', '.'); ?></td>
									</tr>
									
							<?php $finance_items = json_decode($slip['finance_details'] ?? '[]', TRUE) ?: array(); ?>
				<?php foreach ($finance_items as $item): ?><tr><td colspan="2" style="font-size:10px;">Keuangan <?php echo html_escape($item['date']); ?>: <?php echo html_escape($item['description']); ?> (<?php echo $item['effect'] === 'addition' ? '+' : '-'; ?>Rp <?php echo number_format($item['amount'], 0, ',', '.'); ?>)</td></tr><?php endforeach; ?>
			<tr class="total table-active"><td><strong>Total Gaji</strong></td><td class="text-right"><strong>Rp <?php echo number_format((float) $slip['net_salary'] + (float) $slip['weekly_daily_paid'], 0, ',', '.'); ?></strong></td></tr>
				<tr><td>Sudah Ditransfer</td><td class="text-right">Rp <?php echo number_format($slip['weekly_daily_paid'], 0, ',', '.'); ?></td></tr>
				<tr class="total table-active"><td><strong>Yang Dibayarkan</strong></td><td class="text-right"><strong>Rp <?php echo number_format($slip['net_salary'], 0, ',', '.'); ?></strong></td></tr>
			</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>
	</div>
