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
	<h3>Laporan Summary Penjualan Harian</h3>
</div>
<div style="width: 100%; font-size: 10pt;">
	<table>
		<tr>
			<td style="width: 5%;">Branch</td>
			<td style="width: 3%;">:</td>
			<td><?php echo $branch; ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Periode</td>
			<td style="width: 3%;">:</td>
			<td><?php echo tglIndonesia($start_date, '-', ' '); ?> s/d <?php echo tglIndonesia($end_date, '-', ' '); ?></td>
		</tr>
	</table>
</div>
<div style="width: 100%;">
	<table class="border-field" style="margin-bottom: 0px; min-width: 100%; border: 1px solid; border-collapse: collapse; font-size: 10pt;">
		<thead>
			<tr>
				<th align="center" style="min-width: 10%;">Date</th>
				<th align="center" style="min-width: 10%;">Bill No</th>
				<th align="center" style="min-width: 10%;">Food</th>
				<th align="center" style="min-width: 10%;">Baverage</th>
				<th align="center" style="min-width: 10%;">Tobacco</th>
				<th align="center" style="min-width: 10%;">Food Promo</th>
				<th align="center" style="min-width: 10%;">Other Income</th>
				<th align="center" style="min-width: 10%;">Discount</th>
				<th align="center" style="min-width: 10%;">Total</th>
				<th align="center" style="min-width: 10%;">Cash</th>
				<th align="center" style="min-width: 10%;">Credit</th>
				<th align="center" style="min-width: 10%;">Voucher</th>
				<th align="center" style="min-width: 10%;">OC</th>
				<th align="center" style="min-width: 10%;">Entertain</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( !empty($data) && count($data) > 0 ): ?>
				<?php
					$tot1 = 0;
					$tot2 = 0;
					$tot3 = 0;
					$tot4 = 0;
					$tot5 = 0;
					$tot6 = 0;
					$tot7 = 0;
					$tot8 = 0;
					$tot9 = 0;
					$tot10 = 0;
					$tot11 = 0;
					$tot12 = 0;
				?>
				<?php foreach ($data as $k_data => $v_data): ?>
					<tr>
						<td><?php echo isset($v_data['date']) ? tglIndonesia($v_data['date'], '-', ' ') : '-'; ?></td>
						<td><?php echo isset($v_data['kode_faktur']) ? $v_data['kode_faktur'] : '-'; ?></td>
						<td align="right"><?php echo isset($v_data['kategori_menu'][1]) ? angkaRibuan($v_data['kategori_menu'][1]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['kategori_menu'][2]) ? angkaRibuan($v_data['kategori_menu'][2]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['kategori_menu'][3]) ? angkaRibuan($v_data['kategori_menu'][3]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['diskon'][1]) ? angkaRibuan($v_data['diskon'][1]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['other_income']) ? angkaRibuan($v_data['other_income']) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['diskon'][2]) ? angkaRibuan($v_data['diskon'][2]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['total']) ? angkaRibuan($v_data['total']) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['kategori_pembayaran'][1]) ? angkaRibuan($v_data['kategori_pembayaran'][1]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['kategori_pembayaran'][2]) ? angkaRibuan($v_data['kategori_pembayaran'][2]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['kategori_pembayaran'][3]) ? angkaRibuan($v_data['kategori_pembayaran'][3]) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['diskon_requirement']['OC']) ? angkaRibuan($v_data['diskon_requirement']['OC']) : 0; ?></td>
						<td align="right"><?php echo isset($v_data['diskon_requirement']['ENTERTAIN']) ? angkaRibuan($v_data['diskon_requirement']['ENTERTAIN']) : 0; ?></td>
					</tr>
					<?php
						$tot1 += isset($v_data['kategori_menu'][1]) ? ($v_data['kategori_menu'][1]) : 0;
						$tot2 += isset($v_data['kategori_menu'][2]) ? ($v_data['kategori_menu'][2]) : 0;
						$tot3 += isset($v_data['kategori_menu'][3]) ? ($v_data['kategori_menu'][3]) : 0;
						$tot4 += isset($v_data['diskon'][1]) ? ($v_data['diskon'][1]) : 0;
						$tot5 += isset($v_data['other_income']) ? ($v_data['other_income']) : 0;
						$tot6 += isset($v_data['diskon'][2]) ? ($v_data['diskon'][2]) : 0;
						$tot7 += isset($v_data['total']) ? ($v_data['total']) : 0;
						$tot8 += isset($v_data['kategori_pembayaran'][1]) ? ($v_data['kategori_pembayaran'][1]) : 0;
						$tot9 += isset($v_data['kategori_pembayaran'][2]) ? ($v_data['kategori_pembayaran'][2]) : 0;
						$tot10 += isset($v_data['kategori_pembayaran'][3]) ? ($v_data['kategori_pembayaran'][3]) : 0;
						$tot11 += isset($v_data['diskon_requirement']['OC']) ? ($v_data['diskon_requirement']['OC']) : 0;
						$tot12 += isset($v_data['diskon_requirement']['ENTERTAIN']) ? ($v_data['diskon_requirement']['ENTERTAIN']) : 0;
					?>
				<?php endforeach ?>
				<tr>
					<td align="right" colspan="2"><b>Total</b></td>
					<td align="right"><b><?php echo angkaRibuan($tot1); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot2); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot3); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot4); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot5); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot6); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot7); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot8); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot9); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot10); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot11); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($tot12); ?></b></td>
				</tr>
			<?php else: ?>
				<tr>
					<td colspan="14">Data tidak ditemukan.</td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>
</div>