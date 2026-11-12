	<div class="content-wrapper">
		<section class="content-header">
			<div class="container-fluid">
				<h1><?php echo $page_title; ?></h1>
			</div>
		</section>
		<section class="content">
			<div class="container-fluid">
				<?php if ($this->session->flashdata('error')): ?>
				<div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
				<?php endif; ?>
				<div class="card">
					<div class="card-body">
						<form method="post" action="<?php echo site_url($action); ?>">
								<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
							<div class="form-group">
								<label>Divisi</label>
								<input type="text" name="division_name" class="form-control" value="<?php echo set_value('division_name', isset($department['division_name']) ? $department['division_name'] : ''); ?>">
							</div>
							<div class="form-group">
								<label>Jabatan</label>
								<input type="text" name="position_name" class="form-control" value="<?php echo set_value('position_name', isset($department['position_name']) ? $department['position_name'] : ''); ?>">
							</div>
							<div class="form-group">
								<label>Tunjangan Jabatan</label>
								<?php $allowance_value = set_value('allowance', isset($department['allowance']) ? $department['allowance'] : ''); ?>
								<input type="text" id="allowance_display" class="form-control" value="" inputmode="numeric" autocomplete="off">
								<input type="hidden" name="allowance" id="allowance" value="<?php echo html_escape($allowance_value); ?>">
							</div>
							<button type="submit" class="btn btn-primary">Simpan</button>
							<a href="<?php echo site_url('departments'); ?>" class="btn btn-secondary">Kembali</a>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var allowanceInput = document.getElementById('allowance');
	var allowanceDisplay = document.getElementById('allowance_display');

	if (!allowanceInput || !allowanceDisplay) {
		return;
	}

	function digitsOnly(value) {
		return (value || '').replace(/\D+/g, '');
	}

	function formatRupiah(value) {
		var digits = digitsOnly(value);
		if (!digits) {
			return '';
		}

		return 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(digits, 10));
	}

	function syncDisplayFromHidden() {
		allowanceDisplay.value = formatRupiah(allowanceInput.value);
	}

	allowanceDisplay.addEventListener('input', function () {
		allowanceInput.value = digitsOnly(allowanceDisplay.value);
		syncDisplayFromHidden();
	});

	allowanceDisplay.addEventListener('blur', syncDisplayFromHidden);

	syncDisplayFromHidden();
});
</script>
