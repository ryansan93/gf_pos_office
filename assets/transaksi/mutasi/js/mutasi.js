var mutasi = {
	start_up: function () {
		mutasi.setting_up();
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
        $("#TglMutasi").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            minDate: moment(new Date((today+' 00:00:00'))).subtract(7, 'days'),
            useCurrent: false
        }).on('dp.hide', function(e) {
            mutasi.getHargaItem();
        });

        if ( !empty($("#TglMutasi").find('input').data('tgl')) ) {
            var tgl = $("#TglMutasi").find('input').data('tgl');
            $("#TglMutasi").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $('select.asal').select2().on('select2:select', function (e) {
            mutasi.getHargaItem();
        });
        $('select.tujuan').select2();
        $('select.item').select2().on('select2:select', function (e) {
            var _tr = $(this).closest('tr');
            var select_satuan = $(_tr).find('select.satuan');

            var val_satuan = $(select_satuan).attr('data-val');

            var data = e.params.data.element.dataset;

            console.log( data );

            var coa = JSON.parse( data.coa );
            var ket_coa = JSON.parse( data.ketcoa );

            $(_tr).find('td.coa').html( coa+'<br>'+ket_coa );

            var satuan = JSON.parse( data.satuan );
            var opt = '<option value="">Pilih Satuan</option>';
            for (var i = 0; i < satuan.length; i++) {
                var selected = null;
                if ( !empty(select_satuan) ) {
                    if ( satuan[i].satuan == val_satuan ) {
                        selected = 'selected';
                    }
                }

                opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'" data-harga="'+satuan[i].harga+'" '+selected+' >'+satuan[i].satuan+'</option>';
            }

            $(select_satuan).html( opt );
            $(select_satuan).removeAttr('disabled');
            $(_tr).find('.jumlah').removeAttr('disabled');

            $(select_satuan).on('change', function() {
                var harga = parseFloat($(this).find('option:selected').attr('data-harga'));

                $(_tr).find('td.harga').html( numeral.formatDec(harga) );

                mutasi.hitTotal( $(this) );
            });
        });
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
                var data = e.params.data.element.dataset;

                var _tr = $(this).closest('tr');

                $(_tr).find('.satuan').val( data.satuan );
                $(_tr).find('.group').val( data.namagroup );
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

            mutasi.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    getHargaItem: function() {
        var dcontent = $('div#action');

        var asal = $(dcontent).find('select.asal').select2().val();
        var tgl_mutasi = $(dcontent).find('#TglMutasi input').val();

        if ( !empty(asal) && !empty(tgl_mutasi) ) {
            $('select.item').removeAttr('disabled', 'disabled');

            var tbody = $(dcontent).find('table.tbl_riwayat tbody');

            var params = {
                'asal': asal,
                'tgl_mutasi': dateSQL( $(dcontent).find('#TglMutasi').data('DateTimePicker').date() )
            };

            $.ajax({
                url : 'transaksi/Mutasi/getHargaItem',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ showLoading('Ambil Harga Mutasi . . .'); },
                success : function(html){
                    $.map( $('table tbody').find('tr select.item'), function(select) {
                        $(select).html( html );
                        $(select).select2();
                    });

                    hideLoading();
                    // $(tbody).html(html);
                },
            });
        } else {
            $('select.item').attr('disabled', 'disabled');
            $('select.item').val('');
            $('select.item').select2();
        }
    }, // end - getHargaItem

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/Mutasi/loadForm',
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
                mutasi.setting_up();

                if ( !empty(v_id) && !empty(resubmit) ) {
                    $.map( $(dcontent).find('select.item'), function (select) {
                        var _tr = $(select).closest('tr');
                        var select_satuan = $(_tr).find('select.satuan');

                        var val_satuan = $(select_satuan).attr('data-val');

                        var data_coa = $(select).find('option:selected').attr('data-coa');
                        var data_ketcoa = $(select).find('option:selected').attr('data-ketcoa');

                        var coa = data_coa;
                        var ket_coa = data_ketcoa;

                        $(_tr).find('td.coa').html( coa+'<br>'+ket_coa );

                        var data_satuan = $(select).find('option:selected').attr('data-satuan');

                        var satuan = JSON.parse( data_satuan );

                        var opt = '<option value="">Pilih Satuan</option>';
                        for (var i = 0; i < satuan.length; i++) {
                            var selected = null;
                            if ( !empty(select_satuan) ) {
                                if ( satuan[i].satuan == val_satuan ) {
                                    selected = 'selected';
                                }
                            }

                            opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'" data-harga="'+satuan[i].harga+'" '+selected+' >'+satuan[i].satuan+'</option>';
                        }

                        $(select_satuan).html( opt );
                        $(select_satuan).removeAttr('disabled');
                        $(_tr).find('.jumlah').removeAttr('disabled');

                        $(select_satuan).on('change', function() {
                            var harga = parseFloat($(this).find('option:selected').attr('data-harga'));

                            $(_tr).find('td.harga').html( numeral.formatDec(harga) );

                            mutasi.hitTotal( $(this) );
                        });
                    });
                }
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
                url : 'transaksi/Mutasi/getLists',
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
                        'nama_pic': $(dcontent).find('.nama_pic').val().toUpperCase(),
                        'asal': $(dcontent).find('select.asal').val(),
                        'tujuan': $(dcontent).find('select.tujuan').val(),
                        'tgl_mutasi': dateSQL( $(dcontent).find('#TglMutasi').data('DateTimePicker').date() ),
                        'no_sj': $(dcontent).find('.no_sj').val().toUpperCase(),
                        'keterangan': $(dcontent).find('.keterangan').val().toUpperCase(),
                        'detail': detail
                    };

                    var file_tmp = $(dcontent).find('.file_lampiran').get(0).files[0];

                    var formData = new FormData();

                    formData.append('data', JSON.stringify(data));
                    formData.append('file', file_tmp);

					$.ajax({
		                url: 'transaksi/Mutasi/save',
		                dataType: 'json',
                        type: 'post',
                        async:false,
                        processData: false,
                        contentType: false,
                        data: formData,
		                beforeSend: function() {
		                    showLoading();
		                },
		                success: function(data) {
		                    hideLoading();
		                    if ( data.status == 1 ) {
		                    	bootbox.alert(data.message, function() {
		                    		mutasi.loadForm(data.content.id, '');
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

    edit: function(elm) {
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
            bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
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
                        'kode_mutasi': $(elm).data('kode'),
                        'nama_pic': $(dcontent).find('.nama_pic').val().toUpperCase(),
                        'asal': $(dcontent).find('select.asal').val(),
                        'tujuan': $(dcontent).find('select.tujuan').val(),
                        'tgl_mutasi': dateSQL( $(dcontent).find('#TglMutasi').data('DateTimePicker').date() ),
                        'no_sj': $(dcontent).find('.no_sj').val().toUpperCase(),
                        'keterangan': $(dcontent).find('.keterangan').val().toUpperCase(),
                        'detail': detail
                    };

                    var file_tmp = $(dcontent).find('.file_lampiran').get(0).files[0];

                    var formData = new FormData();

                    formData.append('data', JSON.stringify(data));
                    formData.append('file', file_tmp);

                    $.ajax({
                        url: 'transaksi/Mutasi/edit',
                        dataType: 'json',
                        type: 'post',
                        async:false,
                        processData: false,
                        contentType: false,
                        data: formData,
                        beforeSend: function() {
                            showLoading();
                        },
                        success: function(data) {
                            hideLoading();
                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    mutasi.loadForm(data.content.id, '');
                                });
                            } else {
                                bootbox.alert(data.message);
                            };
                        },
                    });
                }
            });
        }
    }, // end - edit

    delete: function(elm) {
        bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
            if ( result ) {
                var params = {
                    'kode_mutasi': $(elm).data('kode')
                };

                $.ajax({
                    url: 'transaksi/Mutasi/delete',
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
    }, // end - delete

    approve: function(elm) {

        bootbox.confirm('Apakah anda yakin ingin merubah status menjadi terima ?', function(result) {
            if ( result ) {
                var kode = $(elm).data('kode');

                $.ajax({
                    url: 'transaksi/Mutasi/approve',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'kode_mutasi': kode
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
    }, // end - approve

    hitTotal: function (elm) {
        var tr = $(elm).closest('tr');

        var jumlah = numeral.unformat( $(tr).find('.jumlah').val() );
        var harga = numeral.unformat( $(tr).find('.harga').text() );

        var total = jumlah * harga;

        $(tr).find('.total').text( numeral.formatDec(total) );

        mutasi.hitGrandTotal( $(tr) );
    }, // end - hitTotal

    hitGrandTotal: function (tr) {
        var tbody = $(tr).closest('tbody');
        var table = $(tbody).closest('table');

        var grand_total = 0;
        $.map( $(tbody).find('tr'), function(_tr) {
            var total = numeral.unformat( $(_tr).find('.total').text() );

            grand_total += parseFloat( total );
        });

        $(table).find('tfoot .grand_total b').text( numeral.formatDec( grand_total ) );
    }, // end - hitGrandTotal
};

mutasi.start_up();