var meja = {
	startUp: function () {
	}, // end - startUp

	addRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        var tr_clone = $(tr).clone();

        $(tr_clone).find('input, select').val('');

        $(tr_clone).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $(tbody).append( $(tr_clone) );
    }, // end - addRow

    removeRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 0 ) {
            $(tr).remove();
        }
    }, // end - addRow

	modalAddForm: function () {
        $.get('parameter/Meja/modalAddForm',{
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
					priceFormat( $(this) );
				});

				$(this).find('.branch').select2({placeholder: 'Pilih Branch'});

		        $(this).removeAttr('tabindex');
            });
        },'html');
	}, // end - modalAddForm

	save: function () {
		var div = $('.modal');

		var err = 0;
		$.map( $(div).find('[data-required=1]'), function(ipt) {
			if ( empty( $(ipt).val() ) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin menyimpan data layout meja ?', function(result) {
				if ( result ) {
					var meja = $.map( $(div).find('.tbl_meja tbody tr'), function(tr) {
						return $(tr).find('input').val();
					});

					var params = {
						'kode_branch': $(div).find('.branch').select2('val'),
						'lantai': $(div).find('.lantai').val(),
						'kontrol_meja': $(div).find('[name=kontrol_meja]:checked').val(),
						'meja': meja
					};

					$.ajax({
			            url : 'parameter/Meja/save',
			            data : {
			                'params' :  params
			            },
			            type : 'POST',
			            dataType : 'JSON',
			            beforeSend : function(){ showLoading(); },
			            success : function(data){
			                hideLoading();
			                if ( data.status == 1 ) {
			                	bootbox.alert( data.message, function() {
			                		location.reload();
			                	});
			                } else {
			                	bootbox.alert( data.message );
			                }
			            },
			        });
				}
			});
		}
	}, // end - save

	delete: function (elm) {
		bootbox.confirm('Apakah anda yakin ingin me non aktif kan data layout meja ?', function(result) {
			if ( result ) {
				var params = {
					'id': $(elm).attr('data-id')
				};

				$.ajax({
		            url : 'parameter/Meja/delete',
		            data : {
		                'params' :  params
		            },
		            type : 'POST',
		            dataType : 'JSON',
		            beforeSend : function(){ showLoading(); },
		            success : function(data){
		                hideLoading();
		                if ( data.status == 1 ) {
		                	bootbox.alert( data.message, function() {
		                		location.reload();
		                	});
		                } else {
		                	bootbox.alert( data.message );
		                }
		            },
		        });
			}
		});
	}, // end - delete

	aktif: function (elm) {
		bootbox.confirm('Apakah anda yakin ingin meng aktif kan data layout meja ?', function(result) {
			if ( result ) {
				var params = {
					'id': $(elm).attr('data-id')
				};

				$.ajax({
		            url : 'parameter/Meja/aktif',
		            data : {
		                'params' :  params
		            },
		            type : 'POST',
		            dataType : 'JSON',
		            beforeSend : function(){ showLoading(); },
		            success : function(data){
		                hideLoading();
		                if ( data.status == 1 ) {
		                	bootbox.alert( data.message, function() {
		                		location.reload();
		                	});
		                } else {
		                	bootbox.alert( data.message );
		                }
		            },
		        });
			}
		});
	}, // end - aktif
};

meja.startUp();