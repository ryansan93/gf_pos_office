var kode_branch = null;
var nama = null;
var alamat = null;
var telp = null;
var pin_branch = null;

var branch = {
	start_up: function () {
	}, // end - start_up

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/branch/modalAddForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});

                $(this).find('.kode').val(kode_branch);
				$(this).find('.nama').val(nama);
				$(this).find('.alamat').val(alamat);
				$(this).find('.no_telp').val(telp);
				$(this).find('.pin').val(pin_branch);
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		$('.modal').modal('hide');

		var tr = $(elm).closest('tr');

        $.get('parameter/branch/modalEditForm',{
            'kode': $(tr).find('td.kode').text()
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
            });
        },'html');
	}, // end - modalEditForm

	save: function() {
		var div = $('.modal');

		var err = 0;
		$.map( $(div).find('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			$('.modal').modal('hide');

			kode_branch = $(div).find('.kode').val().toUpperCase();
			nama = $(div).find('.nama').val().toUpperCase();
			alamat = $(div).find('.alamat').val().toUpperCase();
			telp = $(div).find('.no_telp').val().toUpperCase();
			pin_branch = $(div).find('.pin').val().toUpperCase();

			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var data = {
						'kode': kode_branch,
						'nama': nama,
						'alamat': alamat,
						'no_telp': telp,
						'pin': pin_branch
					};

			        $.ajax({
			            url: 'parameter/Branch/save',
			            data: {
			                'params': data
			            },
			            type: 'POST',
			            dataType: 'JSON',
			            beforeSend: function() { showLoading(); },
			            success: function(data) {
			                hideLoading();
			                if ( data.status == 1 ) {
			                	bootbox.alert(data.message, function() {
			                		location.reload();
			                	});
			                } else {
			                    bootbox.alert(data.message, function() {
			                    	branch.modalAddForm();
			                    });
			                }
			            }
			        });
				} else {
					branch.modalAddForm();
				}
			});
		}
    }, // end - save

    edit: function() {
		var div = $('.modal');

		var err = 0;
		$.map( $(div).find('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			$('.modal').modal('hide');

			kode_branch = $(div).find('.kode').val().toUpperCase();
			nama = $(div).find('.nama').val().toUpperCase();
			alamat = $(div).find('.alamat').val().toUpperCase();
			telp = $(div).find('.no_telp').val().toUpperCase();
			pin_branch = $(div).find('.pin').val().toUpperCase();

			bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
				if ( result ) {
					var data = {
						'kode': kode_branch,
						'nama': nama,
						'alamat': alamat,
						'no_telp': telp,
						'pin': pin_branch
					};

			        $.ajax({
			            url: 'parameter/Branch/edit',
			            data: {
			                'params': data
			            },
			            type: 'POST',
			            dataType: 'JSON',
			            beforeSend: function() { showLoading(); },
			            success: function(data) {
			                hideLoading();
			                if ( data.status == 1 ) {
			                	bootbox.alert(data.message, function() {
			                		location.reload();
			                	});
			                } else {
			                    bootbox.alert(data.message, function() {
			                    	branch.modalAddForm();
			                    });
			                }
			            }
			        });
				} else {
					branch.modalAddForm();
				}
			});
		}
    }, // end - edit

    delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				kode_branch = $(tr).find('td.kode').text();

		        $.ajax({
		            url: 'parameter/Branch/delete',
		            data: {
		                'kode_branch': kode_branch
		            },
		            type: 'POST',
		            dataType: 'JSON',
		            beforeSend: function() { showLoading(); },
		            success: function(data) {
		                hideLoading();
		                if ( data.status == 1 ) {
		                	bootbox.alert(data.message, function() {
		                		location.reload();
		                	});
		                } else {
		                    bootbox.alert(data.message);
		                }
		            }
		        });
			}
		});
    }, // end - delete
};

branch.start_up();