<div class="row content-panel detailed">
	<div class="col-xs-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
                <div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
                    <div class="col-xs-4 no-padding" style="padding-right: 5px;">
                        <div class="col-xs-12 no-padding"><label class="label-control">Tanggal</label></div>
                        <div class="col-xs-12 no-padding">
                            <div class="input-group date" id="Tanggal" name="tanggal">
                                <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-8 no-padding" style="padding-left: 5px;">
                        <div class="col-xs-12 no-padding"><label class="label-control">Branch</label></div>
                        <div class="col-xs-12 no-padding">
                            <select class="form-control branch" data-required="1" multiple="multiple">
                                <?php foreach ($branch as $key => $value) { ?>
                                    <option value="<?php echo $value['kode_branch']; ?>"><?php echo strtoupper($value['kode_branch'].' | '.$value['nama']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
				<div class="col-xs-12 no-padding">
					<button type="button" class="col-xs-12 btn btn-primary" onclick="co.getLists()"><i class="fa fa-search"></i> Tampilkan</button>
				</div>
                <div class="col-xs-12 no-padding"><hr style="margin: 10px 0px;"></div>
                <div class="col-xs-12 no-padding">
                    <small>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-xs-1">Kode</th>
                                    <th class="col-xs-2">Branch</th>
                                    <th class="col-xs-3">User</th>
                                    <th class="col-xs-2">Waktu</th>
                                    <th class="col-xs-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5">Data tidak ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </small>
                </div>
			</div>
		</form>
	</div>
</div>