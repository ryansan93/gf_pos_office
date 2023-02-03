<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<?php if ( $akses['a_submit'] == 1 ) { ?>
			<div class="col-lg-12 no-padding">
				<button id="btn-add" type="button" data-href="action" class="col-lg-12 btn btn-primary cursor-p pull-right" title="ADD" onclick="mg.addForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			</div>
		<?php } ?>
		<div class="col-lg-12 no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-lg-12 search left-inner-addon no-padding" style="margin-bottom: 10px;">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_riwayat" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<small>
			<table class="table table-bordered tbl_riwayat" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-12">Nama</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $key => $value): ?>
							<tr class="search cursor-p" onclick="mg.viewForm(this)" data-kode="<?php echo $value['id']; ?>">
								<td><?php echo $value['nama']; ?></td>
							</tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="1">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>