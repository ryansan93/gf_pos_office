<style type="text/css">
	table.border-field td, table.border-field th {
		border: 1px solid;
		border-collapse: collapse;
	}

	@page{
		margin: 0.5em 1em;
	}
</style>

<div style="width: 100%;">
	<h3>Laporan Menu Gagal Per Tanggal</h3>
</div>
<div style="width: 100%; font-size: 10pt;">
	<table>
		<tr>
			<td style="width: 5%;">Branch</td>
			<td style="width: 3%;">:</td>
			<td><?php echo $branch; ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Tanggal</td>
			<td style="width: 3%;">:</td>
			<td><?php echo tglIndonesia($tanggal, '-', ' '); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">User Export</td>
			<td style="width: 3%;">:</td>
			<td><?php echo strtoupper($nama_user); ?></td>
		</tr>
	</table>
</div>
<div style="width: 100%;">
	<div style="width: 100%;">
		<table class="border-field" style="margin-bottom: 0px; min-width: 100%; border: 1px solid; border-collapse: collapse; font-size: 10pt;">
			<thead>
				<tr>
					<th align="center" style="min-width: 10%;">Kode Pesanan</th>
					<th align="center" style="min-width: 10%;">Menu</th>
					<th align="center" style="min-width: 10%;">Jumlah</th>
					<th align="center" style="min-width: 10%;">User</th>
					<th align="center" style="min-width: 10%;">Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( !empty($data) && count($data) > 0 ): ?>
					<?php foreach ($data as $k_data => $v_data): ?>
						<tr>
							<td><?php echo $v_data['kode_pesanan']; ?></td>
							<td><?php echo $v_data['nama_menu']; ?></td>
							<td align="right"><?php echo angkaRibuan($v_data['jumlah']); ?></td>
							<td><?php echo $v_data['nama_user']; ?></td>
							<td><?php echo !empty($v_data['keterangan']) ? $v_data['keterangan'] : '-'; ?></td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
					<tr>
						<td colspan="5">Data tidak ditemukan.</td>
					</tr>
				<?php endif ?>
			</tbody>
		</table>
	</div>
</div>