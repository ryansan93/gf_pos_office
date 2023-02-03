var item = {
	start_up: function () {
	}, // end - start_up

	addRow: function (elm) {
		var tr = $(elm).closest('tr');
		var tbody = $(tr).closest('tbody');

		var tr_clone = $(tr).clone();
		$(tr_clone).find('input').val('');

		$(tr_clone).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
			// $(this).priceFormat(Config[$(this).data('tipe')]);
			priceFormat( $(this).val() );
		});

		$(tbody).append( $(tr_clone) );
	}, // end - addRow

	removeRow: function (elm) {
		var tr = $(elm).closest('tr');
		var tbody = $(tr).closest('tbody');

		if ( $(tbody).find('tr').length > 1 ) {
			$(tr).remove();
		}
	}, // end - removeRow

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/Item/modalAddForm',{
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

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this).val() );
				});

		        $(this).find('textarea').addClass('uppercase');
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

		$('.modal').modal('hide');

        $.get('parameter/Item/modalEditForm',{
        	'kode': $(tr).data('kode')
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

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this).val() );
				});
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
			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var satuan = $.map( $(div).find('.tbl_satuan tbody tr'), function (tr) {
						var _satuan = {
							'satuan': $(tr).find('input.satuan').val().toUpperCase(),
							'pengali': numeral.unformat($(tr).find('input.pengali').val())
						};

						return _satuan;
					});

					var data = {
						'kode': $(div).find('.kode').val().toUpperCase(),
						'nama': $(div).find('.nama').val().toUpperCase(),
						'brand': $(div).find('.brand').val().toUpperCase(),
						'group': $(div).find('.group').val(),
						'keterangan': $(div).find('.keterangan').val(),
						'satuan': satuan
					};

			        $.ajax({
			            url: 'parameter/Item/save',
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
			                    bootbox.alert(data.message);
			                }
			            }
			        });
				}
			});
		}
	}, // end - save

	edit: function(elm) {
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
			bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
				if ( result ) {
					var satuan = $.map( $(div).find('.tbl_satuan tbody tr'), function (tr) {
						var _satuan = {
							'satuan': $(tr).find('input.satuan').val().toUpperCase(),
							'pengali': numeral.unformat($(tr).find('input.pengali').val())
						};

						return _satuan;
					});

					var data = {
						'kode': $(div).find('.kode').val().toUpperCase(),
						'nama': $(div).find('.nama').val().toUpperCase(),
						'brand': $(div).find('.brand').val().toUpperCase(),
						'group': $(div).find('.group').val(),
						'keterangan': $(div).find('.keterangan').val(),
						'satuan': satuan
					};

			        $.ajax({
			            url: 'parameter/Item/edit',
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
			                    bootbox.alert(data.message);
			                }
			            }
			        });
				}
			});
		}
	}, // end - edit

	delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				kode = $(tr).data('kode');

		        $.ajax({
		            url: 'parameter/Item/delete',
		            data: {
		                'kode': kode
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

item.start_up();