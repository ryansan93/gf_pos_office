<?php if ( !empty($data) && count($data) > 0 ) { ?>
    <?php foreach ($data as $key => $value) { ?>
        <tr>
            <td><?php echo $value['kode']; ?></td>
            <td><?php echo $value['nama_branch']; ?></td>
            <td><?php echo $value['nama_user']; ?></td>
            <td><?php echo $value['tanggal']; ?></td>
            <td>
                <button type="button" class="col-xs-12 btn btn-danger" onclick="co.delete(this)" data-id="<?php echo $value['kode']; ?>"><i class="fa fa-trash"></i> Hapus</button>
            </td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="5">Data tidak ditemukan.</td>
    </tr>
<?php } ?>