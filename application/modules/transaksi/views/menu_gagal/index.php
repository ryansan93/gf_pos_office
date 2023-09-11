<div class="row content-panel" style="height: 100%;">
	<div class="col-xs-12 tab-contain" style="padding-top: 10px; height: 100%;">
		<div class="col-xs-12">
			<button class="btn btn-primary btn-tab" data-href="riwayat">Riwayat</button>
			<button class="btn btn-primary btn-tab" data-href="action">Action</button>
		</div>
		<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12 div-tab active" id="riwayat">
			<?php echo $riwayatForm; ?>
		</div>
		<div class="col-xs-12 div-tab non-active" id="action">
			<?php if ( $akses['a_submit'] == 1 ): ?>
				<?php echo $addForm; ?>
			<?php else: ?>
				Detail Menu Gagal
			<?php endif ?>
		</div>
	</div>
</div>