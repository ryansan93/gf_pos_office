<style type="text/css">
	table.border-field td, table.border-field th {
		border: 1px solid;
		border-collapse: collapse;
	}

	@media print {
	    html, body {
	        height: 99%;
	        width: 99.7%;
			max-width: 100%;
	    }

		.noPrint {
			display: none;
			padding-bottom: 0px;
		}

		div.contain {
			padding: 0px;
		}

		/* table.maintable tbody { page-break-inside:auto }
		table.maintable tbody tr.data { page-break-inside:avoid; page-break-after:auto } */
	}

	@media screen {
	    html, body {
	        height: 99%;
	        width: 99.5%;
			max-width: 100%;
	    }

		.noPrint {
			padding-bottom: 5px;
		}

		div.contain {
			padding: 10px;
		}
	}

	@page { 
		/* size: 8.5in 5.5in landscape; */
		size: a4 landscape;
		margin: 1em 2em 1em 2em; 
	}

	body {
		background-color: #666666;
	}

	div.contain {
		font-size: 9pt;
		background-color: #ffffff;
		/* padding: 10px; */
	}
</style>

<div class="noPrint">
	<button type="button" onclick="window.print()">PRINT</button>
</div>

<div class="contain">
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
			<tr>
				<td style="width: 5%;">Kasir</td>
				<td style="width: 3%;">:</td>
				<td><?php echo strtoupper($nama_kasir); ?></td>
			</tr>
		</table>
	</div>
	<div style="width: 99%;">
		<div style="width: 100%;">
			<table class="border-field" style="margin-bottom: 0px; min-width: 100%; border: 1px solid; border-collapse: collapse; font-size: 9pt;">
				<thead>
					<tr>
						<th align="center" style="min-width: 10%;">Date</th>
						<th align="center" style="min-width: 10%;">Bill No</th>
						<th align="center" style="min-width: 6.66%;">Food</th>
						<th align="center" style="min-width: 6.66%;">Baverage</th>
						<th align="center" style="min-width: 6.66%;">Tobacco</th>
						<th align="center" style="min-width: 6.66%;">Miscellaneous</th>
						<th align="center" style="min-width: 6.66%;">Other Income</th>
						<th align="center" style="min-width: 6.66%;">Food Promo</th>
						<th align="center" style="min-width: 6.66%;">Discount</th>
						<th align="center" style="min-width: 6.66%;">Total</th>
						<th align="center" style="min-width: 6.66%;">Cash</th>
						<th align="center" style="min-width: 6.66%;">Credit</th>
						<th align="center" style="min-width: 6.66%;">Voucher</th>
						<th align="center" style="min-width: 6.66%;">CL</th>
						<!-- <th align="center" style="min-width: 10%;">OC</th>
						<th align="center" style="min-width: 10%;">Entertain</th> -->
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data) && count($data) > 0 ): ?>
						<?php
							$tot1 = 0;
							$tot2 = 0;
							$tot3 = 0;
							$tot5 = 0;
							$tot6 = 0;
							$tot4 = 0;
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
							<?php if ( (!isset($v_data['diskon_requirement']['OC']) || $v_data['diskon_requirement']['OC'] == 0) && (!isset($v_data['diskon_requirement']['ENTERTAIN']) || $v_data['diskon_requirement']['ENTERTAIN'] == 0) && isset($v_data['status_gabungan'])  ) : ?>

								<tr>
									<td><?php echo isset($v_data['date']) ? tglIndonesia($v_data['date'], '-', ' ') : '-'; ?></td>
									<td><?php echo isset($v_data['kode_faktur']) ? $v_data['kode_faktur'] : '-'; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_menu'][1]) ? angkaDecimal($v_data['kategori_menu'][1]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_menu'][2]) ? angkaDecimal($v_data['kategori_menu'][2]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_menu'][3]) ? angkaDecimal($v_data['kategori_menu'][3]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_menu'][4]) ? angkaDecimal($v_data['kategori_menu'][4]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_menu'][5]) ? angkaDecimal($v_data['kategori_menu'][5]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['diskon_requirement']['FOOD_PROMO']) ? angkaDecimal($v_data['diskon_requirement']['FOOD_PROMO']) : 0; ?></td>
									<!-- <td align="right"><?php echo isset($v_data['diskon'][2]) ? angkaDecimal($v_data['diskon'][2]) : 0; ?></td> -->
									<td align="right"><?php echo ((!isset($v_data['diskon_requirement']['FOOD_PROMO']) || (isset($v_data['diskon_requirement']['FOOD_PROMO']) && $v_data['diskon_requirement']['FOOD_PROMO'] == 0)) && isset($v_data['diskon'][1])) ? angkaDecimal($v_data['diskon'][1]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['total']) ? angkaDecimal($v_data['total']) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_pembayaran'][1]) ? angkaDecimal($v_data['kategori_pembayaran'][1]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_pembayaran'][2]) ? angkaDecimal($v_data['kategori_pembayaran'][2]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_pembayaran'][3]) ? angkaDecimal($v_data['kategori_pembayaran'][3]) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['kategori_pembayaran'][4]) ? angkaDecimal($v_data['kategori_pembayaran'][4]) : 0; ?></td>
									<!-- <td align="right"><?php echo isset($v_data['diskon_requirement']['OC']) ? angkaDecimal($v_data['diskon_requirement']['OC']) : 0; ?></td>
									<td align="right"><?php echo isset($v_data['diskon_requirement']['ENTERTAIN']) ? angkaDecimal($v_data['diskon_requirement']['ENTERTAIN']) : 0; ?></td> -->
								</tr>
								<?php
									$tot1 += isset($v_data['kategori_menu'][1]) ? ($v_data['kategori_menu'][1]) : 0;
									$tot2 += isset($v_data['kategori_menu'][2]) ? ($v_data['kategori_menu'][2]) : 0;
									$tot3 += isset($v_data['kategori_menu'][3]) ? ($v_data['kategori_menu'][3]) : 0;
									$tot5 += isset($v_data['kategori_menu'][5]) ? ($v_data['kategori_menu'][5]) : 0;
									$tot6 += isset($v_data['diskon_requirement']['FOOD_PROMO']) ? angkaDecimal($v_data['diskon_requirement']['FOOD_PROMO']) : 0;
									// $tot6 += isset($v_data['diskon'][2]) ? ($v_data['diskon'][2]) : 0;
									$tot4 += ((!isset($v_data['diskon_requirement']['FOOD_PROMO']) || (isset($v_data['diskon_requirement']['FOOD_PROMO']) && $v_data['diskon_requirement']['FOOD_PROMO'] == 0)) && isset($v_data['diskon'][1])) ? ($v_data['diskon'][1]) : 0;
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
							<td align="right" colspan="2"><b>Total</b></td>
							<td align="right"><b><?php echo angkaDecimal($tot1); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot2); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot3); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot14); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot5); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot6); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot4); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot7); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot8); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot9); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot10); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot13); ?></b></td>
							<!-- <td align="right"><b><?php echo angkaDecimal($tot11); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot12); ?></b></td> -->
						</tr>
					<?php else: ?>
						<tr>
							<td colspan="14">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
		<div style="width: 99%;"><br></div>
		<div style="width: 100%;">
			<table class="border-field" style="margin-bottom: 0px; min-width: 100%; border: 1px solid; border-collapse: collapse; font-size: 9pt;">
				<thead>
					<tr>
						<th style="min-width: 10%; width: 10%;">Date</th>
						<th style="min-width: 10%; width: 10%;">Bill No</th>
						<th style="min-width: 10%; width: 10%;">Food</th>
						<th style="min-width: 10%; width: 10%;">Baverage</th>
						<th style="min-width: 10%; width: 10%;">Tobacco</th>
						<th style="min-width: 10%; width: 10%;">Miscellaneous</th>
						<th style="min-width: 10%; width: 10%;">Other Income</th>
						<th style="min-width: 10%; width: 10%;">Total</th>
						<th style="min-width: 10%; width: 10%;">OC</th>
						<th style="min-width: 10%; width: 10%;">Compliment</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data_oc_compliment) && count($data_oc_compliment) > 0 ): ?>
						<?php
							$tot1 = 0;
							$tot2 = 0;
							$tot3 = 0;
							$tot4 = 0;
							$tot5 = 0;
							$tot7 = 0;
							$tot11 = 0;
							$tot12 = 0;
						?>
						<?php foreach ($data_oc_compliment as $k_data => $v_data): ?>
							<?php
								$bg_color = 'transparent';
								if ( $v_data['status_gabungan'] == 1 ) {
									$bg_color = '#ffb3b3';
								}
							?>
							<tr class="cursor-p" style="background-color: <?php echo $bg_color; ?>;">
								<td><?php echo isset($v_data['date']) ? tglIndonesia($v_data['date'], '-', ' ') : '-'; ?></td>
								<td><?php echo isset($v_data['kode_faktur']) ? $v_data['kode_faktur'] : '-'; ?></td>
								<td align="right"><?php echo isset($v_data['kategori_menu'][1]) ? angkaDecimal($v_data['kategori_menu'][1]) : 0; ?></td>
								<td align="right"><?php echo isset($v_data['kategori_menu'][2]) ? angkaDecimal($v_data['kategori_menu'][2]) : 0; ?></td>
								<td align="right"><?php echo isset($v_data['kategori_menu'][3]) ? angkaDecimal($v_data['kategori_menu'][3]) : 0; ?></td>
								<td align="right"><?php echo isset($v_data['kategori_menu'][4]) ? angkaDecimal($v_data['kategori_menu'][4]) : 0; ?></td>
								<td align="right"><?php echo isset($v_data['kategori_menu'][5]) ? angkaDecimal($v_data['kategori_menu'][5]) : 0; ?></td>
								<td align="right"><?php echo isset($v_data['total']) ? angkaDecimal($v_data['total']) : 0; ?></td>
								<td align="right"><?php echo isset($v_data['diskon_requirement']['OC']) ? angkaDecimal($v_data['diskon_requirement']['OC']) : 0; ?></td>
								<td align="right"><?php echo isset($v_data['diskon_requirement']['ENTERTAIN']) ? angkaDecimal($v_data['diskon_requirement']['ENTERTAIN']) : 0; ?></td>
							</tr>
							<?php
								$tot1 += isset($v_data['kategori_menu'][1]) ? ($v_data['kategori_menu'][1]) : 0;
								$tot2 += isset($v_data['kategori_menu'][2]) ? ($v_data['kategori_menu'][2]) : 0;
								$tot3 += isset($v_data['kategori_menu'][3]) ? ($v_data['kategori_menu'][3]) : 0;
								$tot4 += isset($v_data['kategori_menu'][4]) ? ($v_data['kategori_menu'][4]) : 0;
								$tot5 += isset($v_data['kategori_menu'][5]) ? ($v_data['kategori_menu'][5]) : 0;
								$tot7 += isset($v_data['total']) ? ($v_data['total']) : 0;
								$tot11 += isset($v_data['diskon_requirement']['OC']) ? ($v_data['diskon_requirement']['OC']) : 0;
								$tot12 += isset($v_data['diskon_requirement']['ENTERTAIN']) ? ($v_data['diskon_requirement']['ENTERTAIN']) : 0;
							?>
						<?php endforeach ?>
						<tr>
							<td align="right" colspan="2"><b>Total</b></td>
							<td align="right"><b><?php echo angkaDecimal($tot1); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot2); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot3); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot4); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot5); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot7); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot11); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($tot12); ?></b></td>
						</tr>
					<?php else: ?>
						<tr>
							<td colspan="10">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
	</div>
</div>