<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Nama</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="form-control nama uppercase" placeholder="Nama" data-required="1" value="<?php echo $data['nama']; ?>">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Deskripsi</label></div>
				<div class="col-xs-12 no-padding">
					<textarea class="form-control deskripsi uppercase" placeholder="Deskripsi"><?php echo $data['deskripsi']; ?></textarea>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Branch</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control branch" name="branch[]" multiple="multiple" data-required="1" disabled>
						<?php if ( !empty($branch) ): ?>
							<?php foreach ($branch as $key => $val): ?>
								<?php
									$selected = '';
									if ( $val['kode_branch'] == $data['branch_kode'] ) {
										$selected = 'selected';
									}
								?>
								<option value="<?php echo $val['kode_branch']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
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
									<?php
										$selected = '';
										if ( $val['id'] == $data['kategori_menu_id'] ) {
											$selected = 'selected';
										}
									?>
									<option value="<?php echo $val['id']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
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
									<?php
										$selected = '';
										if ( $val['id'] == $data['jenis_menu_id'] ) {
											$selected = 'selected';
										}
									?>
									<option value="<?php echo $val['id']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
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
						<input type="file" onchange="menu.showNameFile(this)" class="file_lampiran" name="" data-allowtypes="jpg|jpeg|png" style="display: none;" data-filename="<?php echo ( isset($data['file_name']) && !empty($data['file_name']) ) ? $data['file_name'] : ''; ?>" data-pathname="<?php echo ( isset($data['path_name']) && !empty($data['path_name']) ) ? $data['path_name'] : ''; ?>">
						<i class="fa fa-file-image-o cursor-p" style="border: solid #aaa 1px; padding: 5px 10px; border-radius: 3px;"></i>
					</label>
					<a href="uploads/<?php echo $data['path_name']; ?>" target="_blank"><?php echo $data['file_name']; ?></a>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding"><label class="control-label">Additional</label></div>
				<div class="col-xs-12 no-padding" style="padding-left: 15px;">
					<input type="radio" id="1" name="age" value="1" <?php echo ($data['additional'] == 1) ? 'checked' : ''; ?> >
  					<label for="1">Ya</label><br>
  					<input type="radio" id="0" name="age" value="0" <?php echo ($data['additional'] == 0) ? 'checked' : ''; ?> >
  					<label for="0">Tidak</label><br>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-2 no-padding"><label class="control-label">PB1</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="ppn col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['ppn'] == 1) ? 'checked' : ''; ?> >
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-2 no-padding"><label class="control-label">Service Charge</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="service_charge col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['service_charge'] == 1) ? 'checked' : ''; ?> >
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
											<input type="text" class="form-control text-right harga" data-required="1" data-tipe="decimal" placeholder="Harga" maxlength="14" value="<?php echo angkaRibuan( $val['harga'] ); ?>">
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
			<button type="button" class="btn btn-primary pull-right" onclick="menu.edit(this)" data-kode="<?php echo $data['kode_menu']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>