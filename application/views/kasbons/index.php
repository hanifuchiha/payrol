	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1>Kasbon Karyawan</h1>
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
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Daftar Kasbon</h3>
						<div class="card-tools">
							<a href="<?php echo site_url('kasbons/create'); ?>" class="btn btn-primary btn-sm">Tambah</a>
						</div>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover text-nowrap">
							<thead>
								<tr>
									<th>Tanggal</th>
									<th>NIK</th>
									<th>Karyawan</th>
									<th>Nominal</th>
									<th>Keterangan</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($kasbons as $kasbon): ?>
								<tr>
									<td><?php echo $kasbon['transaction_date']; ?></td>
									<td><?php echo $kasbon['nik']; ?></td>
									<td><?php echo $kasbon['employee_name']; ?></td>
									<td>Rp <?php echo number_format($kasbon['amount'], 0, ',', '.'); ?></td>
									<td><?php echo $kasbon['note']; ?></td>
									<td>
										<a href="<?php echo site_url('kasbons/edit/'.$kasbon['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
										<a href="<?php echo site_url('kasbons/delete/'.$kasbon['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
