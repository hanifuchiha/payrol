	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1>Data Karyawan</h1>
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
				<div class="alert alert-info"><?php echo $source_notice; ?></div>
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Daftar Karyawan</h3>
						<div class="card-tools">
							<a href="<?php echo site_url('employees/create'); ?>" class="btn btn-primary btn-sm">Tambah</a>
						</div>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover text-nowrap">
								<thead>
									<tr>
										<th>NIK</th>
										<th>Nama</th>
										<th>No Telepon</th>
										<th>NPWP</th>
										<th>BPJS</th>
										<th>Tanggal Masuk</th>
										<th>Divisi</th>
										<th>Jabatan</th>
										<th>Status</th>
										<th>Periode Kontrak</th>
										<th>Sisa Kontrak</th>
										<th>Gaji Pokok</th><th>Uang Harian / Hari</th>
										<th>Aksi</th>
									</tr>
								</thead>
							<tbody>
								<?php foreach ($employees as $employee): ?>
								<tr>
									<td><?php echo $employee['nik']; ?></td>
										<td><?php echo $employee['name']; ?></td>
										<td><?php echo $employee['phone']; ?></td>
										<td><?php echo !empty($employee['npwp']) ? $employee['npwp'] : '-'; ?></td>
										<td><?php echo !empty($employee['bpjs']) ? $employee['bpjs'] : '-'; ?></td>
										<td><?php echo !empty($employee['join_date']) ? date('d-m-Y', strtotime($employee['join_date'])) : '-'; ?></td>
										<td><?php echo strtolower(trim((string) $employee['division_name'])) === 'absensi' ? 'Operasional' : $employee['division_name']; ?></td>
										<td><?php echo $employee['position_name']; ?></td>
										<td><?php echo $employee['employment_status']; ?></td>
										<td>
											<?php if ($employee['employment_status'] === 'Kontrak' && !empty($employee['contract_start_date']) && !empty($employee['contract_end_date'])): ?>
												<?php echo date('d-m-Y', strtotime($employee['contract_start_date'])).' s/d '.date('d-m-Y', strtotime($employee['contract_end_date'])); ?>
											<?php else: ?>
												-
											<?php endif; ?>
										</td>
										<td><?php echo !empty($employee['contract_remaining_text']) ? $employee['contract_remaining_text'] : '-'; ?></td>
										<td>Rp <?php echo number_format($employee['base_salary'], 0, ',', '.'); ?></td><td>Rp <?php echo number_format($employee['daily_allowance'], 0, ',', '.'); ?></td>
										<td>
										<a href="<?php echo site_url('employees/edit/'.$employee['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
										<a href="<?php echo site_url('employees/delete/'.$employee['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>
	</div>
