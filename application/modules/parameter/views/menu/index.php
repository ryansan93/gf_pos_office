<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-lg-8 search left-inner-addon no-padding">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_menu" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<div class="col-lg-4 action no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="ADD" onclick="menu.modalAddForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			<?php } else { ?>
				<div class="col-lg-2 action no-padding pull-right">
					&nbsp
				</div>
			<?php } ?>
		</div>
		<small>
			<table class="table table-bordered table-hover tbl_menu" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-1 text-center">Kode</th>
						<th class="col-sm-2 text-center">Nama</th>
						<th class="col-sm-3 text-center">Deskripsi</th>
						<th class="col-sm-1 text-center">Jenis</th>
						<th class="col-sm-1 text-center">Kategori</th>
						<th class="col-sm-1 text-center">Branch</th>
						<th class="col-sm-1 text-center">Additional</th>
						<th class="col-sm-1 text-center">Status</th>
						<th class="col-sm-1 text-center">Action</th>
					</tr>
				</thead>
				<tbody class="list">
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<?php 
								$status = 'removed';
								if ( $v_data['status'] == 1 ) {
									$status = 'submitted';
								}
							?>
							<tr class="search head <?php echo $status; ?>" data-kode="<?php echo $v_data['kode_menu']; ?>">
								<td><?php echo strtoupper($v_data['kode_menu']); ?></td>
								<td><?php echo strtoupper($v_data['nama']); ?></td>
								<td><?php echo empty($v_data['deskripsi']) ? '-' : strtoupper($v_data['deskripsi']); ?></td>
								<td><?php echo empty($v_data['jenis']) ? '-' : strtoupper($v_data['jenis']['nama']); ?></td>
								<td><?php echo empty($v_data['kategori']) ? '-' : strtoupper($v_data['kategori']['nama']); ?></td>
								<td><?php echo $v_data['branch']['nama']; ?></td>
								<td class="text-center"><?php echo ($v_data['additional'] == 0) ? 'NO' : 'YES'; ?></td>
								<td class="text-center status"><b><?php echo strtoupper($status); ?></b></td>
								<td>
									<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_edit'] == 1 ) { ?>
											<button class="btn btn-primary" onclick="menu.modalEditForm(this);"><i class="fa fa-edit"></i></button>
										<?php } ?>
									</div>
									<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_delete'] == 1 ) { ?>
											<button class="btn btn-danger" onclick="menu.delete(this);"><i class="fa fa-trash"></i></button>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr class="detail hide"></tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="6">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>