	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1>Divisi dan Jabatan</h1>
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
						<h3 class="card-title">Struktur Organisasi</h3>
						<div class="card-tools">
							<a href="<?php echo site_url('departments/create'); ?>" class="btn btn-primary btn-sm">Tambah</a>
						</div>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover text-nowrap">
							<thead>
								<tr>
									<th>Divisi</th>
									<th>Jabatan</th>
									<th>Tunjangan Jabatan</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($departments as $department): ?>
								<tr>
									<td><?php echo strtolower(trim((string) $department['division_name'])) === 'absensi' ? 'Operasional' : $department['division_name']; ?></td>
									<td><?php echo $department['position_name']; ?></td>
									<td>Rp <?php echo number_format($department['allowance'], 0, ',', '.'); ?></td>
									<td>
										<a href="<?php echo site_url('departments/edit/'.$department['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
										<a href="<?php echo site_url('departments/delete/'.$department['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
