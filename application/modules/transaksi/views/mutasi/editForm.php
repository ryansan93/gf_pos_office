<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control nama_pic uppercase" placeholder="Nama PiC" data-required="1" value="<?php echo $data['nama_pic']; ?>">
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Asal</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control asal" data-required="1">
			<option value="">Pilih Gudang</option>
			<?php if ( !empty($gudang) ): ?>
				<?php foreach ($gudang as $key => $value): ?>
					<?php
						$selected = null;
						if ( $value['kode_gudang'] == $data['asal'] ) {
							$selected = 'selected';
						}
					?>
					<option value="<?php echo $value['kode_gudang']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tujuan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control tujuan" data-required="1">
			<option value="">Pilih Gudang</option>
			<?php if ( !empty($gudang) ): ?>
				<?php foreach ($gudang as $key => $value): ?>
					<?php
						$selected = null;
						if ( $value['kode_gudang'] == $data['tujuan'] ) {
							$selected = 'selected';
						}
					?>
					<option value="<?php echo $value['kode_gudang']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Mutasi</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglMutasi" id="TglMutasi">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $data['tgl_mutasi']; ?>" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">No. SJ</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control no_sj uppercase" placeholder="No. SJ" value="<?php echo $data['no_sj']; ?>">
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Lampiran SJ</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding attachment" style="margin-top: 0px;">
			<a name="dokumen" class="text-right" target="_blank" style="padding-right: 10px;" href="uploads/<?php echo $data['lampiran']; ?>" title="<?php echo $data['lampiran']; ?>"><i class="fa fa-file"></i></a>
            <label class="" style="margin-bottom: 0px;">
                <input style="display: none;" placeholder="Dokumen" class="file_lampiran no-check" type="file" onchange="mutasi.showNameFile(this)" data-name="name" data-allowtypes="doc|pdf|docx|jpg|jpeg|png">
                <i class="glyphicon glyphicon-paperclip cursor-p" title="Attachment"></i> 
            </label>
		</div>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-2">COA SAP</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga Satuan (Rp.)</th>
					<th class="col-xs-1">Total</th>
					<th class="col-xs-1">Action</th>
				</tr>
			</thead>
			<?php $grand_total = 0; ?>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td>
							<select class="form-control item" data-required="1">
							<option value="<?php echo $v_det['item_kode']; ?>" data-coa="<?php echo $v_det['coa']; ?>" data-ketcoa="<?php echo $v_det['ket_coa']; ?>" selected><?php echo $v_det['nama_item']; ?></option>
								<!-- <option value="">-- Pilih Item --</option>
								<?php if ( !empty($item) ): ?>
									<?php foreach ($item as $k_item => $v_item): ?>
										<?php
											$selected = null;
											if ( $v_item['kode'] == $v_det['item_kode'] ) {
												$selected = 'selected';
											}
										?>
										<option value="<?php echo $v_item['kode']; ?>" data-satuan='<?php echo json_encode($v_item['satuan']); ?>' data-coa='<?php echo $v_item['coa']; ?>' data-ketcoa='<?php echo $v_item['ket_coa']; ?>' <?php echo $selected; ?> ><?php echo strtoupper($v_item['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?> -->
							</select>
						</td>
						<td class="coa">
							-
						</td>
						<td>
							<select class="form-control satuan" data-required="1">
								<option value="<?php echo $v_det['satuan']; ?>" data-pengali="<?php echo $v_det['pengali']; ?>" data-harga="<?php echo $v_det['harga']; ?>" selected><?php echo $v_det['satuan']; ?></option>
								<!-- <option value="">Pilih Satuan</option> -->
							</select>
						</td>
						<td>
							<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal" data-required="1" maxlength="10" value="<?php echo angkaDecimal($v_det['jumlah']); ?>" onblur="mutasi.hitTotal(this)">
						</td>
						<td class="harga text-right"><?php echo angkaDecimal($v_det['harga']); ?></td>
						<td class="total text-right"><?php echo angkaDecimal($v_det['total']); ?></td>
						<td>
							<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
								<button type="button" class="btn btn-danger" onclick="mutasi.removeRow(this);"><i class="fa fa-minus"></i></button>
							</div>
							<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
								<button type="button" class="btn btn-primary" onclick="mutasi.addRow(this);"><i class="fa fa-plus"></i></button>
							</div>
						</td>
					</tr>
					<?php $grand_total += $v_det['total']; ?>
				<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" class="text-right"><b>TOTAL</b></td>
					<td class="text-right grand_total"><b><?php echo angkaDecimal($grand_total); ?></b></td>
				</tr>
			</tfoot>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Keterangan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<textarea class="form-control keterangan"><?php echo $data['keterangan']; ?></textarea>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding" style="padding-bottom: 5px;">
	<button type="button" class="col-xs-12 btn btn-primary pull-right" onclick="mutasi.edit(this)" data-kode="<?php echo $data['kode_mutasi']; ?>"><i class="fa fa-save"></i> Simpan Perubahan</button>
</div>
<div class="col-xs-12 no-padding" style="padding-top: 5px;">
	<button type="button" class="col-xs-12 btn btn-danger pull-right" onclick="mutasi.changeTabActive(this)" data-id="<?php echo $data['kode_mutasi']; ?>" data-href="action" data-edit=""><i class="fa fa-times"></i> Batal</button>
</div>