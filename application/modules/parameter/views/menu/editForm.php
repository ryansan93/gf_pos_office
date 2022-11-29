<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Menu</span>
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
							<input type="text" class="col-sm-6 form-control nama uppercase" placeholder="Nama" data-required="1" value="<?php echo $data['nama']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Deskripsi</label>
						</td>
						<td class="col-sm-10">
							<textarea class="col-sm-12 form-control deskripsi uppercase" placeholder="Deskripsi"><?php echo $data['deskripsi']; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Kategori</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-4 form-control kategori">
								<option value="">-- Pilih Kategori --</option>
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
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Induk Menu</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-4 form-control induk_menu" data-required="1">
								<option value="">-- Pilih Induk Menu --</option>
								<?php if ( !empty($induk_menu) ): ?>
									<?php foreach ($induk_menu as $key => $val): ?>
										<?php
											$selected = null;
											if ( $val['id'] == $data['induk_menu_id'] ) {
												$selected = 'selected';
											}
										?>
										<option value="<?php echo $val['id']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="menu.edit(this)" data-kode="<?php echo $data['kode_menu']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>