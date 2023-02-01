<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<?php if ( $akses['a_submit'] == 1 ) { ?>
			<div class="col-lg-12 no-padding">
				<button id="btn-add" type="button" data-href="action" class="col-lg-12 btn btn-primary cursor-p pull-right" title="ADD" onclick="meja.modalAddForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			</div>
		<?php } ?>
		<div class="col-lg-12 no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-lg-12 search left-inner-addon no-padding" style="margin-bottom: 10px;">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_riwayat" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<small>
			<table class="table table-bordered tbl_riwayat" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-3 text-center">Branch</th>
						<th class="col-sm-2 text-center">Lantai</th>
						<th class="col-sm-2 text-center">Meja</th>
						<th class="col-sm-1 text-center">Kontrol Meja</th>
						<th class="col-sm-1 text-center">Status</th>
						<th class="col-sm-1 text-center">Action</th>
					</tr>
				</thead>
				<?php foreach ($data as $k_branch => $v_branch): ?>
					<tbody class="row-wrapper">
						<?php 
							$idx_branch = 0; 
							$rowspan_branch = 0;
							foreach ($v_branch['lantai'] as $k_lt => $v_lt) {
								$rowspan_branch++;
								$rowspan_branch += count($v_lt['meja']);
							}
						?>
						<?php foreach ($v_branch['lantai'] as $k_lt => $v_lt): ?>
							<?php
								$idx_lt = 0;
								$rowspan_lantai = count($v_lt['meja']);
							?>
							<?php foreach ($v_lt['meja'] as $k_meja => $v_meja): ?>
								<tr>
									<?php if ( $idx_branch == 0 ): ?>
										<td rowspan="<?php echo $rowspan_branch; ?>"><?php echo $v_branch['nama']; ?></td>
									<?php endif ?>
									<?php if ( $idx_lt == 0 ): ?>
										<td rowspan="<?php echo $rowspan_lantai; ?>"><?php echo $v_lt['nama']; ?></td>
									<?php endif ?>
									<td><?php echo $v_meja['nama']; ?></td>
									<?php if ( $idx_lt == 0 ): ?>
										<td class="text-center" rowspan="<?php echo $rowspan_lantai; ?>">
											<b>
												<?php if ( $v_lt['kontrol_meja'] == 1 ): ?>
													<i class="fa fa-check"></i>
												<?php else: ?>
													<i class="fa fa-minus"></i>
												<?php endif ?>
											</b>
										</td>
										<td class="text-center" rowspan="<?php echo $rowspan_lantai; ?>"><b><?php echo ($v_lt['status'] == 1) ? 'AKTIF' : 'NON AKTIF'; ?></b></td>
										<td rowspan="<?php echo $rowspan_lantai; ?>">
											<?php if ( $v_lt['status'] == 1 ): ?>
												<div class="col-xs-12 no-padding">
													<div class="col-xs-12 no-padding" style="padding-left: 3px;">
														<button type="button" class="col-xs-12 btn btn-danger" data-id="<?php echo $v_lt['kode']; ?>" onclick="meja.delete(this)"><i class="fa fa-trash"></i> NON AKTIF</button>
													</div>
												</div>
											<?php else: ?>
												<div class="col-xs-12 no-padding">
													<div class="col-xs-12 no-padding" style="padding-left: 3px;">
														<button type="button" class="col-xs-12 btn btn-primary" data-id="<?php echo $v_lt['kode']; ?>" onclick="meja.aktif(this)"><i class="fa fa-check"></i> AKTIF</button>
													</div>
												</div>
											<?php endif ?>
										</td>
									<?php endif ?>
								</tr>
								<?php $idx_lt++;  ?>
								<?php $idx_branch++;  ?>
							<?php endforeach ?>
						<?php endforeach ?>
					</tbody>
				<?php endforeach ?>
			</table>
		</small>
	</div>
</div>