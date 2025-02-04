<div class="row content-panel detailed">
	<div class="col-xs-12 detailed">
		<!-- <div class="col-xs-8 search left-inner-addon no-padding">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_harga" placeholder="Search" onkeyup="filter_all(this)">
		</div> -->
		<div class="col-xs-12 no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<div class="col-xs-12 no-padding">
					<div class="col-lg-6 no-padding" style="padding-right: 5px;">
						<button id="btn-add" type="button" data-href="action" class="col-xs-12 btn btn-primary cursor-p pull-right" title="ADD" onclick="hm.modalAddForm(this)"> 
							<i class="fa fa-plus" aria-hidden="true"></i> ADD
						</button>
					</div>
					<div class="col-lg-6 no-padding" style="padding-left: 5px;">
						<button type="button" class="col-lg-12 btn btn-default pull-right" onclick="hm.importForm(this)" data-href="action" data-edit=""><i class="fa fa-upload"></i> Import Data</button>
					</div>
				</div>
				<div class="col-xs-12 no-padding">
					<hr style="margin-top: 10px; margin-bottom: 10px;">
				</div>
			<?php } ?>
		</div>
		<small>
			<table class="table table-bordered table-hover tbl_harga" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<td>
							<select class="form-control branch" data-required="1">
								<option value="">-- All --</option>
								<?php if ( !empty($branch) ): ?>
									<?php foreach ($branch as $key => $val): ?>
										<option value="<?php echo $val['kode_branch']; ?>"><?php echo strtoupper($val['kode_branch']).' | '.strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
						<td>
							<select class="form-control menu" data-required="1" disabled>
								<option value="">-- All --</option>
								<?php if ( !empty($menu) ): ?>
									<?php foreach ($menu as $key => $val): ?>
										<option value="<?php echo $val['kode_menu']; ?>" data-branch="<?php echo $val['branch_kode']; ?>"><?php echo strtoupper($val['jenis']['nama']).' | '.strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
						<td>
							<select class="form-control jenis_pesanan" data-required="1">
								<option value="">-- All --</option>
								<?php if ( !empty($jenis_pesanan) ): ?>
									<?php foreach ($jenis_pesanan as $key => $val): ?>
										<option value="<?php echo $val['kode']; ?>"><?php echo strtoupper($val['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<th class="col-sm-2 text-center">Branch</th>
						<th class="col-sm-3 text-center">Menu</th>
						<th class="col-sm-2 text-center">Jenis Pesanan</th>
						<th class="col-sm-1 text-center">Tgl Berlaku</th>
						<th class="col-sm-1 text-center">Harga</th>
						<th class="col-sm-1 text-center">Action</th>
					</tr>
				</thead>
				<tbody class="list">
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr class="search head" data-branch="<?php echo $v_data['menu']['branch']['kode_branch']; ?>" data-menu="<?php echo $v_data['menu_kode']; ?>" data-jp="<?php echo $v_data['jenis_pesanan_kode']; ?>">
								<td class="branch" data-val="<?php echo $v_data['menu']['branch']['kode_branch']; ?>"><?php echo strtoupper($v_data['menu']['branch']['nama']); ?></td>
								<td class="menu" data-val="<?php echo $v_data['menu_kode']; ?>"><?php echo strtoupper($v_data['menu']['nama']); ?></td>
								<td class="jenis_pesanan" data-val="<?php echo $v_data['jenis_pesanan_kode']; ?>"><?php echo strtoupper($v_data['jenis_pesanan']['nama']); ?></td>
								<td class="text-center tgl_mulai" data-val="<?php echo $v_data['tgl_mulai']; ?>"><?php echo tglIndonesia($v_data['tgl_mulai'], '-', ' '); ?></td>
								<td class="text-right harga" data-val="<?php echo $v_data['harga']; ?>"><?php echo angkaDecimal($v_data['harga']); ?></td>
								<td>
									<!-- <div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_edit'] == 1 ) { ?>
											<button class="btn btn-primary" onclick="hm.modalEditForm(this);"><i class="fa fa-edit"></i></button>
										<?php } ?>
									</div> -->
									<div class="col-sm-12 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_delete'] == 1 ) { ?>
											<?php if ( $v_data['tgl_mulai'] > date('Y-m-d') ): ?>
												<button class="btn btn-danger" onclick="hm.delete(this);"><i class="fa fa-trash"></i></button>
											<?php endif ?>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr class="detail hide"></tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="5">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>