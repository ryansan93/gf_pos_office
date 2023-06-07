var sp = {
	startUp: function () {
		sp.settingUp();
	}, // end - startUp

	settingUp: function() {
		$('.branch').select2();
		$("#StartDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });
        $("#EndDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });
	}, // end - settingUp

	getLists: function() {
		var err = 0;

		$.map( $('[data-required=1]'), function(ipt) {
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
			var params = {
				'branch': $('.branch').select2('val'),
				'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
				'end_date': dateSQL($('#EndDate').data('DateTimePicker').date())
			};

			$.ajax({
	            url: 'transaksi/SinkronPajak/getLists',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();

	                if ( data.status == 1 ) {
		                $('table.tbl_real tbody').html( data.html.real );
		                $('table.tbl_pajak tbody').html( data.html.pajak );
	                } else {
	                	bootbox.alert( data.message );
	                }
	            }
	        });
		}
	}, // end - getLists

	sinkron: function() {
		var err = 0;

		$.map( $('[data-required=1]'), function(ipt) {
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
			bootbox.confirm('Apakah anda yakin ingin sinkron data penjualan tanggal <b>'+$('#StartDate').find('input').val().toUpperCase()+'</b> s/d <b>'+$('#EndDate').find('input').val().toUpperCase()+'</b> ?', function (result) {
				if ( result ) {					
					var params = {
						'branch': $('.branch').select2('val'),
						'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
						'end_date': dateSQL($('#EndDate').data('DateTimePicker').date())
					};

					$.ajax({
			            url: 'transaksi/SinkronPajak/sinkron',
			            data: {
			                'params': params
			            },
			            type: 'POST',
			            dataType: 'JSON',
			            beforeSend: function() { showLoading(); },
			            success: function(data) {
			                hideLoading();

			                if ( data.status == 1 ) {
			                	bootbox.alert( data.message, function () {
			                		sp.getLists();
			                	});
			                } else {
			                	bootbox.alert( data.message );
			                }
			            }
			        });
				}
			});
		}
	}, // end - sinkron
};

sp.startUp();