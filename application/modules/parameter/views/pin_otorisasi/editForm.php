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
							<label class="control-label">User</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-12 form-control user" data-required="1" onchange="po.setNama(this)">
								<option value="">-- Pilih User --</option>
								<?php $nama_user = null; ?>
								<?php if ( !empty($user) ): ?>
									<?php foreach ($user as $k_user => $v_user): ?>
										<?php
											$selected = null;
											if ( $v_user['id_user'] == $data['user_id'] ) {
												$selected = 'selected';
												$nama_user = $v_user['detail_user']['nama_detuser'];
											}
										?>
										<option value="<?php echo $v_user['id_user'] ?>" data-nama="<?php echo $v_user['detail_user']['nama_detuser']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_user['id_user'].' | '.$v_user['detail_user']['nama_detuser']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-8 form-control nama uppercase" placeholder="Nama" data-required="1" readonly value="<?php echo $nama_user; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">PIN</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-4 form-control pin" placeholder="PIN" data-required="1" data-tipe="angka" readonly="" value="<?php echo $data['pin']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Fitur</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-12 form-control fitur" data-required="1">
								<option value="">-- Pilih Fitur --</option>
								<?php if ( !empty($fitur) ): ?>
									<?php foreach ($fitur as $k_fitur => $v_fitur): ?>
										<?php
											$selected = null;
											if ( $v_fitur['id_detfitur'] == $data['id_detfitur'] ) {
												$selected = 'selected';
											}
										?>
										<option value="<?php echo $v_fitur['id_detfitur'] ?>" <?php echo $selected; ?> ><?php echo $v_fitur['id_detfitur'].' | '.$v_fitur['nama_fitur'].' | '.$v_fitur['nama_detfitur']; ?></option>
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
			<button type="button" class="btn btn-primary pull-right" onclick="po.edit(this)" data-id="<?php echo $data['id']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>