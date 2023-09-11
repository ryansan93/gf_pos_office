<div class="row content-panel" style="height: 100%;">
	<div class="col-xs-12 tab-contain" style="padding-top: 10px; height: 100%;">
		<div class="panel-heading no-padding">
			<ul class="nav nav-tabs nav-justified">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#riwayat" data-tab="riwayat">Riwayat</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#action" data-tab="action">Action</a>
				</li>
			</ul>
		</div>
		<div class="panel-body no-padding">
			<div class="tab-content">
				<div id="riwayat" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">
					<?php echo $riwayatForm; ?>
				</div>

				<div id="action" class="tab-pane fade" role="tabpanel" style="padding-top: 10px;">
				<?php if ( $akses['a_submit'] == 1 ): ?>
					<?php echo $addForm; ?>
				<?php else: ?>
					Detail Menu Gagal
				<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</div>