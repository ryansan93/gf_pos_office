<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-lg-8 search left-inner-addon no-padding">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_ppn" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<div class="col-lg-4 action no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="ADD" onclick="tp.modalAddForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			<?php } else { ?>
				<div class="col-lg-2 action no-padding pull-right">
					&nbsp
				</div>
			<?php } ?>
		</div>
		<small>
			<table class="table table-bordered table-hover tbl_ppn" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-3 text-center">Tgl Berlaku</th>
						<th class="col-sm-3 text-center">Nilai (%)</th>
						<th class="col-sm-2 text-center"></th>
					</tr>
				</thead>
				<tbody class="list">
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr class="search">
								<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_berlaku'], '-', ' ')); ?></td>
								<td class="text-right"><?php echo angkaDecimal($v_data['nilai']); ?></td>
								<td class="text-right">
									<button class="col-sm-12 btn btn-danger" data-id="<?php echo $v_data['id']; ?>" onclick="tp.delete(this)"><i class="fa fa-times"></i></button>
								</td>
							</tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="3">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>