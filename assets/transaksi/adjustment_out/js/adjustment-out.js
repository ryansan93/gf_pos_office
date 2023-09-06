var adjout = {
	start_up: function () {
		adjout.setting_up();
	}, // end - start_up

	setting_up: function() {
        $("#StartDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });
        $("#EndDate").datetimepicker({
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

        var today = moment(new Date()).format('YYYY-MM-DD');
        $("#TglAdjust").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            // minDate: moment(new Date((today+' 00:00:00')))
        });
        if ( !empty($("#TglAdjust").find('input').data('tgl')) ) {
            var tgl = $("#TglAdjust").find('input').data('tgl');
            $("#TglAdjust").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $('select.gudang').select2();
        $('select.item').select2().on('select2:select', function (e) {
            var _tr = $(this).closest('tr');
            var select_satuan = $(_tr).find('select.satuan');

            var data = e.params.data.element.dataset;
            var satuan = JSON.parse( data.satuan );

            var opt = '<option value="">Pilih Satuan</option>';
            for (var i = 0; i < satuan.length; i++) {
                opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'">'+satuan[i].satuan+'</option>';
            }

            $(select_satuan).html( opt );
            $(select_satuan).removeAttr('disabled');
            $(_tr).find('.jumlah').removeAttr('disabled');
            $(_tr).find('.harga').removeAttr('disabled');
        });

        $('select.gudang_riwayat').select2();
        $('.gudang_riwayat').select2({placeholder: 'Pilih Item'}).on("select2:select", function (e) {
            var branch = $('.gudang_riwayat').select2().val();

            for (var i = 0; i < branch.length; i++) {
                if ( branch[i] == 'all' ) {
                    $('.gudang_riwayat').select2().val('all').trigger('change');

                    i = branch.length;
                }
            }

            $('.gudang_riwayat').next('span.select2').css('width', '100%');
        });
        $('.gudang_riwayat').next('span.select2').css('width', '100%');
    }, // end - setting_up

    showNameFile : function(elm, isLable = 1) {
        var _label = $(elm).closest('label');
        var _a = _label.prev('a[name=dokumen]');
        _a.removeClass('hide');
        // var _allowtypes = $(elm).data('allowtypes').split('|');
        var _dataName = $(elm).data('name');
        var _allowtypes = ['doc', 'DOC', 'docx', 'DOCX', 'jpg', 'JPG', 'jpeg', 'JPEG', 'pdf', 'PDF', 'png', 'PNG'];
        var _type = $(elm).get(0).files[0]['name'].split('.').pop();
        var _namafile = $(elm).val();
        var _temp_url = URL.createObjectURL($(elm).get(0).files[0]);
        _namafile = _namafile.substring(_namafile.lastIndexOf("\\") + 1, _namafile.length);

        if (in_array(_type, _allowtypes)) {
            if (isLable == 1) {
                if (_a.length) {
                    _a.attr('title', _namafile);
                    _a.attr('href', _temp_url);
                    if ( _dataName == 'name' ) {
                        $(_a).text( _namafile );  
                    }
                }
            } else if (isLable == 0) {
                $(elm).closest('label').attr('title', _namafile);
            }
            $(elm).attr('data-filename', _namafile);
        } else {
            $(elm).val('');
            $(elm).closest('label').attr('title', '');
            $(elm).attr('data-filename', '');
            _a.addClass('hide');
            bootbox.alert('Format file tidak sesuai. Mohon attach ulang.');
        }
    }, // end - showNameFile

    addRow: function(elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        $(tr).find('select.item').select2('destroy')
                                   .removeAttr('data-live-search')
                                   .removeAttr('data-select2-id')
                                   .removeAttr('aria-hidden')
                                   .removeAttr('tabindex');
        $(tr).find('select.item option').removeAttr('data-select2-id');

        var tr_clone = $(tr).clone();

        $(tr_clone).find('input, select').val('');

        $(tr_clone).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $(tbody).append( $(tr_clone) );

        $.each($(tbody).find('select.item'), function(a) {
            $(this).select2();
            $(this).on('select2:select', function (e) {
                var _tr = $(this).closest('tr');
                var select_satuan = $(_tr).find('select.satuan');

                var data = e.params.data.element.dataset;
                var satuan = JSON.parse( data.satuan );

                var opt = '<option value="">Pilih Satuan</option>';
                for (var i = 0; i < satuan.length; i++) {
                    opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'">'+satuan[i].satuan+'</option>';
                }

                $(select_satuan).html( opt );
                $(select_satuan).removeAttr('disabled');
                $(_tr).find('.jumlah').removeAttr('disabled');
                $(_tr).find('.harga').removeAttr('disabled');
            });
        });
    }, // end - addRow

    removeRow: function(elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 1 ) {
            $(tr).remove();
        }
    }, // end - removeRow

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

            adjout.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/AdjustmentOut/loadForm',
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
                adjout.setting_up();
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
                'end_date': dateSQL( $(dcontent).find('#EndDate').data('DateTimePicker').date() ),
                'gudang_kode': $(dcontent).find('.gudang_riwayat').select2().val(),
            };

            $.ajax({
                url : 'transaksi/AdjustmentOut/getLists',
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
					var detail = $.map( $(dcontent).find('table tbody tr'), function(_tr) {
                        var _detail = {
                            'item_kode': $(_tr).find('.item').val(),
                            'jumlah': numeral.unformat( $(_tr).find('.jumlah').val() ),
                            'satuan': $(_tr).find('.satuan').val(),
                            'pengali': $(_tr).find('.satuan option:selected').attr('data-pengali')
                        };

                        return _detail;
                    });

                    var data = {
                        'gudang': $(dcontent).find('select.gudang').val(),
                        'tgl_adjust': dateSQL( $(dcontent).find('#TglAdjust').data('DateTimePicker').date() ),
                        'keterangan': $(dcontent).find('.keterangan').val().toUpperCase(),
                        'detail': detail
                    };

					$.ajax({
		                url: 'transaksi/AdjustmentOut/save',
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
		                    	adjout.hitungStok( data.content.id );
		                    } else {
		                        bootbox.alert(data.message);
		                    };
		                },
		            });
				}
			});
		}
	}, // end - save

    hitungStok: function (kode) {
        var params = {'kode': kode};

        $.ajax({
            url: 'transaksi/AdjustmentOut/hitungStok',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading('Hitung Stok . . .'); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert( data.message, function () {
                        location.reload();
                    });
                } else {
                    bootbox.alert( data.message );
                }
            }
        });
    }, // end - hitungStok
};

adjout.start_up();