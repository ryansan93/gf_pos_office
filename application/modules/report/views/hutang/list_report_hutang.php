<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $total_hutang = 0; $total_bayar = 0; ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<?php if ( !empty($v_data['jenis_bayar']) ): ?>
			<?php $idx_jb = 0; ?>
			<?php $rowspan = count($v_data['jenis_bayar']); ?>
			<?php foreach ($v_data['jenis_bayar'] as $k_jb => $v_jb): ?>
				<tr class="search">
					<?php if ( $idx_jb == 0 ): ?>
						<td rowspan="<?php echo $rowspan; ?>"><?php echo tglIndonesia($v_data['tgl_pesan'], '-', ' '); ?></td>
						<td rowspan="<?php echo $rowspan; ?>"><?php echo $v_data['nama_kasir']; ?></td>
						<td rowspan="<?php echo $rowspan; ?>"><?php echo $v_data['faktur_kode']; ?></td>
						<td rowspan="<?php echo $rowspan; ?>"><?php echo !empty($v_data['member_group']) ? $v_data['member_group'] : '-'; ?></td>
						<td rowspan="<?php echo $rowspan; ?>"><?php echo $v_data['member']; ?></td>
						<td class="text-right" rowspan="<?php echo $rowspan; ?>"><?php echo angkaRibuan($v_data['hutang']); ?></td>
						<td class="text-right" rowspan="<?php echo $rowspan; ?>"><?php echo angkaRibuan($v_data['bayar']); ?></td>
						<td rowspan="<?php echo $rowspan; ?>"><?php echo !empty($v_data['remark']) ? $v_data['remark'] : '-'; ?></td>
					<?php endif ?>
					<td><?php echo !empty($v_jb['jenis_bayar']) ? $v_jb['jenis_bayar'] : '-'; ?></td>
					<td><?php echo !empty($v_jb['tgl_bayar']) ? tglIndonesia($v_jb['tgl_bayar'], '-', ' ') : '-'; ?></td>
					<td class="text-right"><?php echo !empty($v_jb['nominal']) ? angkaRibuan($v_jb['nominal']) : '-'; ?></td>
				</tr>
				<?php $idx_jb++; ?>
			<?php endforeach ?>
		<?php else: ?>
			<tr class="search">
				<td><?php echo tglIndonesia($v_data['tgl_pesan'], '-', ' '); ?></td>
				<td><?php echo $v_data['nama_kasir']; ?></td>
				<td><?php echo $v_data['faktur_kode']; ?></td>
				<td><?php echo !empty($v_data['member_group']) ? $v_data['member_group'] : '-'; ?></td>
				<td><?php echo $v_data['member']; ?></td>
				<td class="text-right"><?php echo angkaRibuan($v_data['hutang']); ?></td>
				<td class="text-right"><?php echo angkaRibuan($v_data['bayar']); ?></td>
				<td><?php echo !empty($v_data['remark']) ? $v_data['remark'] : '-'; ?></td>
				<td><?php echo '-'; ?></td>
				<td><?php echo '-'; ?></td>
				<td class="text-right"><?php echo 0; ?></td>
			</tr>
		<?php endif ?>
		<?php 
			$total_hutang += $v_data['hutang']; 
			$total_bayar += $v_data['bayar']; 
		?>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="5"><b>TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaRibuan($total_hutang); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($total_bayar); ?></b></td>
		<td class="text-right"></td>
		<td class="text-right"></td>
		<td class="text-right"></td>
		<td class="text-right"><b><?php echo angkaRibuan($total_bayar); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="8">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>