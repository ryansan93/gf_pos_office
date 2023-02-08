var ppn = {
	startUp:  function () {
	}, // end - startUp

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/Ppn/modalAddForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                var today = moment(new Date()).format('YYYY-MM-DD');
				$("#TglBerlaku").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date((today+' 00:00:00')))
		        });

		        $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this) );
				});
            });
        },'html');
	}, // end - modalAddForm

	save: function () {
		var modal = $('.modal');

		var err = 0;
		$.map( $(modal).find('[data-required=1]'), function (ipt) {
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
			bootbox.confirm('Apakah anda yakin ingin menyimpan data PPN ?', function (result) {
				if ( result ) {
					var params = {
						'branch_kode': $(modal).find('.branch').val(),
						'tgl_berlaku': dateSQL( $(modal).find('#TglBerlaku').data('DateTimePicker').date() ),
						'nilai': numeral.unformat( $(modal).find('.nilai').val() )
					};

					$.ajax({
			            url: 'parameter/Ppn/save',
			            data: {
			                'params': params
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

	delete: function (elm) {
		bootbox.confirm('Apakah anda yakin ingin meng-hapus data PPN ?', function (result) {
			if ( result ) {
				$.ajax({
		            url: 'parameter/Ppn/delete',
		            data: {
		                'id_ppn': $(elm).attr('data-id')
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

ppn.startUp();