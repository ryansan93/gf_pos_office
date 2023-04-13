<div class="modal-header header" style="padding-left: 0px; padding-right: 0px;">
	<span class="modal-title">Add Item</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Kode</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="col-sm-12 form-control kode uppercase" placeholder="Kode (MAX : 25)" data-required="1" maxlength="25">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Nama</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="col-sm-12 form-control nama uppercase" placeholder="Nama (MAX : 50)" data-required="1" maxlength="50">
				</div>
			</div>
			<div class="col-xs-12 no-padding hide" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Brand</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="col-sm-12 form-control brand uppercase" placeholder="Brand (MAX : 50)" maxlength="50">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Group</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control group" data-required="1">
						<option>-- Pilih Group --</option>
						<?php foreach ($group as $key => $value): ?>
							<option value="<?php echo $value['kode']; ?>"><?php echo $value['nama']; ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding hide" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Spesifikasi</label></div>
				<div class="col-xs-12 no-padding">
					<textarea class="form-control keterangan" placeholder="Spesifikasi"></textarea>
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
								<tr>
									<td>
										<input type="text" class="form-control uppercase satuan" placeholder="SATUAN" data-required="1" maxlength="10">
									</td>
									<td>
										<input type="text" class="form-control uppercase text-right pengali" placeholder="PENGALI" data-tipe="decimal" data-required="1" maxlength="11">
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
							</tbody>
						</table>
					</small>
				</div>
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr></div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="item.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>