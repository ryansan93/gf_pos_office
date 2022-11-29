var nama = null;
var deskripsi = null;
var tgl_mulai = null;
var tgl_akhir = null;
var level = null;
var persen = null;
var nilai = null;
var non_member = null;
var member = null;
var min_beli = null;

var diskon = {
	start_up: function () {
	}, // end - start_up

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/diskon/modalAddForm',{
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
				$("#StartDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date((today+' 00:00:00')))
		        });
		        $("#EndDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date((today+' 23:59:59')))
		        });
		        $("#StartDate").on("dp.change", function (e) {
	        		var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
	            	$("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        });
		        $("#EndDate").on("dp.change", function (e) {
	        		var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
	        		if ( maxDate >= (today+' 00:00:00') ) {
	            		$("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
	        		}
		        });

		        $(this).find('.nama').val(nama);
				$(this).find('.deskripsi').val(deskripsi);
				$(this).find('#StartDate').data('DateTimePicker').date(moment(new Date(tgl_mulai)));
				$(this).find('#EndDate').data('DateTimePicker').date(moment(new Date(tgl_akhir)));
				$(this).find('.level').val(level);
				$(this).find('.persen').val(numeral.formatDec(persen));
				$(this).find('.nilai').val(numeral.formatDec(nilai));
				if ( non_member == 1 ) {
					$(this).find('.non_member').attr('checked', true);
				}
				if ( member == 1 ) {
					$(this).find('.member').attr('checked', true);
				}
				$(this).find('.min_beli').val(numeral.formatDec(min_beli));
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

		$('.modal').modal('hide');

        $.get('parameter/diskon/modalEditForm',{
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
			var _non_member = 0;
			var _member = 0;
			if( $(div).find('.non_member').is(':checked') ) {
				_non_member = 1;
			}

			if( $(div).find('.member').is(':checked') ) {
				_member = 1;
			}

			if ( _non_member == 0 && _member == 0 ) {
				bootbox.alert('Harap pilih member atau non member untuk diskon.');
			} else {
				$('.modal').modal('hide');

				nama = $(div).find('.nama').val().toUpperCase();
				deskripsi = $(div).find('.deskripsi').val().toUpperCase();
				tgl_mulai = dateSQL($(div).find('#StartDate').data('DateTimePicker').date());
				tgl_akhir = dateSQL($(div).find('#EndDate').data('DateTimePicker').date());
				level = $(div).find('.level').val();
				persen = numeral.unformat($(div).find('.persen').val());
				nilai = numeral.unformat($(div).find('.nilai').val());
				non_member = _non_member;
				member = _member;
				min_beli = numeral.unformat($(div).find('.min_beli').val());

				bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
					if ( result ) {
						var data = {
							'nama': nama,
							'deskripsi': deskripsi,
							'tgl_mulai': tgl_mulai,
							'tgl_akhir': tgl_akhir,
							'level': level,
							'persen': persen,
							'nilai': nilai,
							'non_member': non_member,
							'member': member,
							'min_beli': min_beli
						};

				        $.ajax({
				            url: 'parameter/Diskon/save',
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
				                    	diskon.modalAddForm();
				                    });
				                }
				            }
				        });
					} else {
						diskon.modalAddForm();
					}
				});
			}
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
			var _non_member = 0;
			var _member = 0;
			if( $(div).find('.non_member').is(':checked') ) {
				_non_member = 1;
			}

			if( $(div).find('.member').is(':checked') ) {
				_member = 1;
			}

			if ( _non_member == 0 && _member == 0 ) {
				bootbox.alert('Harap pilih member atau non member untuk diskon.');
			} else {
				$('.modal').modal('hide');

				var kode = $(elm).data('kode');
				nama = $(div).find('.nama').val().toUpperCase();
				deskripsi = $(div).find('.deskripsi').val().toUpperCase();
				tgl_mulai = dateSQL($(div).find('#StartDate').data('DateTimePicker').date());
				tgl_akhir = dateSQL($(div).find('#EndDate').data('DateTimePicker').date());
				level = $(div).find('.level').val();
				persen = numeral.unformat($(div).find('.persen').val());
				nilai = numeral.unformat($(div).find('.nilai').val());
				non_member = _non_member;
				member = _member;
				min_beli = numeral.unformat($(div).find('.min_beli').val());

				bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
					if ( result ) {
						var data = {
							'kode': kode,
							'nama': nama,
							'deskripsi': deskripsi,
							'tgl_mulai': tgl_mulai,
							'tgl_akhir': tgl_akhir,
							'level': level,
							'persen': persen,
							'nilai': nilai,
							'non_member': non_member,
							'member': member,
							'min_beli': min_beli
						};

				        $.ajax({
				            url: 'parameter/Diskon/edit',
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
				                    	diskon.modalEditForm();
				                    });
				                }
				            }
				        });
					} else {
						diskon.modalEditForm();
					}
				});
			}
		}
	}, // end - edit

	delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				kode = $(tr).data('kode');

		        $.ajax({
		            url: 'parameter/Diskon/delete',
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

diskon.start_up();