<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Paket Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-4 form-control uppercase nama" placeholder="Nama" data-required="1" value="<?php echo $data['nama']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Menu Paket</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-8 form-control menu_paket" data-required="1">
								<option>-- Pilih Menu --</option>
								<?php if ( !empty($menu) ): ?>
									<?php foreach ($menu as $key => $val): ?>
										<?php
											$selected = '';
											if ( $val['kode_menu'] == $data['menu_kode'] ) {
												$selected = 'selected';
											}
										?>
										<option value="<?php echo $val['kode_menu']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Jumlah Pilih</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control text-right uppercase jumlah_pilih" placeholder="Jumlah" data-required="1" data-tipe="integer" value="<?php echo angkaRibuan($data['max_pilih']); ?>">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table class="table table-bordered tbl_detail" style="margin-bottom: 0px;">
								<thead>
									<tr>
										<th class="col-sm-6">Menu</th>
										<th class="col-sm-2">Jumlah Min</th>
										<th class="col-sm-2">Jumlah Max</th>
										<th class="col-sm-2">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($data['isi_paket_menu'] as $k_ipm => $v_ipm): ?>
										<tr>
											<td>
												<select class="form-control menu" data-required="1">
													<option>-- Pilih Menu --</option>
													<?php if ( !empty($menu) ): ?>
														<?php foreach ($menu as $key => $val): ?>
															<?php
																$selected = '';
																if ( $val['kode_menu'] == $v_ipm['menu_kode'] ) {
																	$selected = 'selected';
																}
															?>
															<option value="<?php echo $val['kode_menu']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
														<?php endforeach ?>
													<?php endif ?>
												</select>
											</td>
											<td>
												<input type="text" class="form-control text-right uppercase jumlah_min" placeholder="Jumlah" data-required="1" data-tipe="integer" value="<?php echo angkaRibuan($v_ipm['jumlah_min']); ?>">
											</td>
											<td>
												<input type="text" class="form-control text-right uppercase jumlah_max" placeholder="Jumlah" data-required="1" data-tipe="integer" value="<?php echo angkaRibuan($v_ipm['jumlah_max']); ?>">
											</td>
											<td>
												<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
													<button class="btn btn-primary" onclick="pm.addRow(this);"><i class="fa fa-plus"></i></button>
												</div>
												<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
													<button class="btn btn-danger" onclick="pm.removeRow(this);"><i class="fa fa-minus"></i></button>
												</div>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
			<button type="button" class="btn btn-primary pull-right" onclick="pm.edit(this)" data-kode="<?php echo $data['kode_paket_menu'] ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>