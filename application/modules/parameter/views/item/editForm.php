<div class="modal-header header">
	<span class="modal-title">Edit Item</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Kode</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="col-sm-12 form-control kode uppercase" placeholder="Kode (MAX : 25)" data-required="1" maxlength="25" value="<?php echo $data['kode']; ?>" disabled>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Nama</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="col-sm-12 form-control nama uppercase" placeholder="Nama (MAX : 50)" data-required="1" maxlength="50" value="<?php echo $data['nama']; ?>">
				</div>
			</div>
			<div class="col-xs-12 no-padding hide" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Brand</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="col-sm-12 form-control brand uppercase" placeholder="Brand (MAX : 50)" maxlength="50" value="<?php echo $data['brand']; ?>">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Group</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control group" data-required="1">
						<option>-- Pilih Group --</option>
						<?php foreach ($group as $key => $value): ?>
							<?php
								$selected = null;
								if ( $value['kode'] == $data['group_kode'] ) {
									$selected = 'selected';
								}
							?>
							<option value="<?php echo $value['kode']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding hide" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Spesifikasi</label></div>
				<div class="col-xs-12 no-padding">
					<textarea class="form-control keterangan" placeholder="Spesifikasi"><?php echo $data['keterangan']; ?></textarea>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding">
					<small>
						<table class="table table-bordered tbl_satuan" style="margin-bottom: 0px;">
							<thead>
								<tr>
									<th class="col-xs-5">Satuan</th>
									<th class="col-xs-5">Pengali</th>
									<th class="col-xs-2"></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($data['satuan'] as $k_satuan => $v_satuan): ?>
									<tr>
										<td>
											<input type="text" class="form-control uppercase satuan" placeholder="SATUAN" data-required="1" maxlength="10" value="<?php echo $v_satuan['satuan']; ?>">
										</td>
										<td>
											<input type="text" class="form-control uppercase text-right pengali" placeholder="PENGALI" data-tipe="decimal" data-required="1" maxlength="11" value="<?php echo angkaDecimal($v_satuan['pengali']); ?>">
										</td>
										<td>
											<div class="col-xs-12 no-padding">
												<div class="col-xs-6 no-padding" style="padding-right: 5px;">
													<button type="button" class="col-xs-12 btn btn-danger" onclick="item.removeRow(this)"><i class="fa fa-times"></i></button>
												</div>
												<div class="col-xs-6 no-padding" style="padding-left: 5px;">
													<button type="button" class="col-xs-12 btn btn-primary" onclick="item.addRow(this)"><i class="fa fa-plus"></i></button>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</small>
				</div>
			</div>
			<!-- <table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-6 form-control nama uppercase" placeholder="Nama" data-required="1" maxlength="50" value="<?php echo $data['nama']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Brand</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control brand uppercase" placeholder="Brand" data-required="1" maxlength="50" value="<?php echo $data['brand']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Satuan</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control satuan uppercase" placeholder="Satuan" data-required="1" maxlength="5" value="<?php echo $data['satuan']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Group</label>
						</td>
						<td class="col-sm-10">
							<select class="form-control group" data-required="1">
								<option>-- Pilih Group --</option>
								<?php foreach ($group as $key => $value): ?>
									<?php
										$selected = null;
										if ( $value['kode'] == $data['group_kode'] ) {
											$selected = 'selected';
										}
									?>
									<option value="<?php echo $value['kode']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
								<?php endforeach ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Spesifikasi</label>
						</td>
						<td class="col-sm-10">
							<textarea class="form-control keterangan" data-required="1" placeholder="Spesifikasi"><?php echo $data['keterangan']; ?></textarea>
						</td>
					</tr>
				</tbody>
			</table> -->
		</div>
		<div class="col-sm-12 no-padding"><hr></div>
		<div class="col-sm-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="item.edit(this)" data-kode="<?php echo $data['kode']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>