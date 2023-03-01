<div class="modal-header no-padding header">
    <span class="modal-title"><label class="label-control">Detail Transaksi</label></span>
    <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
    <div class="row" style="height: 100%;">
        <div class="col-xs-12" style="padding-top: 10px; height: 100%;">
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">No. Bill</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['kode_faktur']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Waktu</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo str_replace('-', '/', substr($data['tgl_trans'], 0, 16)); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Member</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['member']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Waitress</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['waitress']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Kasir</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['kasir']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
            <div class="col-xs-12 no-padding" style="height: 55%; margin-bottom: 5px;">
                <div class="col-xs-7 no-padding" style="height: 100%; padding-right: 5px; border-right: 1px solid #dedede;">
                    <div class="col-xs-12 no-padding" style="height: 60%; margin-bottom: 5px;">
                        <div class="col-xs-12" style="padding-left: 10px; padding-right: 10px; background-color: #afabff; border-top-left-radius: 5px; border-top-right-radius: 5px;">
                            <label class="label-control" style="margin-bottom: 0px;">LIST BARANG</label>
                        </div>
                        <div class="col-xs-12 list_barang" style="padding-left: 10px; padding-right: 10px; border: 1px solid #dedede; height: 88%; overflow-y: auto;">
                            <?php foreach ($data['detail'] as $k_det => $v_det): ?>
                                <div class="col-xs-12 no-padding">
                                    <div class="col-xs-8 no-padding" style="padding-right: 5px;">
                                        <span><?php echo $v_det['menu_nama'].' @ '.angkaDecimal($v_det['harga']); ?></span>
                                        <?php if ( !empty($v_det['request']) ): ?>
                                            <br>
                                            <span style="padding-left: 15px;"><?php echo '* '.$v_det['request']; ?></span>
                                        <?php endif ?>
                                    </div>
                                    <div class="col-xs-1 no-padding">
                                        <?php echo $v_det['jumlah']; ?>
                                    </div>
                                    <div class="col-xs-3 no-padding text-right">
                                        <?php echo angkaRibuan($v_det['total']); ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <div class="col-xs-12 no-padding" style="height: 40%; margin-bottom: 5px;">
                        <div class="col-xs-12" style="padding-left: 10px; padding-right: 10px; background-color: #afabff; border-top-left-radius: 5px; border-top-right-radius: 5px;">
                            <label class="label-control" style="margin-bottom: 0px;">JENIS BAYAR</label>
                        </div>
                        <div class="col-xs-12 jenis_bayar" style="padding-left: 10px; padding-right: 10px; border: 1px solid #dedede; height: 80%; overflow-y: auto;">
                            <?php if ( !empty($data['jenis_bayar']) ): ?>
                                <?php foreach ($data['jenis_bayar'] as $k_det => $v_det): ?>
                                    <div class="col-xs-12 no-padding">
                                        <div class="col-xs-8 no-padding" style="padding-right: 5px;">
                                            <?php echo $v_det['jenis_bayar']; ?>
                                        </div>
                                        <div class="col-xs-4 no-padding text-right">
                                            <?php echo angkaDecimal($v_det['nominal']); ?>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            <?php else: ?>
                                <div class="col-xs-12 no-padding">Belum ada pembayaran.</div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-5 no-padding" style="padding-left: 5px;">
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Total Belanja</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_belanja']); ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Diskon</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo '('.angkaDecimal($data['total_diskon']).')'; ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Service Charge</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_sc']) ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">PB1</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_ppn']) ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Total Bayar</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['grand_total']); ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Jumlah Bayar</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_bayar']); ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Kembalian</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['kembalian']); ?></label></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
            <?php if ( isset($data['bayar_id']) && !empty($data['bayar_id']) ): ?>
                <div class="col-xs-12 no-padding">
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding">
                        <button type="button" class="col-xs-12 btn btn-primary" onclick="bayar.rePrintNota(this)" data-faktur="<?php echo $data['kode_faktur']; ?>" data-id="<?php echo $data['bayar_id']; ?>"><i class="fa fa-print"></i> Re-Print Bill</button>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>