<div class="modal-header header" style="padding-left: 0px; padding-right: 0px;">
	<span class="modal-title">Detail Diskon</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body no-padding" style="padding-top: 10px;">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-5 no-padding" style="padding-right: 5px;">
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Branch</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo $data['nama_branch']; ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Nama Diskon</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo $data['nama_diskon']; ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Deskripsi</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo $data['deskripsi']; ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Tipe Diskon</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo $data['nama_tipe_diskon']; ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Jenis Diskon</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo $data['nama_jenis_diskon']; ?></label></div>
				</div>
				<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Member</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7">
						<label class="control-label">
							<?php if ( $data['member'] == 1 ): ?>
								<i class="fa fa-check"></i>
							<?php else: ?>
								<i class="fa fa-minus"></i>
							<?php endif ?>
						</label>
					</div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Non Member</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7">
						<label class="control-label">
							<?php if ( $data['non_member'] == 1 ): ?>
								<i class="fa fa-check"></i>
							<?php else: ?>
								<i class="fa fa-minus"></i>
							<?php endif ?>
						</label>
					</div>
				</div>
				<div class="col-xs-12 no-padding contain" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">PB1 (%)</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7">
						<label class="control-label">
							<?php if ( $data['status_ppn'] == 1 ): ?>
								<i class="fa fa-check"></i> <span>(<?php echo $data['ppn']; ?>)</span>
							<?php else: ?>
								<i class="fa fa-minus"></i>
							<?php endif ?>
						</label>
					</div>
				</div>
				<div class="col-xs-12 no-padding contain" style="padding-bottom: 30px;">
					<div class="col-xs-4"><label class="control-label">Service Charge (%)</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7">
						<label class="control-label">
							<?php if ( $data['status_service_charge'] == 1 ): ?>
								<i class="fa fa-check"></i> <span>(<?php echo $data['service_charge']; ?>)</span>
							<?php else: ?>
								<i class="fa fa-minus"></i>
							<?php endif ?>
						</label>
					</div>
				</div>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-4"><label class="control-label">Tampil Harga HPP</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7">
						<label class="control-label">
							<?php if ( $data['harga_hpp'] == 1 ): ?>
								<i class="fa fa-check"></i>
							<?php else: ?>
								<i class="fa fa-minus"></i>
							<?php endif ?>
						</label>
					</div>
				</div>
				<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Tgl Mulai</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo tglIndonesia($data['tgl_mulai'], '-', ' '); ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Tgl Berakhir</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo tglIndonesia($data['tgl_berakhir'], '-', ' '); ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Jam Mulai</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo substr($data['jam_mulai'], 0, 8); ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Jam Berakhir</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
					<div class="col-xs-7"><label class="control-label"><?php echo substr($data['jam_berakhir'], 0, 8); ?></label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
					<div class="col-xs-4"><label class="control-label">Jenis Pembayaran</label></div>
					<div class="col-xs-1"><label class="control-label">:</label></div>
				</div>
				<div class="col-xs-12 no-padding" style="padding-left: 15px; padding-bottom: 10px;">
					<ul>
						<?php foreach ($data['jenis_kartu'] as $k_jk => $v_jk): ?>
							<li><label class="control-label"><?php echo $v_jk['nama_kartu']; ?></label></li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>

			<?php
				$hide_tipe_diskon1 = 'hide';
				$hide_tipe_diskon2 = 'hide';
				$hide_tipe_diskon3 = 'hide';

				if ( $data['tipe_diskon'] == 1 ) {
					$hide_tipe_diskon1 = '';
				}

				if ( $data['tipe_diskon'] == 2 ) {
					$hide_tipe_diskon2 = '';
				}

				if ( $data['tipe_diskon'] == 3 ) {
					$hide_tipe_diskon3 = '';
				}
			?>

			<div class="col-xs-7 no-padding" style="padding-left: 5px;">
				<div class="col-xs-12 no-padding tipe_diskon <?php echo $hide_tipe_diskon1; ?>" id="tipe_diskon1">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12"><label class="control-label" style="margin-bottom: 0px;"><?php echo $data['nama_tipe_diskon']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding"><div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div></div>
					<div class="col-xs-12">
						<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; border-radius: 5px; padding-top: 10px;">
							<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
								<div class="col-xs-3"><label class="control-label">Diskon</label></div>
								<div class="col-xs-1"><label class="control-label">:</label></div>
								<div class="col-xs-8"><label class="control-label"><?php echo ($data['detail']['jenis'] == 'persen') ? angkaDecimal($data['detail']['diskon']).' %' : 'Rp. '.angkaDecimal($data['detail']['diskon']); ?></label></div>
							</div>
							<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
								<div class="col-xs-3"><label class="control-label">Min Beli</label></div>
								<div class="col-xs-1"><label class="control-label">:</label></div>
								<div class="col-xs-8"><label class="control-label"><?php echo angkaDecimal($data['detail']['min_beli']); ?></label></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 no-padding tipe_diskon <?php echo $hide_tipe_diskon2; ?>" id="tipe_diskon2">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12"><label class="control-label" style="margin-bottom: 0px;"><?php echo $data['nama_tipe_diskon']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding"><div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div></div>
					<div class="col-xs-12 no-padding contain_tipe_diskon daftar_jenis_menu">
						<div class="col-xs-12">
							<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; border-radius: 5px;">
								<div class="col-xs-12 no-padding" style="padding-bottom: 10px; height: 50px;">
									<div class="col-xs-12" style="background-color: #c3bdff; height: 100%; display: flex; justify-content: center; align-items: center;"><label class="control-label" style="margin-bottom: 0px;">Daftar Jenis / Item</label></div>
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
														<th class="col-xs-3" style="padding: 3px;">Diskon</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($data['detail'] as $k_det => $v_det): ?>
														<tr>
															<td><?php echo strtoupper($v_det['nama_jenis_menu']); ?></td>
															<td><?php echo $v_det['nama_menu']; ?></td>
															<td class="text-right"><?php echo angkaRibuan($v_det['jml_min']); ?></td>
															<td class="text-right"><?php echo ($v_det['diskon_jenis'] == 'persen') ? angkaDecimal($v_det['diskon']).' %' : 'Rp. '.angkaDecimal($v_det['diskon']); ?></td>
														</tr>
													<?php endforeach ?>
												</tbody>
											</table>
										</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 no-padding tipe_diskon <?php echo $hide_tipe_diskon3; ?>" id="tipe_diskon3">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12"><label class="control-label" style="margin-bottom: 0px;"><?php echo $data['nama_tipe_diskon']; ?></label></div>
					</div>
					<div class="col-xs-12 no-padding"><div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div></div>
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12">
							<div class="col-xs-12 no-padding" style="border: 1px solid #dedede; border-radius: 5px;">
								<div class="col-xs-12 no-padding contain_tipe_diskon daftar_beli">
									<div class="col-xs-12 no-padding" style="padding-bottom: 10px; height: 50px;">
										<div class="col-xs-12" style="background-color: #c3bdff; height: 100%; display: flex; justify-content: center; align-items: center;"><label class="control-label" style="margin-bottom: 0px;">Detail</label></div>
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
															<th class="col-xs-2" style="padding: 3px;">Diskon</th>
														</tr>
													</thead>
													<tbody>
														<?php foreach ($data['detail'] as $k_det => $v_det): ?>
															<tr>
																<td><b>BUY</b></td>
																<td class="text-right"><?php echo angkaRibuan($v_det['jumlah_beli']); ?></td>
																<td>
																	<div class="col-xs-12 no-padding">> <?php echo strtoupper($v_det['nama_jenis_menu_beli']); ?></div>
																	<div class="col-xs-12 no-padding">> <?php echo $v_det['nama_menu_beli'] ?></div>
																</td>
																<td><b>GET</b></td>
																<td class="text-right"><?php echo angkaRibuan($v_det['jumlah_dapat']); ?></td>
																<td>
																	<div class="col-xs-12 no-padding">> <?php echo strtoupper($v_det['nama_jenis_menu_dapat']); ?></div>
																	<div class="col-xs-12 no-padding">> <?php echo $v_det['nama_menu_dapat'] ?></div>
																</td>
																<td class="text-right"><?php echo ($v_det['diskon_jenis_dapat'] == 'persen') ? angkaDecimal($v_det['diskon_dapat']).' %' : 'Rp. '.angkaDecimal($v_det['diskon_dapat']); ?></td>
															</tr>
														<?php endforeach ?>
														</tbody>
													</tbody>
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
		</div>
	</div>
</div>