<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Slip Gaji</title>
	<style>
		body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 0; padding: 28px 24px 24px 24px; }
		.header-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
		.header-table td { border: none; padding: 0; }
		.header-title { font-size: 20px; font-weight: bold; padding-bottom: 6px; }
		.header-subtitle { font-size: 11px; color: #555; }
		.meta { margin-top: 12px; margin-bottom: 18px; }
		.meta td { padding: 4px 0; vertical-align: top; }
		table { width: 100%; border-collapse: collapse; }
		th, td { border: 1px solid #ccc; padding: 8px; }
		th { background: #f1f1f1; text-align: left; }
		.text-right { text-align: right; }
		.total { font-weight: bold; background: #f8f8f8; }
	</style>
</head>
<body>
	<table class="header-table">
		<tr>
			<td class="header-title">Slip Gaji</td>
		</tr>
		<tr>
			<td class="header-subtitle"><?php echo $slip['employee_name']; ?> | Periode <?php echo str_pad($slip['payroll_month'], 2, '0', STR_PAD_LEFT).'/'.$slip['payroll_year']; ?></td>
		</tr>
	</table>
	<table class="meta">
		<tr><td width="20%">NIK</td><td width="30%">: <?php echo $slip['nik']; ?></td><td width="20%">Periode</td><td width="30%">: <?php echo str_pad($slip['payroll_month'], 2, '0', STR_PAD_LEFT).'/'.$slip['payroll_year']; ?></td></tr>
		<tr><td>Nama</td><td>: <?php echo $slip['employee_name']; ?></td><td>Status</td><td>: <?php echo $slip['status']; ?></td></tr>
		<tr><td>Divisi</td><td>: <?php echo strtolower(trim((string) $slip['division_name'])) === 'absensi' ? 'Operasional' : $slip['division_name']; ?></td><td>Jabatan</td><td>: <?php echo $slip['position_name']; ?></td></tr>
	</table>
	<table>
		<thead>
			<tr>
				<th>Komponen</th>
				<th class="text-right">Nominal</th>
			</tr>
		</thead>
			<tbody>
				<tr><td>Gaji Pokok</td><td class="text-right">Rp <?php echo number_format($slip['base_salary'], 0, ',', '.'); ?></td></tr>
				<tr><td>Bonus Lainnya</td><td class="text-right">Rp <?php echo number_format($slip['bonus'], 0, ',', '.'); ?></td></tr>
				<tr><td>Bonus Sholat<?php if (!empty($slip['prayer_rank'])): ?> (<?php echo html_escape($slip['prayer_period']); ?>, peringkat <?php echo (int) $slip['prayer_rank']; ?>, <?php echo (int) $slip['prayer_count']; ?> × Rp5.000)<?php endif; ?></td><td class="text-right">Rp <?php echo number_format($slip['prayer_bonus'], 0, ',', '.'); ?></td></tr>
				<tr><td>Uang Lembur (<?php echo (int) $slip['overtime_days']; ?> hari, <?php echo (int) $slip['overtime_minutes']; ?> menit; maksimal Rp50.000/hari)</td><td class="text-right">Rp <?php echo number_format($slip['overtime_pay'], 0, ',', '.'); ?></td></tr>
				<tr><td>Uang Harian (<?php echo (int) $slip['attendance_days']; ?> hari × Rp <?php echo number_format($slip['daily_rate'], 0, ',', '.'); ?>)</td><td class="text-right">Rp <?php echo number_format($slip['daily_pay'], 0, ',', '.'); ?></td></tr>
				
				<tr><td>Tambahan Transaksi Keuangan</td><td class="text-right">Rp <?php echo number_format($slip['finance_addition'], 0, ',', '.'); ?></td></tr>
				<tr><td>Potongan Transaksi Keuangan</td><td class="text-right">Rp <?php echo number_format($slip['finance_deduction'], 0, ',', '.'); ?></td></tr>
				<tr><td>Potongan</td><td class="text-right">Rp <?php echo number_format($slip['deduction'], 0, ',', '.'); ?></td></tr>
				<tr><td>Kasbon</td><td class="text-right">Rp <?php echo number_format($slip['kasbon'], 0, ',', '.'); ?></td></tr>
				
			<?php $finance_items = json_decode($slip['finance_details'] ?? '[]', TRUE) ?: array(); ?>
				<?php foreach ($finance_items as $item): ?><tr><td colspan="2" style="font-size:10px;">Keuangan <?php echo html_escape($item['date']); ?>: <?php echo html_escape($item['description']); ?> (<?php echo $item['effect'] === 'addition' ? '+' : '-'; ?>Rp <?php echo number_format($item['amount'], 0, ',', '.'); ?>)</td></tr><?php endforeach; ?>
			<tr class="total table-active"><td><strong>Total Gaji</strong></td><td class="text-right"><strong>Rp <?php echo number_format((float) $slip['net_salary'] + (float) $slip['weekly_daily_paid'], 0, ',', '.'); ?></strong></td></tr>
				<tr><td>Sudah Ditransfer</td><td class="text-right">Rp <?php echo number_format($slip['weekly_daily_paid'], 0, ',', '.'); ?></td></tr>
				<tr class="total table-active"><td><strong>Yang Dibayarkan</strong></td><td class="text-right"><strong>Rp <?php echo number_format($slip['net_salary'], 0, ',', '.'); ?></strong></td></tr>
			</tbody>
	</table>
</body>
</html>
