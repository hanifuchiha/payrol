	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1>Dashboard Payroll</h1>
					</div>
				</div>
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
				<div class="row">
					<?php foreach ($stats as $stat): ?>
					<div class="col-lg-3 col-6">
						<div class="small-box <?php echo $stat['bg']; ?>">
							<div class="inner">
								<h3><?php echo $stat['value']; ?></h3>
								<p><?php echo $stat['label']; ?></p>
							</div>
							<div class="icon">
								<i class="<?php echo $stat['icon']; ?>"></i>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>

				<div class="row">
					<div class="col-md-8">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Ringkasan Sistem</h3>
							</div>
							<div class="card-body">
								<ul class="mb-0">
									<?php foreach ($activities as $activity): ?>
									<li><?php echo $activity; ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Menu Siap</h3>
							</div>
							<div class="card-body p-0">
								<ul class="list-group list-group-flush">
									<li class="list-group-item">Data karyawan</li>
									<li class="list-group-item">Divisi dan jabatan</li>
									<li class="list-group-item">Komponen gaji</li>
									<li class="list-group-item">Proses payroll</li>
									<li class="list-group-item">Slip gaji dan laporan</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
