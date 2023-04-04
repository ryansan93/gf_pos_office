<!-- <div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Diskon</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama Diskon</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-8 form-control nama uppercase" placeholder="Nama Diskon" data-required="1" value="<?php echo $data['nama']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Deskripsi</label>
						</td>
						<td class="col-sm-10">
							<textarea class="form-control deskripsi uppercase" data-required="1" placeholder="Deskripsi" data-required="1"><?php echo $data['deskripsi']; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Tgl Mulai</label>
						</td>
						<td class="col-sm-10">
							<div class="col-sm-3 input-group date datetimepicker" name="startDate" id="StartDate">
						        <input type="text" class="form-control text-center" placeholder="Start Date" data-required="1" data-tgl="<?php echo $data['start_date']; ?>" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Tgl Berakhir</label>
						</td>
						<td class="col-sm-10">
							<div class="col-sm-3 input-group date datetimepicker" name="endDate" id="EndDate">
						        <input type="text" class="form-control text-center" placeholder="End Date" data-required="1" data-tgl="<?php echo $data['end_date']; ?>" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Level</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-2 form-control level" data-required="1">
								<option value="">Pilih Level</option>
								<option value="1" <?php echo ($data['level'] == 1) ? 'selected' : ''; ?> >1</option>
								<option value="2" <?php echo ($data['level'] == 2) ? 'selected' : ''; ?> >2</option>
								<option value="3" <?php echo ($data['level'] == 3) ? 'selected' : ''; ?> >3</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Diskon Persen</label>
						</td>
						<td class="col-sm-10">
							<div class="col-sm-2 no-padding">
								<input type="text" class="col-sm-12 text-right form-control persen" placeholder="Persen" maxlength="6" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['persen']); ?>">
							</div>
							<div class="col-sm-1 text-center no-padding">
								%
							</div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Diskon Nilai</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control text-right nilai" placeholder="Nilai" maxlength="10" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['nilai']); ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Non Member</label>
						</td>
						<td class="col-sm-10 text-left">
							<input type="checkbox" class="non_member col-sm-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['detail'][0]['non_member'] == 1) ? 'checked' : ''; ?>>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Member</label>
						</td>
						<td class="col-sm-10 text-left">
							<input type="checkbox" class="member col-sm-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['detail'][0]['member'] == 1) ? 'checked' : ''; ?>>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Minimal Beli</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control text-right min_beli" placeholder="Minimal Beli" maxlength="10" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['min_beli']); ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="diskon.edit(this)" data-kode="<?php echo $data['kode']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div> -->

