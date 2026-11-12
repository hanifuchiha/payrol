	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1>Slip Gaji dan Laporan</h1>
			</div>
		</section>
		<section class="content">
			<div class="container-fluid">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Daftar Output Laporan</h3>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover text-nowrap">
							<thead>
								<tr>
									<th>Nama Laporan</th>
									<th>Periode</th>
									<th>Total Karyawan</th>
									<th>Total Gaji Bersih</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($reports as $report): ?>
								<tr>
									<td>Rekap Payroll</td>
									<td><?php echo $report['period']; ?></td>
									<td><?php echo $report['total_employee']; ?></td>
									<td>Rp <?php echo number_format($report['total_net'], 0, ',', '.'); ?></td>
									<td><?php echo $report['status']; ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Slip Gaji</h3>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover text-nowrap">
							<thead>
								<tr>
									<th>Karyawan</th>
									<th>Periode</th>
									<th>Gaji Pokok</th>
									<th>Bonus</th>
									<th>Potongan</th>
									<th>Kasbon</th>
									<th>Gaji Bersih</th>
									<th>Status</th>
									<th>Slip</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($slips as $slip): ?>
								<tr>
									<td><?php echo $slip['employee_name']; ?></td>
									<td><?php echo str_pad($slip['payroll_month'], 2, '0', STR_PAD_LEFT).'/'.$slip['payroll_year']; ?></td>
									<td>Rp <?php echo number_format($slip['base_salary'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($slip['bonus'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($slip['deduction'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($slip['kasbon'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($slip['net_salary'], 0, ',', '.'); ?></td>
									<td><?php echo $slip['status']; ?></td>
									<td>
										<a href="<?php echo site_url('reports/slip/'.$slip['id']); ?>" class="btn btn-info btn-sm">Lihat Slip</a>
										<a href="<?php echo site_url('reports/slip_pdf/'.$slip['id']); ?>" class="btn btn-secondary btn-sm">PDF</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Rekap Kasbon Karyawan</h3>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover text-nowrap">
							<thead>
								<tr>
									<th>NIK</th>
									<th>Karyawan</th>
									<th>Total Kasbon</th>
									<th>Sudah Dipotong</th>
									<th>Sisa Kasbon</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($kasbon_summaries as $summary): ?>
								<tr>
									<td><?php echo $summary['nik']; ?></td>
									<td><?php echo $summary['employee_name']; ?></td>
									<td>Rp <?php echo number_format($summary['total_kasbon'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($summary['total_potong_kasbon'], 0, ',', '.'); ?></td>
									<td>Rp <?php echo number_format($summary['sisa_kasbon'], 0, ',', '.'); ?></td>
									<td><?php echo $summary['status_pelunasan']; ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>
	</div>
