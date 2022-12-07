<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Branch</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Kode Gudang</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-3 form-control kode uppercase" placeholder="Kode Gudang (MAX:10)" data-required="1" maxlength="10"value="<?php echo $data['kode_gudang']; ?>" readonly>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-8 form-control nama uppercase" placeholder="Nama Gudang (MAX:30)" data-required="1" maxlength="30" value="<?php echo $data['nama']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Branch</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-6 form-control branch" disabled>
								<option value="">Pilih Branch</option>
								<?php if ( !empty($branch) ): ?>
									<?php foreach ($branch as $key => $value): ?>
										<?php
											$selected = null;
											if ( $value['kode_branch'] == $data['branch_kode'] ) {
												$selected = 'selected';
											}
										?>
										<option value="<?php echo $value['kode_branch']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($value['kode_branch'].' | '.$value['nama']); ?></option>
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
			<button type="button" class="btn btn-primary pull-right" onclick="gudang.edit()">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>