<div class="modal-header header" style="padding-left: 0px; padding-right: 0px;">
	<span class="modal-title">Edit Diskon</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body no-padding" style="padding-top: 10px;">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Branch</label></div>
				<div class="col-xs-12">
					<select class="form-control branch" multiple="multiple" data-required="1">
						<option value="">Pilih Branch</option>
						<?php foreach ($branch as $k_branch => $v_branch): ?>
							<?php
								$selected = null;
								if ( $v_branch['kode_branch'] == $data['branch_kode'] ) {
									$selected = 'selected';
								}
							?>
							<option value="<?php echo $v_branch['kode_branch']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_branch['nama']); ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Nama Diskon</label></div>
				<div class="col-xs-12">
					<input type="text" class="col-xs-12 form-control nama uppercase" placeholder="Nama Diskon" value="<?php echo $data['nama']; ?>" data-required="1">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Deskripsi</label></div>
				<div class="col-xs-12">
					<textarea class="form-control deskripsi uppercase" data-required="1" placeholder="Deskripsi" data-required="1"><?php echo $data['deskripsi']; ?></textarea>
				</div>
			</div>
			<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-2"><label class="control-label">Member</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="member col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['detail'][0]['member'] == 1) ? 'checked' : ''; ?> >
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-2"><label class="control-label">Non Member</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="non_member col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['detail'][0]['non_member'] == 1) ? 'checked' : ''; ?> >
				</div>
			</div>
			<div class="col-xs-12 no-padding contain" style="padding-bottom: 10px;">
				<div class="col-xs-2"><label class="control-label">PB1 (%)</label></div>
				<div class="col-xs-1">
					<input type="checkbox" class="status_ppn col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 47%;" onchange="diskon.cekCheckbox(this)" <?php echo ($data['status_ppn'] == 1) ? 'checked' : ''; ?> >
				</div>
				<div class="col-xs-1 no-padding">
					<input type="text" class="form-control text-right ppn" placeholder="PB1" style="height: 20px;" value="<?php echo angkaDecimal($data['ppn']) ?>" data-tipe="decimal" maxlength="6" <?php echo ($data['status_ppn'] == 1) ? '' : 'disabled'; ?> >
				</div>
			</div>
			<div class="col-xs-12 no-padding contain" style="padding-bottom: 30px;">
				<div class="col-xs-2"><label class="control-label">Service Charge (%)</label></div>
				<div class="col-xs-1">
					<input type="checkbox" class="status_service_charge col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 47%;" onchange="diskon.cekCheckbox(this)" <?php echo ($data['status_service_charge'] == 1) ? 'checked' : ''; ?> >
				</div>
				<div class="col-xs-1 no-padding">
					<input type="text" class="form-control text-right service_charge" placeholder="Service Charge" style="height: 20px;" value="<?php echo angkaDecimal($data['service_charge']) ?>" data-tipe="decimal" maxlength="6" <?php echo ($data['status_service_charge'] == 1) ? '' : 'disabled'; ?> >
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-2"><label class="control-label">Tampil Harga HPP</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="harga_hpp col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['detail'][0]['harga_hpp'] == 1) ? 'checked' : ''; ?> >
				</div>
			</div>
			<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-3 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Tgl Mulai</label></div>
				<div class="col-xs-12">
					<div class="col-xs-12 input-group date datetimepicker" name="startDate" id="StartDate">
				        <input type="text" class="form-control text-center" placeholder="Start Date" data-required="1" data-tgl="<?php echo $data['start_date']; ?>" />
				        <span class="input-group-addon">
				            <span class="glyphicon glyphicon-calendar"></span>
				        </span>
				    </div>
				</div>
			</div>
			<div class="col-xs-3 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Tgl Berakhir</label></div>
				<div class="col-xs-12">
					<div class="col-xs-12 input-group date datetimepicker" name="endDate" id="EndDate">
				        <input type="text" class="form-control text-center" placeholder="End Date" data-required="1" data-tgl="<?php echo $data['end_date']; ?>" />
				        <span class="input-group-addon">
				            <span class="glyphicon glyphicon-calendar"></span>
				        </span>
				    </div>
				</div>
			</div>
			<div class="col-xs-3 no-padding" style="padding-left: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Jam Mulai</label></div>
				<div class="col-xs-12">
					<div class="col-xs-12 input-group date datetimepicker" name="startTime" id="StartTime">
				        <input type="text" class="form-control text-center" placeholder="Start Time" data-required="1" data-jam="<?php echo $data['start_time']; ?>" />
				        <span class="input-group-addon">
				            <span class="glyphicon glyphicon-calendar"></span>
				        </span>
				    </div>
				</div>
			</div>
			<div class="col-xs-3 no-padding" style="padding-left: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Jam Berakhir</label></div>
				<div class="col-xs-12">
					<div class="col-xs-12 input-group date datetimepicker" name="endTime" id="EndTime">
				        <input type="text" class="form-control text-center" placeholder="End Time" data-required="1" data-jam="<?php echo $data['end_time']; ?>" />
				        <span class="input-group-addon">
				            <span class="glyphicon glyphicon-calendar"></span>
				        </span>
				    </div>
				</div>
			</div>
			<div class="col-xs-6 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Diskon (%)</label></div>
				<div class="col-xs-12">
					<input type="text" class="col-xs-12 text-right form-control persen" placeholder="Persen" maxlength="6" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['persen']); ?>" >
				</div>
			</div>
			<div class="col-xs-6 no-padding" style="padding-left: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Diskon Nilai</label></div>
				<div class="col-xs-12">
					<input type="text" class="col-xs-2 form-control text-right nilai" placeholder="Nilai" maxlength="10" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['nilai']); ?>" >
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Min Beli</label></div>
					<div class="col-xs-12">
						<input type="text" class="col-xs-2 form-control text-right min_beli" placeholder="Minimal Beli" maxlength="10" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['min_beli']); ?>">
					</div>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Jenis Kartu</label></div>
					<div class="col-xs-12">
						<select class="form-control jenis_kartu" multiple="multiple">
							<option value="">Pilih Jenis Kartu</option>
							<?php foreach ($jenis_kartu as $k_jk => $v_jk): ?>
								<?php
									$selected = null;
									if ( !empty($data['diskon_jenis_kartu']) ) {
										foreach ($data['diskon_jenis_kartu'] as $k_djk => $v_djk) {
											if ( $v_jk['kode_jenis_kartu'] == $v_djk['jenis_kartu_kode'] ) {
												$selected = 'selected';

												break;
											}
										}
									}
								?>
								<option value="<?php echo $v_jk['kode_jenis_kartu']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_jk['nama']); ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-xs-12">
				<small>
					<table class="table table-bordered" style="margin-bottom: 0px;">
						<thead>
							<tr>
								<th class="col-xs-6">Menu</th>
								<th class="col-xs-4">Jumlah Min</th>
								<th class="col-xs-2"></th>
							</tr>
						</thead>
						<tbody>
							<?php if ( !empty($data['diskon_menu']) ): ?>
								<?php foreach ($data['diskon_menu'] as $k_dm => $v_dm): ?>
									<tr>
										<td>
											<select class="form-control menu">
												<option value="">Pilih Menu</option>
												<?php foreach ($menu as $k_menu => $v_menu): ?>
													<?php
														$selected = null;
														if ( !empty($data['diskon_jenis_kartu']) ) {
															if ( $v_menu['kode_menu'] == $v_dm['menu_kode'] ) {
																$selected = 'selected';
															}
														}
													?>
													<option value="<?php echo $v_menu['kode_menu']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($v_menu['branch_kode'].' | '.$v_menu['nama']); ?></option>
												<?php endforeach ?>
											</select>
										</td>
										<td>
											<input type="text" class="col-xs-12 form-control text-right jumlah_min" placeholder="Jumlah Minimal" maxlength="5" data-tipe="integer" value="<?php echo angkaRibuan($v_dm['jumlah_min']); ?>" >
										</td>
										<td>
											<div class="col-xs-6 no-padding" style="padding-right: 5px;">
												<button type="button" class="col-xs-12 btn btn-primary" onclick="diskon.addRowMenu(this)"><i class="fa fa-plus"></i></button>
											</div>
											<div class="col-xs-6 no-padding" style="padding-left: 5px;">
												<button type="button" class="col-xs-12 btn btn-danger" onclick="diskon.removeRowMenu(this)"><i class="fa fa-times"></i></button>
											</div>
										</td>
									</tr>
								<?php endforeach ?>
							<?php else: ?>
								<tr>
									<td>
										<select class="form-control menu">
											<option value="">Pilih Menu</option>
											<?php foreach ($menu as $k_menu => $v_menu): ?>
												<option value="<?php echo $v_menu['kode_menu']; ?>"><?php echo strtoupper($v_menu['branch_kode'].' | '.$v_menu['nama']); ?></option>
											<?php endforeach ?>
										</select>
									</td>
									<td>
										<input type="text" class="col-xs-12 form-control text-right jumlah_min" placeholder="Jumlah Minimal" maxlength="5" data-tipe="integer">
									</td>
									<td>
										<div class="col-xs-6 no-padding" style="padding-right: 5px;">
											<button type="button" class="col-xs-12 btn btn-primary" onclick="diskon.addRowMenu(this)"><i class="fa fa-plus"></i></button>
										</div>
										<div class="col-xs-6 no-padding" style="padding-left: 5px;">
											<button type="button" class="col-xs-12 btn btn-danger" onclick="diskon.removeRowMenu(this)"><i class="fa fa-times"></i></button>
										</div>
									</td>
								</tr>
							<?php endif ?>
						</tbody>
					</table>
				</small>
			</div>
		</div>
		<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12">
			<button type="button" class="btn btn-primary pull-right" onclick="diskon.edit(this)" data-kode="<?php echo $data['kode']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>