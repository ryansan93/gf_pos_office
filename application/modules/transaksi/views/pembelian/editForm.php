<!-- <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control branch" data-required="1">
			<option value="">-- Pilih Branch --</option>
			<?php foreach ($branch as $k_db => $v_db): ?>
				<option data-tokens="<?php echo $v_db['nama']; ?>" value="<?php echo $v_db['kode_branch']; ?>"><?php echo strtoupper($v_db['kode_branch'].' | '.$v_db['nama']); ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div> -->

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control nama_pic uppercase" placeholder="Nama PiC" data-required="1" value="<?php echo $data['nama_pic']; ?>">
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control branch selectpicker" data-live-search="true" data-required="1">
			<?php foreach ($branch as $key => $value): ?>
				<?php
					$selected = null;
					if ( $value['kode_branch'] == $data['branch_kode'] ) {
						$selected = 'selected';
					}
				?>
				<option value="<?php echo $value['kode_branch']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Supplier</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control supplier selectpicker" data-live-search="true" data-required="1">
			<?php foreach ($supplier as $key => $value): ?>
				<?php
					$selected = null;
					if ( $value['kode'] == $data['supplier_kode'] ) {
						$selected = 'selected';
					}
				?>
				<option value="<?php echo $value['kode']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Beli</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglBeli" id="TglBeli">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $data['tgl_beli']; ?>" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">No. Faktur</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control no_faktur uppercase" placeholder="No. Faktur" data-required="1" value="<?php echo $data['no_faktur']; ?>">
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Bukti Pembelian</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding attachment" style="margin-top: 0px;">
			<a href="uploads/<?php echo $data['lampiran']; ?>" name="dokumen" class="text-right" target="_blank" style="padding-right: 10px;"><?php echo $data['lampiran']; ?></a>
            <label class="" style="margin-bottom: 0px;">
                <input style="display: none;" placeholder="Dokumen" class="file_lampiran no-check" type="file" onchange="beli.showNameFile(this)" data-name="name" data-allowtypes="doc|pdf|docx|jpg|jpeg|png">
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
					<th class="col-xs-1">Group</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga</th>
					<th class="col-xs-2">Total</th>
					<th class="col-xs-1">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<?php 
							$opt = '';
							$satuan = null;
							$group = null;
							foreach ($data_item as $k_item => $v_item) {
								$selected = null;
								if ( $v_item['kode'] == $v_det['item_kode'] ) {
									$selected = 'selected';
									$satuan = $v_item['satuan'];
									$group = $v_item['group']['nama'];
								}
								$opt .= '<option value="'.$v_item['kode'].'" data-satuan="'.$v_item['satuan'].'" '.$selected.'>'.strtoupper($v_item['nama']).'</option>';
							} 
						?>
						<td><input type="text" class="form-control group uppercase" placeholder="Satuan" data-required="1" readonly value="<?php echo strtoupper($group); ?>"></td>
						<td>
							<select class="form-control item" data-required="1" onchange="beli.setSatuan(this)">
								<option value="">-- Pilih Item --</option>
								<?php echo $opt; ?>
								<!-- <?php if ( !empty($data_item) ): ?>
									<?php foreach ($data_item as $k_item => $v_item): ?>
										<?php
											$selected = null;
											if ( $v_item['kode'] == $v_det['item_kode'] ) {
												$selected = 'selected';
											}
										?>
										<option value="<?php echo $v_item['kode']; ?>" data-satuan="<?php echo $v_item['satuan']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_item['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?> -->
							</select>
						</td>
						<td>
							<input type="text" class="form-control satuan uppercase" placeholder="Satuan" data-required="1" readonly value="<?php echo strtoupper($satuan); ?>">
						</td>
						<td>
							<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal" data-required="1" maxlength="10" onblur="beli.hitTotal(this)" value="<?php echo angkaDecimal($v_det['jumlah']); ?>">
						</td>
						<td>
							<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" data-required="1" maxlength="15" onblur="beli.hitTotal(this)" value="<?php echo angkaDecimal($v_det['harga']); ?>">
						</td>
						<td>
							<input type="text" class="form-control text-right total uppercase" placeholder="Total" data-tipe="decimal" data-required="1" readonly value="<?php echo angkaDecimal($v_det['total']); ?>">
						</td>
						<td>
							<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
								<button type="button" class="btn btn-danger" onclick="beli.removeRow(this);"><i class="fa fa-minus"></i></button>
							</div>
							<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
								<button type="button" class="btn btn-primary" onclick="beli.addRow(this);"><i class="fa fa-plus"></i></button>
							</div>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-right" colspan="4"><b>Grand Total</b></td>
					<td class="grand_total text-right"><b><?php echo angkaDecimal($data['total']); ?></b></td>
					<td></td>
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
		<textarea class="form-control keterangan"><?php echo $data['keterangan'] ?></textarea>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="beli.edit(this)" data-id="<?php echo $data['kode_beli']; ?>"><i class="fa fa-save"></i> Simpan Perubahan</button>
</div>