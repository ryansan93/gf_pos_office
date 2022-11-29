var menu = null;
var jenis_pesanan = null;
var tgl_berlaku = null;
var harga = null;

var hm = {
	start_up: function () {
	}, // end - start_up

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/HargaMenu/modalAddForm',{
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

                var today = moment(new Date()).format('YYYY-MM-DD');
				$("#TglBerlaku").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date((today+' 00:00:00')))
		        });

		        $(this).find('.menu').val(menu);
				$(this).find('.jenis_pesanan').val(jenis_pesanan);
				$(this).find('#TglBerlaku').data('DateTimePicker').date(moment(new Date(tgl_berlaku)));
				$(this).find('.harga').val(numeral.formatDec(harga));
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

		$('.modal').modal('hide');

        $.get('parameter/HargaMenu/modalEditForm',{
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

				$("#StartDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date(($("#StartDate input").data('tgl')+' 00:00:00')))
		        });
		        $("#EndDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date(($("#EndDate input").data('tgl')+' 23:59:59')))
		        });
		        $("#StartDate").on("dp.change", function (e) {
	        		var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
	            	$("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        });
		        $("#EndDate").on("dp.change", function (e) {
	        		var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
	            	$("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
		        });

				$(this).find('#StartDate').data('DateTimePicker').date(moment(new Date($("#StartDate input").data('tgl'))));
				$(this).find('#EndDate').data('DateTimePicker').date(moment(new Date($("#EndDate input").data('tgl'))));
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

			menu = $(div).find('.menu').val();
			jenis_pesanan = $(div).find('.jenis_pesanan').val();
			tgl_berlaku = dateSQL($(div).find('#TglBerlaku').data('DateTimePicker').date());
			harga = numeral.unformat($(div).find('.harga').val());

			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var data = {
						'menu': menu,
						'jenis_pesanan': jenis_pesanan,
						'tgl_berlaku': tgl_berlaku,
						'harga': harga
					};

			        $.ajax({
			            url: 'parameter/HargaMenu/save',
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
			                    	hm.modalAddForm();
			                    });
			                }
			            }
			        });
				} else {
					hm.modalAddForm();
				}
			});
		}
	}, // end - save

	delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				var data = {
					'menu': $(tr).find('td.menu').data('val'),
					'jenis_pesanan': $(tr).find('td.jenis_pesanan').data('val'),
					'tgl_berlaku': $(tr).find('td.tgl_mulai').data('val'),
					'harga': $(tr).find('td.harga').data('val')
				};

		        $.ajax({
		            url: 'parameter/HargaMenu/delete',
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
    }, // end - delete
};

hm.start_up();