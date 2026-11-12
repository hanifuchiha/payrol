<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login Admin</title>
	<link rel="stylesheet" href="<?php echo base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/adminlte/dist/css/adminlte.min.css'); ?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
	<div class="login-logo">
		<b>Payroll</b> Admin
	</div>
	<div class="card">
		<div class="card-body login-card-body">
			<p class="login-box-msg">Masuk untuk mengelola payroll</p>
			<?php if ($this->session->flashdata('error')): ?>
			<div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
			<?php endif; ?>
			<form action="<?php echo site_url('auth/attempt'); ?>" method="post">
				<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
				<div class="input-group mb-3">
					<input type="text" name="username" class="form-control" placeholder="Username">
					<div class="input-group-append">
						<div class="input-group-text"><span class="fas fa-user"></span></div>
					</div>
				</div>
				<div class="input-group mb-3">
					<input type="password" name="password" class="form-control" placeholder="Password">
					<div class="input-group-append">
						<div class="input-group-text"><span class="fas fa-lock"></span></div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary btn-block">Login</button>
			</form>
			<p class="mt-3 mb-0 text-muted text-center">Default: admin / admin123</p>
		</div>
	</div>
</div>
</body>
</html>
