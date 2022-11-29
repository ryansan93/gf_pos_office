<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-lg-8 search left-inner-addon no-padding">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_diskon" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<div class="col-lg-4 action no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="ADD" onclick="pm.modalAddForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			<?php } else { ?>
				<div class="col-lg-2 action no-padding pull-right">
					&nbsp
				</div>
			<?php } ?>
		</div>
		<small>
			<table class="table table-bordered table-hover tbl_paket_menu" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-2 text-center">Kode</th>
						<th class="col-sm-3 text-center">Nama</th>
						<th class="col-sm-3 text-center">Nama Menu</th>
						<th class="col-sm-1 text-center">Jumlah Max</th>
						<th class="col-sm-1 text-center">Action</th>
					</tr>
				</thead>
				<tbody class="list">
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr class="head" data-kode="<?php echo $v_data['kode_paket_menu']; ?>">
								<td><?php echo strtoupper($v_data['kode_paket_menu']); ?></td>
								<td><?php echo strtoupper($v_data['nama']); ?></td>
								<td><?php echo strtoupper($v_data['menu']['nama']); ?></td>
								<td class="text-right"><?php echo angkaRibuan($v_data['max_pilih']); ?></td>
								<td class="action">
									<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_edit'] == 1 ) { ?>
											<button class="btn btn-primary" onclick="pm.modalEditForm(this);"><i class="fa fa-edit"></i></button>
										<?php } ?>
									</div>
									<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_delete'] == 1 ) { ?>
											<button class="btn btn-danger" onclick="pm.delete(this);"><i class="fa fa-trash"></i></button>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr class="detail hide" style="background-color: #dedede;">
								<td colspan="5">
									<table class="table table-bordered" style="margin-bottom: 0px;">
										<thead>
											<tr>
												<th class="col-sm-6">Menu</th>
												<th class="col-sm-3">Jumlah Min</th>
												<th class="col-sm-3">Jumlah Max</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($v_data['isi_paket_menu'] as $k_ipm => $v_ipm): ?>
												<tr>
													<td><?php echo $v_ipm['menu']['nama']; ?></td>
													<td class="text-right"><?php echo angkaRibuan($v_ipm['jumlah_min']); ?></td>
													<td class="text-right"><?php echo angkaRibuan($v_ipm['jumlah_max']); ?></td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
								</td>
							</tr>
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