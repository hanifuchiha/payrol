<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $page_title; ?></title>
	<link rel="stylesheet" href="<?php echo base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/adminlte/dist/css/adminlte.min.css'); ?>">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
	<nav class="main-header navbar navbar-expand navbar-white navbar-light">
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
			</li>
			<li class="nav-item d-none d-sm-inline-block">
				<a href="<?php echo site_url(); ?>" class="nav-link">Dashboard</a>
			</li>
		</ul>
		<ul class="navbar-nav ml-auto">
			<li class="nav-item d-flex align-items-center">
				<span class="nav-link" aria-label="Pengguna yang sedang login"><i class="fas fa-user-circle mr-1" aria-hidden="true"></i><?php echo html_escape($current_user); ?></span>
			</li>
			<li class="nav-item">
				<a href="<?php echo site_url('auth/logout'); ?>" class="nav-link text-nowrap" title="Keluar dari semua aplikasi SSO"><i class="fas fa-sign-out-alt mr-1" aria-hidden="true"></i>Logout semua</a>
			</li>
		</ul>
	</nav>
