<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Item</span>
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
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="item.edit(this)" data-kode="<?php echo $data['kode']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>