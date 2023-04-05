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
		<?php if ( (!isset($v_data['diskon_requirement']['OC']) || $v_data['diskon_requirement']['OC'] == 0) && (!isset($v_data['diskon_requirement']['ENTERTAIN']) || $v_data['diskon_requirement']['ENTERTAIN'] == 0) && isset($v_data['status_gabungan']) ) : ?>
			<?php
				$bg_color = 'transparent';
				if ( isset($v_data['status_gabungan']) && $v_data['status_gabungan'] == 1 ) {
					$bg_color = '#ffb3b3';
				}
			?>
			<tr class="cursor-p" style="background-color: <?php echo $bg_color; ?>;">
				<td><?php echo isset($v_data['date']) ? tglIndonesia($v_data['date'], '-', ' ') : '-'; ?></td>
				<td><?php echo isset($v_data['kode_faktur']) ? $v_data['kode_faktur'] : '-'; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_menu'][1]) ? angkaDecimal($v_data['kategori_menu'][1]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_menu'][2]) ? angkaDecimal($v_data['kategori_menu'][2]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_menu'][3]) ? angkaDecimal($v_data['kategori_menu'][3]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_menu'][4]) ? angkaDecimal($v_data['kategori_menu'][4]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_menu'][5]) ? angkaDecimal($v_data['kategori_menu'][5]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['diskon_requirement']['FOOD_PROMO']) ? angkaDecimal($v_data['diskon_requirement']['FOOD_PROMO']) : 0; ?></td>
				<!-- <td class="text-right"><?php echo isset($v_data['diskon'][2]) ? angkaDecimal($v_data['diskon'][2]) : 0; ?></td> -->
				<td class="text-right"><?php echo ((!isset($v_data['diskon_requirement']['FOOD_PROMO']) || (isset($v_data['diskon_requirement']['FOOD_PROMO']) && $v_data['diskon_requirement']['FOOD_PROMO'] == 0)) && isset($v_data['diskon'][1])) ? angkaDecimal($v_data['diskon'][1]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['total']) ? angkaDecimal($v_data['total']) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][1]) ? angkaDecimal($v_data['kategori_pembayaran'][1]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][2]) ? angkaDecimal($v_data['kategori_pembayaran'][2]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][3]) ? angkaDecimal($v_data['kategori_pembayaran'][3]) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][4]) ? angkaDecimal($v_data['kategori_pembayaran'][4]) : 0; ?></td>
				<!-- <td class="text-right"><?php echo isset($v_data['diskon_requirement']['OC']) ? angkaDecimal($v_data['diskon_requirement']['OC']) : 0; ?></td>
				<td class="text-right"><?php echo isset($v_data['diskon_requirement']['ENTERTAIN']) ? angkaDecimal($v_data['diskon_requirement']['ENTERTAIN']) : 0; ?></td> -->
			</tr>
			<?php
				$tot1 += isset($v_data['kategori_menu'][1]) ? ($v_data['kategori_menu'][1]) : 0;
				$tot2 += isset($v_data['kategori_menu'][2]) ? ($v_data['kategori_menu'][2]) : 0;
				$tot3 += isset($v_data['kategori_menu'][3]) ? ($v_data['kategori_menu'][3]) : 0;
				$tot5 += isset($v_data['kategori_menu'][5]) ? ($v_data['kategori_menu'][5]) : 0;
				$tot4 += isset($v_data['diskon_requirement']['FOOD_PROMO']) ? ($v_data['diskon_requirement']['FOOD_PROMO']) : 0;
				// $tot4 += isset($v_data['diskon'][2]) ? ($v_data['diskon'][2]) : 0;
				$tot6 += ((!isset($v_data['diskon_requirement']['FOOD_PROMO']) || (isset($v_data['diskon_requirement']['FOOD_PROMO']) && $v_data['diskon_requirement']['FOOD_PROMO'] == 0)) && isset($v_data['diskon'][1])) ? ($v_data['diskon'][1]) : 0;
				$tot7 += isset($v_data['total']) ? ($v_data['total']) : 0;
				$tot8 += isset($v_data['kategori_pembayaran'][1]) ? ($v_data['kategori_pembayaran'][1]) : 0;
				$tot9 += isset($v_data['kategori_pembayaran'][2]) ? ($v_data['kategori_pembayaran'][2]) : 0;
				$tot10 += isset($v_data['kategori_pembayaran'][3]) ? ($v_data['kategori_pembayaran'][3]) : 0;
				// $tot11 += isset($v_data['diskon_requirement']['OC']) ? ($v_data['diskon_requirement']['OC']) : 0;
				// $tot12 += isset($v_data['diskon_requirement']['ENTERTAIN']) ? ($v_data['diskon_requirement']['ENTERTAIN']) : 0;
				$tot13 += isset($v_data['kategori_pembayaran'][4]) ? ($v_data['kategori_pembayaran'][4]) : 0;
				$tot14 += isset($v_data['kategori_menu'][4]) ? ($v_data['kategori_menu'][4]) : 0;
			?>
		<?php endif ?>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="2"><b>Total</b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot1); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot2); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot3); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot14); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot5); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot4); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot6); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot7); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot8); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot9); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot10); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot13); ?></b></td>
		<!-- <td class="text-right"><b><?php echo angkaDecimal($tot11); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($tot12); ?></b></td> -->
	</tr>
<?php else: ?>
	<tr>
		<td colspan="14">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>