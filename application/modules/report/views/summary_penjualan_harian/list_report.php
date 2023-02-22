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
		// $tot11 = 0;
		// $tot12 = 0;
		$tot13 = 0;
		$tot14 = 0;
	?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<?php
			$bg_color = 'transparent';
			if ( $v_data['status_gabungan'] == 1 ) {
				$bg_color = '#ffb3b3';
			}
		?>
		<tr class="cursor-p" style="background-color: <?php echo $bg_color; ?>;">
			<td><?php echo isset($v_data['date']) ? tglIndonesia($v_data['date'], '-', ' ') : '-'; ?></td>
			<td><?php echo isset($v_data['kode_faktur']) ? $v_data['kode_faktur'] : '-'; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_menu'][1]) ? angkaRibuan($v_data['kategori_menu'][1]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_menu'][2]) ? angkaRibuan($v_data['kategori_menu'][2]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_menu'][3]) ? angkaRibuan($v_data['kategori_menu'][3]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_menu'][4]) ? angkaRibuan($v_data['kategori_menu'][4]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['other_income']) ? angkaRibuan($v_data['other_income']) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['diskon'][2]) ? angkaRibuan($v_data['diskon'][2]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['diskon'][1]) ? angkaRibuan($v_data['diskon'][1]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['total']) ? angkaRibuan($v_data['total']) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][1]) ? angkaRibuan($v_data['kategori_pembayaran'][1]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][2]) ? angkaRibuan($v_data['kategori_pembayaran'][2]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][3]) ? angkaRibuan($v_data['kategori_pembayaran'][3]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][4]) ? angkaRibuan($v_data['kategori_pembayaran'][4]) : 0; ?></td>
			<!-- <td class="text-right"><?php echo isset($v_data['diskon_requirement']['OC']) ? angkaRibuan($v_data['diskon_requirement']['OC']) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['diskon_requirement']['ENTERTAIN']) ? angkaRibuan($v_data['diskon_requirement']['ENTERTAIN']) : 0; ?></td> -->
		</tr>
		<?php
			$tot1 += isset($v_data['kategori_menu'][1]) ? ($v_data['kategori_menu'][1]) : 0;
			$tot2 += isset($v_data['kategori_menu'][2]) ? ($v_data['kategori_menu'][2]) : 0;
			$tot3 += isset($v_data['kategori_menu'][3]) ? ($v_data['kategori_menu'][3]) : 0;
			$tot5 += isset($v_data['other_income']) ? ($v_data['other_income']) : 0;
			$tot4 += isset($v_data['diskon'][2]) ? ($v_data['diskon'][2]) : 0;
			$tot6 += isset($v_data['diskon'][1]) ? ($v_data['diskon'][1]) : 0;
			$tot7 += isset($v_data['total']) ? ($v_data['total']) : 0;
			$tot8 += isset($v_data['kategori_pembayaran'][1]) ? ($v_data['kategori_pembayaran'][1]) : 0;
			$tot9 += isset($v_data['kategori_pembayaran'][2]) ? ($v_data['kategori_pembayaran'][2]) : 0;
			$tot10 += isset($v_data['kategori_pembayaran'][3]) ? ($v_data['kategori_pembayaran'][3]) : 0;
			// $tot11 += isset($v_data['diskon_requirement']['OC']) ? ($v_data['diskon_requirement']['OC']) : 0;
			// $tot12 += isset($v_data['diskon_requirement']['ENTERTAIN']) ? ($v_data['diskon_requirement']['ENTERTAIN']) : 0;
			$tot13 += isset($v_data['kategori_pembayaran'][4]) ? ($v_data['kategori_pembayaran'][4]) : 0;
			$tot14 += isset($v_data['kategori_menu'][4]) ? ($v_data['kategori_menu'][4]) : 0;
		?>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="2"><b>Total</b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot1); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot2); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot3); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot14); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot5); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot4); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot6); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot7); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot8); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot9); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot10); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot13); ?></b></td>
		<!-- <td class="text-right"><b><?php echo angkaRibuan($tot11); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($tot12); ?></b></td> -->
	</tr>
<?php else: ?>
	<tr>
		<td colspan="14">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>