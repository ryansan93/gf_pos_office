<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php
		$grand_total = 0;
		$grand_total_pajak = 0;
	?>
	<?php foreach ($data as $key => $value): ?>
		<tr>
			<td class="text-left"><?php echo $value['kode_faktur']; ?></td>
			<td class="text-center"><?php echo tglIndonesia($value['tgl_trans'], '-', ' '); ?></td>
			<td class="text-right total"><?php echo angkaRibuan($value['grand_total']); ?></td>
			<td class="text-right pajak"><?php echo angkaRibuan($value['ppn']); ?></td>
		</tr>

		<?php
			$grand_total += $value['grand_total'];
			$grand_total_pajak += $value['ppn'];
		?>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="2"><b>TOTAL</b></td>
		<td class="text-right grand_total"><b><?php echo angkaRibuan($grand_total); ?></b></td>
		<td class="text-right grand_total_pajak"><b><?php echo angkaRibuan($grand_total_pajak); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="4">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>