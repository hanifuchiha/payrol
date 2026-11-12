	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1>Komponen Gaji</h1>
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
						<h3 class="card-title">Daftar Komponen</h3>
						<div class="card-tools">
							<a href="<?php echo site_url('salary_components/create'); ?>" class="btn btn-primary btn-sm">Tambah</a>
						</div>
					</div>
					<div class="card-body table-responsive p-0">
						<table class="table table-hover text-nowrap">
							<thead>
								<tr>
									<th>Nama Komponen</th>
									<th>Tipe</th>
									<th>Nominal Default</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($components as $component): ?>
								<tr>
									<td><?php echo $component['name']; ?></td>
									<td><?php echo $component['component_type']; ?></td>
									<td>Rp <?php echo number_format($component['default_amount'], 0, ',', '.'); ?></td>
									<td>
										<a href="<?php echo site_url('salary_components/edit/'.$component['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
										<a href="<?php echo site_url('salary_components/delete/'.$component['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
