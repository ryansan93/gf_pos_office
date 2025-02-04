<div class="modal-header">
	<span class="modal-title"><b>IMPORT DATA</b></span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body" style="padding-bottom: 0px;">
	<div class="row detailed">
		<div class="col-xs-12 detailed no-padding">
			<form role="form" class="form-horizontal">
                <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
                    <div class="col-xs-2 no-padding">Lampirkan File Excel</div>
                    <div class="col-xs-1 no-padding text-center">:</div>
                    <div class="col-xs-9 no-padding" style="padding-top: 2px;">
                        <a name="dokumen" class="text-right hide" target="_blank" style="padding-right: 10px;"><i class="fa fa-file"></i></a>
                        <label class="" style="margin-bottom: 0px;">
                            <input style="display: none;" placeholder="Dokumen" class="file_lampiran no-check" type="file" onchange="menu.showNameFile(this)" data-name="name" data-allowtypes="xlsx" data-required="1">
                            <i class="glyphicon glyphicon-paperclip cursor-p" title="Attachment"></i> 
                        </label>
                    </div>
                    <!-- <div class="col-xs-2 no-padding">
                        <button type="button" class="btn btn-primary pull-right" onclick="kp.upload()"><i class="fa fa-upload"></i> Upload</button>
                    </div> -->
                </div>
				<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-xs-12 no-padding text-right">  
                    <button type="button" class="btn btn-default" onclick="window.open('parameter/Menu/downloadTemplate')"><i class="fa fa-download"></i> Download Template</button>
                    |
                    <button type="button" class="btn btn-primary" onclick="menu.import()"><i class="fa fa-save"></i> Import</button>
				</div>
			</form>
		</div>
	</div>
</div>