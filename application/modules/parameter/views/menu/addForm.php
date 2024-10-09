<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Nama</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="form-control nama uppercase" placeholder="Nama" data-required="1">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Deskripsi</label></div>
				<div class="col-xs-12 no-padding">
					<textarea class="form-control deskripsi uppercase" placeholder="Deskripsi"></textarea>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Branch</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control branch" name="branch[]" multiple="multiple" data-required="1">
						<?php if ( !empty($branch) ): ?>
							<?php foreach ($branch as $key => $val): ?>
								<option value="<?php echo $val['kode_branch']; ?>"><?php echo strtoupper($val['nama']); ?></option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding" style="padding-right: 5px;">
					<div class="col-xs-12 no-padding"><label class="control-label">Kategori</label></div>
					<div class="col-xs-12 no-padding">
						<select class="form-control kategori" data-required="1">
							<?php if ( !empty($kategori) ): ?>
								<?php foreach ($kategori as $key => $val): ?>
									<option value="<?php echo $val['id']; ?>"><?php echo strtoupper($val['nama']); ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
					</div>
				</div>
				<div class="col-xs-6 no-padding" style="padding-left: 5px;">
					<div class="col-xs-12 no-padding"><label class="control-label">Jenis</label></div>
					<div class="col-xs-12 no-padding">
						<select class="form-control jenis" data-required="1">
							<?php if ( !empty($jenis) ): ?>
								<?php foreach ($jenis as $key => $val): ?>
									<option value="<?php echo $val['id']; ?>"><?php echo strtoupper($val['nama']); ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding"><label class="control-label">Image</label></div>
				<div class="col-xs-12 no-padding">
					<label class="" style="padding-right: 10px;">
						<input type="file" onchange="menu.showNameFile(this)" class="file_lampiran" name="" data-allowtypes="jpg|jpeg|png" style="display: none;">
						<i class="fa fa-file-image-o cursor-p" style="border: solid #aaa 1px; padding: 5px 10px; border-radius: 3px;"></i>
					</label>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding"><label class="control-label">Additional</label></div>
				<div class="col-xs-12 no-padding" style="padding-left: 15px;">
					<input type="radio" id="1" name="age" value="1">
  					<label for="1">Ya</label><br>
  					<input type="radio" id="0" name="age" value="0" checked>
  					<label for="0">Tidak</label><br>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-2 no-padding"><label class="control-label">PB1</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="ppn col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;">
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-2 no-padding"><label class="control-label">Service Charge</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="service_charge col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;">
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<small>
					<table class="table table-bordered tbl_jenis_pesanan" style="margin-bottom: 0px;">
						<thead>
							<tr>
								<th class="col-xs-8">Jenis Pesanan</th>
								<th class="col-xs-4">Harga (Rp.)</th>
							</tr>
						</thead>
						<tbody>
							<?php if ( !empty($jenis_pesanan) ): ?>
								<?php foreach ($jenis_pesanan as $key => $val): ?>
									<tr class="data">
										<td class="kode" data-val="<?php echo $val['kode']; ?>"><?php echo strtoupper($val['nama']); ?></td>
										<td>
											<input type="text" class="form-control text-right harga" data-required="1" data-tipe="decimal" placeholder="Harga" maxlength="14">
										</td>
									</tr>
								<?php endforeach ?>
							<?php else: ?>
								<tr>
									<td colspan="2">Data jenis pesanan tidak ditemukan.</td>
								</tr>
							<?php endif ?>
						</tbody>
					</table>
				</small>
			</div>
		</div>
		<div class="col-xs-12 no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="menu.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>