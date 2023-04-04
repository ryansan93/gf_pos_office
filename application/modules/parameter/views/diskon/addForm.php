<div class="modal-header header" style="padding-left: 0px; padding-right: 0px;">
	<span class="modal-title">Add Diskon</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body no-padding" style="padding-top: 10px;">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-5 no-padding" style="padding-right: 5px;">
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Branch</label></div>
					<div class="col-xs-12">
						<select class="form-control branch" multiple="multiple" data-required="1">
							<option value="">Pilih Branch</option>
							<?php foreach ($branch as $k_branch => $v_branch): ?>
								<option value="<?php echo $v_branch['kode_branch']; ?>"><?php echo strtoupper($v_branch['nama']); ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Nama Diskon</label></div>
					<div class="col-xs-12">
						<input type="text" class="col-xs-12 form-control nama uppercase" placeholder="Nama Diskon" data-required="1">
					</div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Deskripsi</label></div>
					<div class="col-xs-12">
						<textarea class="form-control deskripsi uppercase" data-required="1" placeholder="Deskripsi" data-required="1"></textarea>
					</div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Tipe Diskon</label></div>
					<div class="col-xs-12">
						<select class="form-control tipe_diskon" data-required="1" onchange="diskon.changeTipeDiskon()">
							<?php foreach ($tipe_diskon as $k_td => $v_td): ?>
								<option value="<?php echo $k_td; ?>" data-hrefdiv="<?php echo 'tipe_diskon'.$k_td; ?>"><?php echo $v_td; ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Jenis Diskon</label></div>
					<div class="col-xs-12">
						<select class="form-control requirement_diskon" data-required="1">
							<?php foreach ($requirement_diskon as $k_rd => $v_rd): ?>
								<option value="<?php echo $k_rd; ?>"><?php echo $v_rd; ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-1">
						<input type="checkbox" class="member form-check-input cursor-p" style="height: 20px; margin: 0px;">
					</div>
					<div class="col-xs-11"><label class="control-label">Member</label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-1">
						<input type="checkbox" class="non_member form-check-input cursor-p" style="height: 20px; margin: 0px;">
					</div>
					<div class="col-xs-11"><label class="control-label">Non Member</label></div>
				</div>
				<div class="col-xs-12 no-padding contain" style="padding-bottom: 10px;">
					<div class="col-xs-1">
						<input type="checkbox" class="status_ppn form-check-input cursor-p" style="height: 20px; margin: 0px;" onchange="diskon.cekCheckbox(this)">
					</div>
					<div class="col-xs-5"><label class="control-label">PB1 (%)</label></div>
					<div class="col-xs-3">
						<input type="text" class="form-control text-right ppn" placeholder="PB1" style="height: 20px;" value="0" data-tipe="decimal" maxlength="6" disabled>
					</div>
				</div>
				<div class="col-xs-12 no-padding contain" style="padding-bottom: 30px;">
					<div class="col-xs-1">
						<input type="checkbox" class="status_service_charge form-check-input cursor-p" style="height: 20px; margin: 0px;" onchange="diskon.cekCheckbox(this)">
					</div>
					<div class="col-xs-5"><label class="control-label">Service Charge (%)</label></div>
					<div class="col-xs-3">
						<input type="text" class="form-control text-right service_charge" placeholder="Service Charge" style="height: 20px;" value="0" data-tipe="decimal" maxlength="6" disabled>
					</div>
				</div>
				<div class="col-xs-12 no-padding contain">
					<div class="col-xs-1">
						<input type="checkbox" class="harga_hpp form-check-input cursor-p" style="height: 20px; margin: 0px;">
					</div>
					<div class="col-xs-5"><label class="control-label">Tampil Harga HPP</label></div>
				</div>
				<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-xs-6 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Tgl Mulai</label></div>
					<div class="col-xs-12">
						<div class="col-xs-12 input-group date datetimepicker" name="startDate" id="StartDate">
					        <input type="text" class="form-control text-center" placeholder="Start Date" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="col-xs-6 no-padding" style="padding-left: 5px; padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Tgl Berakhir</label></div>
					<div class="col-xs-12">
						<div class="col-xs-12 input-group date datetimepicker" name="endDate" id="EndDate">
					        <input type="text" class="form-control text-center" placeholder="End Date" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="col-xs-6 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Jam Mulai</label></div>
					<div class="col-xs-12">
						<div class="col-xs-12 input-group date datetimepicker" name="startTime" id="StartTime">
					        <input type="text" class="form-control text-center" placeholder="Start Time" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="col-xs-6 no-padding" style="padding-left: 5px; padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Jam Berakhir</label></div>
					<div class="col-xs-12">
						<div class="col-xs-12 input-group date datetimepicker" name="endTime" id="EndTime">
					        <input type="text" class="form-control text-center" placeholder="End Time" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
						<div class="col-xs-12"><label class="control-label">Jenis Pembayaran</label></div>
						<div class="col-xs-12">
							<select class="form-control jenis_kartu" multiple="multiple">
								<option value="">Pilih Jenis Pembayaran</option>
								<?php foreach ($jenis_kartu as $k_jk => $v_jk): ?>
									<option value="<?php echo $v_jk['kode_jenis_kartu']; ?>"><?php echo strtoupper($v_jk['nama']); ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-7 no-padding" style="padding-left: 5px;">
				<div class="col-xs-12 no-padding tipe_diskon" id="tipe_diskon1">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12"><label class="control-label" style="margin-bottom: 0px;"><?php echo $tipe_diskon[1]; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding"><div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div></div>
					<div class="col-xs-12">
						<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; border-radius: 5px; padding-top: 10px;">
							<div class="col-xs-10 no-padding" style="padding-bottom: 10px;">
								<div class="col-xs-12"><label class="control-label">Diskon</label></div>
								<div class="col-xs-12">
									<input type="text" class="col-xs-12 form-control text-right diskon" placeholder="Diskon" maxlength="11" data-tipe="decimal">
								</div>
							</div>
							<div class="col-xs-2 no-padding" style="padding-bottom: 10px;">
								<div class="col-xs-12"><label class="control-label">&nbsp;</label></div>
								<div class="col-xs-12" style="padding-left: 0px;">
									<select class="form-control diskon_jenis" style="padding-left: 3px;">
										<option value="persen">%</option>
										<option value="nilai">Rp.</option>
									</select>
								</div>
							</div>
							<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
								<div class="col-xs-12"><label class="control-label">Min Beli</label></div>
								<div class="col-xs-12">
									<input type="text" class="col-xs-2 form-control text-right min_beli" placeholder="Minimal Beli" maxlength="10" data-tipe="decimal">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 no-padding tipe_diskon hide" id="tipe_diskon2">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12"><label class="control-label" style="margin-bottom: 0px;"><?php echo $tipe_diskon[2]; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding"><div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div></div>
					<div class="col-xs-12 no-padding contain_tipe_diskon daftar_jenis_menu">
						<div class="col-xs-12">
							<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; border-radius: 5px;">
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px; height: 50px;">
									<div class="col-xs-12" style="background-color: #c3bdff; height: 100%; display: flex; justify-content: center; align-items: center;"><label class="control-label" style="margin-bottom: 0px;">Daftar Jenis / Item</label></div>
								</div>
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12"><label class="control-label">Jenis</label></div>
									<div class="col-xs-12">
										<select class="form-control jenis_menu">
											<option value="all">-- All --</option>
											<?php foreach ($jenis_menu as $k_jm => $v_jm): ?>
												<option value="<?php echo $v_jm['id']; ?>"><?php echo strtoupper($v_jm['nama']); ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12"><label class="control-label">Menu</label></div>
									<div class="col-xs-12">
										<select class="form-control menu">
											<option value="all">-- All --</option>
											<?php foreach ($menu as $k_menu => $v_menu): ?>
												<option value="<?php echo $v_menu['kode_menu']; ?>" data-jm="<?php echo $v_menu['jenis_menu_id']; ?>" data-branch="<?php echo $v_menu['branch']['kode_branch']; ?>"><?php echo $v_menu['branch']['kode_branch'].' | '.$v_menu['nama']; ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12"><label class="control-label">Jml Min</label></div>
									<div class="col-xs-12">
										<input type="text" class="col-xs-12 form-control text-right jml_min" placeholder="Jumlah Min" maxlength="3" data-tipe="integer">
									</div>
								</div>
								<div class="col-xs-10 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12"><label class="control-label">Diskon</label></div>
									<div class="col-xs-12">
										<input type="text" class="col-xs-12 form-control text-right diskon" placeholder="Diskon" maxlength="11" data-tipe="decimal">
									</div>
								</div>
								<div class="col-xs-2 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12"><label class="control-label">&nbsp;</label></div>
									<div class="col-xs-12" style="padding-left: 0px;">
										<select class="form-control diskon_jenis" style="padding-left: 3px;">
											<option value="persen">%</option>
											<option value="nilai">Rp.</option>
										</select>
									</div>
								</div>
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12">
										<button type="button" class="col-xs-12 btn btn-primary" onclick="diskon.addDaftarJenisMenu(this)"><i class="fa fa-plus"></i></button>
									</div>
								</div>

								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12">
										<small>
											<table class="table table-bordered" style="margin-bottom: 0px;">
												<thead>
													<tr>
														<th class="col-xs-3" style="padding: 3px;">Jenis</th>
														<th class="col-xs-4" style="padding: 3px;">Produk</th>
														<th class="col-xs-2" style="padding: 3px;">Jml Min</th>
														<th class="col-xs-2" style="padding: 3px;">Diskon</th>
														<th class="col-xs-1" style="padding: 3px;"></th>
													</tr>
												</thead>
												<tbody></tbody>
											</table>
										</small>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="col-xs-12 no-padding"><br></div>
					<div class="col-xs-12 no-padding contain_tipe_diskon daftar_pengecualian_jenis_menu">
						<div class="col-xs-12">
							<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; border-radius: 5px;">
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px; height: 50px;">
									<div class="col-xs-12" style="background-color: #c3bdff; height: 100%; display: flex; justify-content: center; align-items: center;"><label class="control-label" style="margin-bottom: 0px;">Daftar Pengecualian</label></div>
								</div>
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12"><label class="control-label">Jenis</label></div>
									<div class="col-xs-12">
										<select class="form-control jenis_menu">
											<option value="all">-- All --</option>
											<?php foreach ($jenis_menu as $k_jm => $v_jm): ?>
												<option value="<?php echo $v_jm['id']; ?>"><?php echo $v_jm['nama']; ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12"><label class="control-label">Menu</label></div>
									<div class="col-xs-12">
										<select class="form-control menu">
											<option value="all">-- All --</option>
											<?php foreach ($menu as $k_menu => $v_menu): ?>
												<option value="<?php echo $v_menu['kode_menu']; ?>" data-jm="<?php echo $v_menu['jenis_menu_id']; ?>" data-branch="<?php echo $v_menu['branch']['kode_branch']; ?>"><?php echo $v_menu['branch']['kode_branch'].' | '.$v_menu['nama']; ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12">
										<button type="button" class="col-xs-12 btn btn-primary"><i class="fa fa-plus"></i></button>
									</div>
								</div>

								<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
									<div class="col-xs-12">
										<small>
											<table class="table table-bordered" style="margin-bottom: 0px;">
												<thead>
													<tr>
														<th class="col-xs-4" style="padding: 3px;">Jenis</th>
														<th class="col-xs-6" style="padding: 3px;">Produk</th>
														<th class="col-xs-2" style="padding: 3px;"></th>
													</tr>
												</thead>
												<tbody></tbody>
											</table>
										</small>
									</div>
								</div>
							</div>
						</div>
					</div> -->
				</div>
				<div class="col-xs-12 no-padding tipe_diskon hide" id="tipe_diskon3">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12"><label class="control-label" style="margin-bottom: 0px;"><?php echo $tipe_diskon[3]; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding"><div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div></div>
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12">
							<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; border-radius: 5px;">
								<div class="col-xs-12 no-padding contain_tipe_diskon daftar_beli">
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px; height: 50px;">
										<div class="col-xs-12" style="background-color: #c3bdff; height: 100%; display: flex; justify-content: center; align-items: center;"><label class="control-label" style="margin-bottom: 0px;">Beli</label></div>
									</div>
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">Jenis</label></div>
										<div class="col-xs-12">
											<select class="form-control jenis_menu">
												<option value="all">-- All --</option>
												<?php foreach ($jenis_menu as $k_jm => $v_jm): ?>
													<option value="<?php echo $v_jm['id']; ?>"><?php echo $v_jm['nama']; ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">Menu</label></div>
										<div class="col-xs-12">
											<select class="form-control menu">
												<option value="all">-- All --</option>
												<?php foreach ($menu as $k_menu => $v_menu): ?>
													<option value="<?php echo $v_menu['kode_menu']; ?>" data-jm="<?php echo $v_menu['jenis_menu_id']; ?>" data-branch="<?php echo $v_menu['branch']['kode_branch']; ?>"><?php echo $v_menu['branch']['kode_branch'].' | '.$v_menu['nama']; ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">Jumlah</label></div>
										<div class="col-xs-12">
											<input type="text" class="col-xs-12 form-control text-right jumlah" placeholder="Jumlah" maxlength="10" data-tipe="integer">
										</div>
									</div>
								</div>
								<div class="col-xs-12 no-padding contain_tipe_diskon daftar_dapat">
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px; height: 50px;">
										<div class="col-xs-12" style="background-color: #c3bdff; height: 100%; display: flex; justify-content: center; align-items: center;"><label class="control-label" style="margin-bottom: 0px;">Dapat</label></div>
									</div>
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">Jenis</label></div>
										<div class="col-xs-12">
											<select class="form-control jenis_menu">
												<option value="all">-- All --</option>
												<?php foreach ($jenis_menu as $k_jm => $v_jm): ?>
													<option value="<?php echo $v_jm['id']; ?>"><?php echo $v_jm['nama']; ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">Menu</label></div>
										<div class="col-xs-12">
											<select class="form-control menu">
												<option value="all">-- All --</option>
												<?php foreach ($menu as $k_menu => $v_menu): ?>
													<option value="<?php echo $v_menu['kode_menu']; ?>" data-jm="<?php echo $v_menu['jenis_menu_id']; ?>" data-branch="<?php echo $v_menu['branch']['kode_branch']; ?>"><?php echo $v_menu['branch']['kode_branch'].' | '.$v_menu['nama']; ?></option>
												<?php endforeach ?>
											</select>
										</div>
									</div>
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">Jumlah</label></div>
										<div class="col-xs-12">
											<input type="text" class="col-xs-12 form-control text-right jumlah" placeholder="Jumlah" maxlength="10" data-tipe="integer">
										</div>
									</div>
									<div class="col-xs-10 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">Diskon</label></div>
										<div class="col-xs-12">
											<input type="text" class="col-xs-12 form-control text-right diskon" placeholder="Diskon" maxlength="11" data-tipe="decimal">
										</div>
									</div>
									<div class="col-xs-2 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12"><label class="control-label">&nbsp;</label></div>
										<div class="col-xs-12" style="padding-left: 0px;">
											<select class="form-control diskon_jenis" style="padding-left: 3px;">
												<option value="persen">%</option>
												<option value="nilai">Rp.</option>
											</select>
										</div>
									</div>
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12">
											<button type="button" class="col-xs-12 btn btn-primary" onclick="diskon.addDaftarBeliDapat(this)"><i class="fa fa-plus"></i></button>
										</div>
									</div>

									<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
										<div class="col-xs-12">
											<small>
												<table class="table table-bordered" style="margin-bottom: 0px;">
													<thead>
														<tr>
															<th class="col-xs-1" style="padding: 3px;">#</th>
															<th class="col-xs-1" style="padding: 3px;">Jml</th>
															<th class="col-xs-3" style="padding: 3px;">Produk</th>
															<th class="col-xs-1" style="padding: 3px;">#</th>
															<th class="col-xs-1" style="padding: 3px;">Jml</th>
															<th class="col-xs-3" style="padding: 3px;">Produk</th>
															<th class="col-xs-1" style="padding: 3px;">Diskon</th>
															<th class="col-xs-1" style="padding: 3px;"></th>
														</tr>
													</thead>
													<tbody></tbody>
												</table>
											</small>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <div class="col-xs-6 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Diskon (%)</label></div>
				<div class="col-xs-12">
					<input type="text" class="col-xs-12 text-right form-control persen" placeholder="Persen" maxlength="6" data-tipe="decimal">
				</div>
			</div>
			<div class="col-xs-6 no-padding" style="padding-left: 5px; padding-bottom: 10px;">
				<div class="col-xs-12"><label class="control-label">Diskon Nilai</label></div>
				<div class="col-xs-12">
					<input type="text" class="col-xs-2 form-control text-right nilai" placeholder="Nilai" maxlength="10" data-tipe="decimal">
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding" style="padding-right: 5px; padding-bottom: 10px;">
					<div class="col-xs-12"><label class="control-label">Min Beli</label></div>
					<div class="col-xs-12">
						<input type="text" class="col-xs-2 form-control text-right min_beli" placeholder="Minimal Beli" maxlength="10" data-tipe="decimal">
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
						</tbody>
					</table>
				</small>
			</div> -->
		</div>
		<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12">
			<button type="button" class="btn btn-primary pull-right" onclick="diskon.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>