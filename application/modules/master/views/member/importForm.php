<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12 no-padding">
			<div class="col-lg-8">
				<span style="font-weight: bold;">IMPORT MEMBER</span>
			</div>
			<div class="col-md-4 text-right">
				<button type="button" class="close pull-right" data-dismiss="modal" style="color: #000000;">&times;</button>
			</div>
			<div class="col-md-12 text-left">
				<hr style="margin-top: 5px; margin-bottom: 10px;">
			</div>
		</div>
		<div class="col-lg-12 no-padding">
			<div class="col-lg-12">
				<div class="col-lg-2 no-padding">Attach File</div>
				<div class="col-lg-8" style="padding-top: 2px;">
		            <a name="dokumen" class="text-right hide" target="_blank" style="padding-right: 10px;"><i class="fa fa-file"></i></a>
		            <label class="" style="margin-bottom: 0px;">
		                <input style="display: none;" placeholder="Dokumen" class="file_lampiran no-check" type="file" onchange="mbr.showNameFile(this)" data-name="name" data-allowtypes="xlsx" data-required="1">
		                <i class="glyphicon glyphicon-paperclip cursor-p" title="Attachment"></i> 
		            </label>
		        </div>
		        <div class="col-lg-2 no-padding">
		        	<button type="button" class="btn btn-primary pull-right" onclick="mbr.upload()"><i class="fa fa-upload"></i> Upload</button>
		        </div>
			</div>
			<div class="col-lg-12">
				<hr>
			</div>
			<div class="col-lg-12">
				<b>* Header pada file jangan di hapus dan usahakan semua file jadi text dan tidak ada function .</b><br>
				<b>* Angka dan huruf format sesuai di contoh file .</b><br>
			</div>
			<div class="col-lg-12">&nbsp;</div>
			<div class="col-lg-12">
				<button type="button" class="btn btn-success" onclick="mbr.download()"><i class="fa fa-download"></i> Download Contoh Excel</button>
			</div>
		</div>
		<!-- <div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12">
				<div class="col-md-12 no-padding">
					<button class="btn btn-primary col-md-12" onclick="mbr.save(this)"><i class="fa fa-save"> Simpan</i></button>
				</div>
			</div>
		</div> -->
	</div>
</div>