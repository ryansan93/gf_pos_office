var terima = {
	start_up: function () {
		terima.setting_up();
	}, // end - start_up

	setting_up: function() {
        $("#StartDate, #StartDateBeli").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });
        $("#EndDate, #EndDateBeli").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
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
        $("#StartDateBeli").on("dp.change", function (e) {
            var minDate = dateSQL($("#StartDateBeli").data("DateTimePicker").date())+' 00:00:00';
            $("#EndDateBeli").data("DateTimePicker").minDate(moment(new Date(minDate)));
        });
        $("#EndDateBeli").on("dp.change", function (e) {
            var maxDate = dateSQL($("#EndDateBeli").data("DateTimePicker").date())+' 23:59:59';
            if ( maxDate >= (today+' 00:00:00') ) {
                $("#StartDateBeli").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
            }
        });

        var today = moment(new Date()).format('YYYY-MM-DD');
        $("#TglTerima").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            minDate: moment(new Date((today+' 00:00:00'))).subtract(7, 'days')
        });
        if ( !empty($("#TglTerima").find('input').data('tgl')) ) {
            var tgl = $("#TglTerima").find('input').data('tgl');
            $("#TglTerima").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            $(this).priceFormat(Config[$(this).data('tipe')]);
        });

        $('.no_faktur').selectpicker();
    }, // end - setting_up

	listFakturPembelian: function() {
		var start_date = $('#StartDateBeli').find('input').val();
		var end_date = $('#EndDateBeli').find('input').val();

		if ( empty(start_date) || empty(end_date) ) {
			bootbox.alert('Harap isi periode pembelian terlebih dahulu.');
		} else {
			var params = {
				'start_date': dateSQL( $('#StartDateBeli').data('DateTimePicker').date() ),
				'end_date': dateSQL( $('#EndDateBeli').data('DateTimePicker').date() )
			};

			$.ajax({
                url: 'transaksi/Penerimaan/listFakturPembelian',
                dataType: 'json',
                type: 'post',
                data: {
                	'params': params
                },
                beforeSend: function() {
                    showLoading();
                },
                success: function(data) {
                    hideLoading();
                    if ( data.status == 1 ) {
                        var opt = '<option>-- Pilih No. Faktur --</option>';
                        for (var i = 0; i < data.content.list.length; i++) {
                        	opt += '<option value="'+data.content.list[i].kode_beli+'">'+data.content.list[i].supplier+' | '+data.content.list[i].no_faktur+'</option>';
                        }

                        $('select.no_faktur').html( opt );
                        $('.no_faktur').selectpicker('refresh');
                    } else {
                        bootbox.alert(data.message);
                    };
                },
            });
		}
	}, // end - listFakturPembelian

	dataBeli: function(elm) {
		var val = $(elm).val();

		if ( empty(val) ) {
			$('.dataBeli').html('');
		} else {
			$.ajax({
                url: 'transaksi/Penerimaan/dataBeli',
                dataType: 'json',
                type: 'post',
                data: {
                	'kode_beli': val
                },
                beforeSend: function() {
                    showLoading();
                },
                success: function(data) {
                    hideLoading();
                    if ( data.status == 1 ) {
                    	$('.dataBeli').html(data.content.html);

                    	terima.setting_up();
                    } else {
                        bootbox.alert(data.message);
                    };
                },
            });
		}
	}, // end - dataBeli

	changeTabActive: function(elm) {
        var vhref = $(elm).data('href');
        var edit = $(elm).data('edit');
        // change tab-menu
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        // change tab-content
        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');

        if ( vhref == 'action' ) {
            var v_id = $(elm).attr('data-id');

            terima.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/Penerimaan/loadForm',
            data : {
                'id' :  v_id,
                'resubmit' : resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                hideLoading();
                $(dcontent).html(html);
                terima.setting_up();
            },
        });
    }, // end - loadForm

	getLists: function() {
        var dcontent = $('div#riwayat');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
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
            var tbody = $(dcontent).find('table.tbl_riwayat tbody');

            var params = {
                'start_date': dateSQL( $(dcontent).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(dcontent).find('#EndDate').data('DateTimePicker').date() )
            };

            $.ajax({
                url : 'transaksi/Penerimaan/getLists',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ showLoading(); },
                success : function(html){
                    hideLoading();
                    $(tbody).html(html);
                },
            });
        }
    }, // end - getLists

	save: function() {
		var dcontent = $('#action');
		var err = 0;
		$.map( $(dcontent).find('[data-required=1]'), function(ipt) {
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
					var detail = $.map( $(dcontent).find('.tbl_detail tr.data'), function(tr) {
						var _detail = {
							'item_kode': $(tr).data('item'),
							'jumlah_terima': numeral.unformat($(tr).find('input.jumlah_terima').val()),
							'harga': $(tr).data('harga')
						};

						return _detail;
					});

					var data = {
						'tgl_terima': dateSQL( $(dcontent).find('#TglTerima').data('DateTimePicker').date() ),
						'beli_kode': $(dcontent).find('select.no_faktur').val(),
						'detail': detail
					};

					$.ajax({
		                url: 'transaksi/Penerimaan/save',
		                dataType: 'json',
		                type: 'post',
		                data: {
		                	'params': data
		                },
		                beforeSend: function() {
		                    showLoading();
		                },
		                success: function(data) {
		                    hideLoading();
		                    if ( data.status == 1 ) {
		                    	bootbox.alert(data.message, function() {
		                    		location.reload();
		                    	});
		                    } else {
		                        bootbox.alert(data.message);
		                    };
		                },
		            });
				}
			});
		}
	}, // end - save
};

terima.start_up();