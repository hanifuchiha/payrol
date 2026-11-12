	<aside class="main-sidebar sidebar-dark-primary elevation-4">
		<a href="<?php echo site_url(); ?>" class="brand-link">
			<span class="brand-text font-weight-light ml-3">Payroll App</span>
		</a>

		<div class="sidebar">
			<nav class="mt-3">
				<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
					<li class="nav-item">
						<a href="<?php echo html_escape(Payroll_sso::BASE); ?>" class="nav-link">
							<i class="nav-icon fas fa-th-large" aria-hidden="true"></i>
							<p>Pilih Aplikasi</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo site_url(); ?>" class="nav-link <?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>">
							<i class="nav-icon fas fa-tachometer-alt"></i>
							<p>Dashboard</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo site_url('employees'); ?>" class="nav-link <?php echo $active_menu === 'employees' ? 'active' : ''; ?>">
							<i class="nav-icon fas fa-users"></i>
							<p>Data Karyawan</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo site_url('departments'); ?>" class="nav-link <?php echo $active_menu === 'departments' ? 'active' : ''; ?>">
							<i class="nav-icon fas fa-building"></i>
							<p>Divisi & Jabatan</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo site_url('salary_components'); ?>" class="nav-link <?php echo $active_menu === 'salary_components' ? 'active' : ''; ?>">
							<i class="nav-icon fas fa-wallet"></i>
							<p>Komponen Gaji</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo site_url('kasbons'); ?>" class="nav-link <?php echo $active_menu === 'kasbons' ? 'active' : ''; ?>">
							<i class="nav-icon fas fa-hand-holding-usd"></i>
							<p>Kasbon</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo site_url('payrolls'); ?>" class="nav-link <?php echo $active_menu === 'payrolls' ? 'active' : ''; ?>">
							<i class="nav-icon fas fa-money-check-alt"></i>
							<p>Proses Payroll</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo site_url('reports'); ?>" class="nav-link <?php echo $active_menu === 'reports' ? 'active' : ''; ?>">
							<i class="nav-icon fas fa-file-invoice-dollar"></i>
							<p>Slip & Laporan</p>
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</aside>
