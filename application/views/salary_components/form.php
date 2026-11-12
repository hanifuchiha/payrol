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
								<label>Nama Komponen</label>
								<input type="text" name="name" class="form-control" value="<?php echo set_value('name', isset($component['name']) ? $component['name'] : ''); ?>">
							</div>
							<div class="form-group">
								<label>Tipe</label>
								<select name="component_type" class="form-control">
									<option value="Pendapatan" <?php echo set_select('component_type', 'Pendapatan', isset($component['component_type']) && $component['component_type'] === 'Pendapatan'); ?>>Pendapatan</option>
									<option value="Potongan" <?php echo set_select('component_type', 'Potongan', isset($component['component_type']) && $component['component_type'] === 'Potongan'); ?>>Potongan</option>
								</select>
							</div>
							<div class="form-group">
								<label>Nominal Default</label>
								<?php $default_amount_value = set_value('default_amount', isset($component['default_amount']) ? $component['default_amount'] : ''); ?>
								<input type="text" id="default_amount_display" class="form-control" value="" inputmode="numeric" autocomplete="off">
								<input type="hidden" name="default_amount" id="default_amount" value="<?php echo html_escape($default_amount_value); ?>">
							</div>
							<button type="submit" class="btn btn-primary">Simpan</button>
							<a href="<?php echo site_url('salary_components'); ?>" class="btn btn-secondary">Kembali</a>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var defaultAmountInput = document.getElementById('default_amount');
	var defaultAmountDisplay = document.getElementById('default_amount_display');

	if (!defaultAmountInput || !defaultAmountDisplay) {
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
		defaultAmountDisplay.value = formatRupiah(defaultAmountInput.value);
	}

	defaultAmountDisplay.addEventListener('input', function () {
		defaultAmountInput.value = digitsOnly(defaultAmountDisplay.value);
		syncDisplayFromHidden();
	});

	defaultAmountDisplay.addEventListener('blur', syncDisplayFromHidden);

	syncDisplayFromHidden();
});
</script>
