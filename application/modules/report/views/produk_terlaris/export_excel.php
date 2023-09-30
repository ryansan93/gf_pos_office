<style type="text/css">
	.str { mso-number-format:\@; }
	.decimal_number_format { mso-number-format: "\#\,\#\#0.00"; }
	.number_format { mso-number-format: "\#\,\#\#0"; }
</style>
<div style="width: 100%;">
	<h3>Laporan Performance Produk dan Member</h3>
</div>
<br>
<?php if ( $filter == 0 ): ?>
    <table border="1">
        <thead>
            <tr>
                <th class="col-xs-1">No.</th>
                <th class="col-xs-2">Kategori</th>
                <th class="col-xs-2">Jenis</th>
                <th class="col-xs-4">Menu</th>
                <th class="col-xs-1">Qty</th>
                <th class="col-xs-2">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( !empty($data) && count($data) > 0 ): ?>
                <?php $no = 1; ?>
                <?php foreach ($data as $k_data => $v_data): ?>
                    <tr class="search">
                        <td class="number_format"><?php echo ($no); ?></td>
                        <td class="str"><?php echo !empty($v_data['kategori']) ? $v_data['kategori'] : '-'; ?></td>
                        <td class="str"><?php echo $v_data['jenis']; ?></td>
                        <td class="str"><?php echo $v_data['menu_nama']; ?></td>
                        <td class="number_format"><?php echo ($v_data['qty']); ?></td>
                        <td class="decimal_number_format"><?php echo ($v_data['total']); ?></td>
                    </tr>
                    <?php $no++; ?>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Data tidak ditemukan.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
<?php else: ?>
    <table border="1">
        <thead>
            <tr>
                <th class="col-xs-1">No.</th>
                <th class="col-xs-2">Nama Member</th>
                <th class="col-xs-1">Jml Transaksi</th>
                <th class="col-xs-1">Kategori</th>
                <th class="col-xs-2">Jenis</th>
                <th class="col-xs-3">Menu</th>
                <th class="col-xs-1">Qty</th>
                <th class="col-xs-1">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( !empty($data) && count($data) > 0 ): ?>
                <?php $no = 1; ?>
                <?php foreach ($data as $k_member => $v_member): ?>
                    <?php $idx_member = 0; $t_jumlah = 0; $t_total = 0;?>
                    <?php foreach ($v_member['detail_menu'] as $k_data => $v_data): ?>
                        <tr class="search">
                            <?php if ( $idx_member == 0 ): ?>
                                <td class="number_format" rowspan="<?php echo count($v_member['detail_menu'])+1; ?>"><?php echo ($no); ?></td>
                                <td class="str" rowspan="<?php echo count($v_member['detail_menu']); ?>"><?php echo $v_member['nama']; ?></td>
                                <td class="str" rowspan="<?php echo count($v_member['detail_menu']); ?>" class="text-right"><?php echo $v_member['jml_transaksi']; ?></td>
                            <?php endif ?>
                            <td class="str"><?php echo !empty($v_data['kategori']) ? $v_data['kategori'] : '-'; ?></td>
                            <td class="str"><?php echo $v_data['jenis']; ?></td>
                            <td class="str"><?php echo $v_data['menu_nama']; ?></td>
                            <td class="number_format"><?php echo ($v_data['qty']); ?></td>
                            <td class="decimal_number_format"><?php echo ($v_data['total']); ?></td>
                        </tr>
                        <?php
                            $t_jumlah += $v_data['qty']; 
                            $t_total += $v_data['total'];
                            $idx_member++;
                        ?>
                    <?php endforeach ?>
                    <tr>
                        <td colspan="5" class="str"><b>TOTAL</b></td>
                        <td class="number_format"><b><?php echo ($t_jumlah); ?></b></td>
                        <td class="decimal_number_format"><b><?php echo ($t_total); ?></b></td>
                    </tr>
                    <?php $no++; ?>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Data tidak ditemukan.</td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
<?php endif ?>