var pm = {
	start_up: function () {
		$('table.tbl_paket_menu tbody tr.head td:not(.action)').click( function() {
			pm.collapseRow(this);
		});
	}, // end - start_up

	addRow: function (elm) {
		var tr = $(elm).closest('tr');
		var tbody = $(tr).closest('tbody');

		var tr_clone = $(tr).clone();

		$(tr_clone).find('select, input').val('');

		$(tbody).append( tr_clone );
	}, // end - addRow

	removeRow: function (elm) {
		var tr = $(elm).closest('tr');
		var tbody = $(tr).closest('tbody');

		if ( $(tbody).find('tr').length > 1 ) {
			$(tr).remove();
		}
	}, // end - removeRow

	collapseRow: function(elm) {
		var tr_head = $(elm).closest('tr.head');
		var tr_detail = $(tr_head).next('tr.detail:first');

		if ( $(tr_detail).hasClass('hide') ) {
			$(tr_detail).removeClass('hide');
		} else {
			$(tr_detail).addClass('hide');
		}
	}, // end - collapseRow

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/PaketMenu/modalAddForm',{
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
					$(this).priceFormat(Config[$(this).data('tipe')]);
				});
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

		$('.modal').modal('hide');

        $.get('parameter/PaketMenu/modalEditForm',{
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
					$(this).priceFormat(Config[$(this).data('tipe')]);
				});

				$(this).find('textarea').addClass('uppercase');
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
					var detail = $.map( $(div).find('.tbl_detail tbody tr'), function(tr) {
						var _detail = {
							'menu_kode': $(tr).find('.menu').val(),
							'jumlah_min': numeral.unformat($(tr).find('.jumlah_min').val()),
							'jumlah_max': numeral.unformat($(tr).find('.jumlah_max').val())
						};

						return _detail;
					});
					var data = {
						'nama': $(div).find('.nama').val().toUpperCase(),
						'menu_kode': $(div).find('.menu_paket').val(),
						'max_pilih': numeral.unformat($(div).find('.jumlah_pilih').val()),
						'detail': detail
					};

			        $.ajax({
			            url: 'parameter/PaketMenu/save',
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
					var detail = $.map( $(div).find('.tbl_detail tbody tr'), function(tr) {
						var _detail = {
							'menu_kode': $(tr).find('.menu').val(),
							'jumlah_min': numeral.unformat($(tr).find('.jumlah_min').val()),
							'jumlah_max': numeral.unformat($(tr).find('.jumlah_max').val())
						};

						return _detail;
					});
					var data = {
						'kode': $(elm).data('kode'),
						'nama': $(div).find('.nama').val().toUpperCase(),
						'menu_kode': $(div).find('.menu_paket').val(),
						'max_pilih': numeral.unformat($(div).find('.jumlah_pilih').val()),
						'detail': detail
					};

			        $.ajax({
			            url: 'parameter/PaketMenu/edit',
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
		            url: 'parameter/PaketMenu/delete',
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

pm.start_up();