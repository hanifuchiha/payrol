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
								<label>Karyawan</label>
								<select name="employee_id" class="form-control">
									<option value="">Pilih</option>
									<?php foreach ($employees as $employee): ?>
									<option value="<?php echo $employee['id']; ?>" <?php echo set_select('employee_id', $employee['id'], isset($kasbon['employee_id']) && (int) $kasbon['employee_id'] === (int) $employee['id']); ?>>
										<?php echo $employee['nik'].' - '.$employee['name']; ?>
									</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<label>Tanggal Kasbon</label>
								<input type="date" name="transaction_date" class="form-control" value="<?php echo set_value('transaction_date', isset($kasbon['transaction_date']) ? $kasbon['transaction_date'] : date('Y-m-d')); ?>">
							</div>
							<div class="form-group">
								<label>Nominal Kasbon</label>
								<?php $amount_value = set_value('amount', isset($kasbon['amount']) ? $kasbon['amount'] : ''); ?>
								<input type="text" id="amount_display" class="form-control" value="" inputmode="numeric" autocomplete="off">
								<input type="hidden" name="amount" id="amount" value="<?php echo html_escape($amount_value); ?>">
							</div>
							<div class="form-group">
								<label>Keterangan</label>
								<textarea name="note" class="form-control" rows="3"><?php echo set_value('note', isset($kasbon['note']) ? $kasbon['note'] : ''); ?></textarea>
							</div>
							<button type="submit" class="btn btn-primary">Simpan</button>
							<a href="<?php echo site_url('kasbons'); ?>" class="btn btn-secondary">Kembali</a>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var amountInput = document.getElementById('amount');
	var amountDisplay = document.getElementById('amount_display');

	if (!amountInput || !amountDisplay) {
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
		amountDisplay.value = formatRupiah(amountInput.value);
	}

	amountDisplay.addEventListener('input', function () {
		amountInput.value = digitsOnly(amountDisplay.value);
		syncDisplayFromHidden();
	});

	amountDisplay.addEventListener('blur', syncDisplayFromHidden);

	syncDisplayFromHidden();
});
</script>
