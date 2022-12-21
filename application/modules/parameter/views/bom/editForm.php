<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right:5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Menu</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control menu" data-required="1" disabled data-kode="<?php echo $data['menu_kode']; ?>" value="<?php echo $data['menu']['nama']; ?>">
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left:5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Berlaku</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglBerlaku" id="TglBerlaku">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $data['tgl_berlaku']; ?>" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<small>
		<table class="table table-bordered tbl_item" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-4">Item</th>
					<th class="col-xs-3">Satuan</th>
					<th class="col-xs-3">Jumlah</th>
					<th class="col-xs-2"></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>					
					<tr class="search v-center data">
						<?php 
							$satuan = null; 
							$satuan_item = null; 
						?>
						<td>
							<select class="form-control item" data-required="1">
								<option value="">Pilih Item</option>
								<?php foreach ($item as $k_item => $v_item): ?>
									<?php
										$selected = null;
										if ( $v_item['kode'] == $v_det['item_kode'] ) {
											$selected = 'selected';

											$satuan = $v_item['satuan'];
										}
									?>
									<option value="<?php echo $v_item['kode']; ?>" data-satuan='<?php echo json_encode($v_item['satuan']); ?>' <?php echo $selected; ?> ><?php echo strtoupper($v_item['nama']); ?></option>
								<?php endforeach ?>
							</select>
						</td>
						<td>
							<select class="form-control satuan" data-required="1">
								<option value="">Pilih Satuan</option>
								<?php if ( !empty($satuan) ): ?>
									<?php foreach ($satuan as $k_satuan => $v_satuan): ?>
										<?php
											$selected = null;
											if ( $v_satuan['satuan'] == $v_det['satuan'] ) {
												$selected = 'selected';
											}
										?>
										<option value="<?php echo $v_satuan['satuan']; ?>" data-pengali="<?php echo $v_satuan['pengali']; ?>" <?php echo $selected; ?> ><?php echo $v_satuan['satuan']; ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
						<td>
							<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal"  maxlength="10" data-required="1" value="<?php echo angkaDecimal($v_det['jumlah']); ?>" >
						</td>
						<td>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-6 no-padding" style="padding-right: 5px;">
									<button type="button" class="col-xs-12 btn btn-danger" onclick="bom.removeRow(this)"><i class="fa fa-times"></i></button>
								</div>
								<div class="col-xs-6 no-padding" style="padding-left: 5px;">
									<button type="button" class="col-xs-12 btn btn-primary" onclick="bom.addRow(this)"><i class="fa fa-plus"></i></button>
								</div>
							</div>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<div class="col-xs-6 no-padding" style="padding-right: 5px;">
		<button type="button" class="col-xs-12 btn btn-danger pull-right" onclick="bom.changeTabActive(this)" data-id="<?php echo $data['id']; ?>" data-href="action" data-edit=""><i class="fa fa-times"></i> Batal</button>
	</div>

	<div class="col-xs-6 no-padding" style="padding-left: 5px;">
		<button type="button" class="col-xs-12 btn btn-primary pull-right" onclick="bom.edit(this)" data-id="<?php echo $data['id']; ?>"><i class="fa fa-save"></i> Simpan Perubahan</button>
	</div>
</div>