var bom = {
	startUp: function () {
		bom.settingUp();
	}, // end - startUp

	settingUp: function () {
        var today = moment(new Date()).format('YYYY-MM-DD');
        $("#TglBerlaku").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        if ( $("#TglBerlaku").length > 0 ) {
            var tglBerlaku = $("#TglBerlaku").find('input').attr('data-tgl');
            if ( !empty(tglBerlaku) ) {
                $("#TglBerlaku").data("DateTimePicker").date(new Date(tglBerlaku));
            } else {
                var minDateTglBerlaku = today+' 00:00:00';
                $("#TglBerlaku").data("DateTimePicker").minDate(moment(new Date(minDateTglBerlaku)));
            }
        }

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

        if ( $('select.menu').length > 0 ) {
            $('select.menu').select2();
        }
        $('.menu_riwayat').select2().on('select2:select', function (e) {
            var menu = $('.menu_riwayat').select2().val();

            for (var i = 0; i < menu.length; i++) {
                if ( menu[i] == 'all' ) {
                    $('.menu_riwayat').select2().val('all').trigger('change');

                    i = menu.length;
                }
            }
        });
        $('.item').select2().on('select2:select', function (e) {
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
        });

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });
	}, // end - settingUp

    addRow: function (elm) {
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
        $(tr_clone).find('select.satuan').html('<option value="">Pilih Satuan</option>');
        $(tr_clone).find('select.satuan').attr('disabled', 'disabled');

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
            });
        });
    }, // end - addRow

    removeRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 0 ) {
            $(tr).remove();
        }
    }, // end - addRow

    addRowSatuan: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        var tr_clone = $(tr).clone();
        $(tr_clone).find('input').val('');

        $(tr_clone).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $(tbody).append( $(tr_clone) );
    }, // end - addRowSatuan

    removeRowSatuan: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 1 ) {
            $(tr).remove();
        }
    }, // end - removeRowSatuan

    additionalForm: function (elm) {
        if ( $(elm).is(':checked') ) {
            $('.additional_form').removeClass('hide');
            $('.additional_form').find('[data-required=1]').attr('data-required', 1);
            $('div.menu').addClass('hide');
            $('div.menu').find('select').attr('data-required', 0);
        } else {
            $('.additional_form').addClass('hide');
            $('.additional_form').find('[data-required=1]').attr('data-required', 0);
            $('div.menu').removeClass('hide');
            $('div.menu').find('select').attr('data-required', 1);
        }
    }, // end - additionalForm

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

            bom.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'parameter/BillOfMaterial/loadForm',
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
                
                bom.settingUp();
            },
        });
    }, // end - loadForm

    getLists: function () {
        var div = $('#riwayat');

        var err = 0;
        $.map( $(div).find('[data-required=1]'), function (ipt) {
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
                'start_date': dateSQL( $(div).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(div).find('#EndDate').data('DateTimePicker').date() ),
                'menu_kode': $(div).find('.menu_riwayat').select2('val')
            };

            $.ajax({
                url: 'parameter/BillOfMaterial/getLists',
                data: {
                    'params': params
                },
                type: 'GET',
                dataType: 'HTML',
                beforeSend: function() { showLoading(); },
                success: function(html) {
                    hideLoading();

                    $(div).find('.tbl_riwayat tbody').html( html );
                }
            });
        }
    }, // end - getLists

	save: function () {
        var div = $('#action');

		var err = 0;

        $.map( $(div).find('[data-required=1]'), function (ipt) {
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
            bootbox.confirm('Apakah anda yakin ingin menyimpan data BOM ?', function (result) {
                if ( result ) {
                    var bom_satuan = $.map( $(div).find('.additional_form:not(.hide) tbody tr'), function (tr) {
                        var _bom_satuan = {
                            'satuan': $(tr).find('input.satuan').val().toUpperCase(),
                            'pengali': numeral.unformat($(tr).find('input.pengali').val())
                        };

                        return _bom_satuan;
                    });

                    var list_item = $.map( $(div).find('tr.data'), function (tr) {
                        var _list_item = {
                            'item_kode': $(tr).find('select.item').val(),
                            'satuan': $(tr).find('select.satuan').val(),
                            'pengali': $(tr).find('select.satuan option:selected').attr('data-pengali'),
                            'jenis': $(tr).find('select.item option:selected').attr('data-jenis'),
                            'jumlah': numeral.unformat( $(tr).find('input.jumlah').val() )
                        };

                        return _list_item;
                    });

                    var additional = 0;
                    var nama = null;
                    var menu_kode = null;
                    if ( $(div).find('.additional').is(':checked') ) {
                        additional = 1;
                        nama = $(div).find('.nama').val();
                    } else {
                        menu_kode = $(div).find('select.menu').select2('val');
                    }

                    var params = {
                        'menu_kode': menu_kode,
                        'tanggal': dateSQL( $(div).find('#TglBerlaku').data('DateTimePicker').date() ),
                        'additional': additional,
                        'nama': nama,
                        'jml_porsi': numeral.unformat( $(div).find('.jml_porsi').val() ),
                        'list_item': list_item,
                        'bom_satuan': bom_satuan
                    };

                    $.ajax({
                        url: 'parameter/BillOfMaterial/save',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();
                            if ( data.status == 1 ) {
                                bootbox.alert( data.message, function() {
                                    bom.loadForm();
                                });
                            } else {
                                bootbox.alert( data.message );
                            }
                        }
                    });
                }
            });
        }
	}, // end - save

    edit: function (elm) {
        var div = $('#action');

        var err = 0;

        $.map( $(div).find('[data-required=1]'), function (ipt) {
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
            bootbox.confirm('Apakah anda yakin ingin meng-ubah data BOM ?', function (result) {
                if ( result ) {
                    // var list_item = $.map( $(div).find('tr.data'), function (tr) {
                    //     var _list_item = {
                    //         'item_kode': $(tr).find('select.item').val(),
                    //         'satuan': $(tr).find('select.satuan').val(),
                    //         'pengali': $(tr).find('select.satuan option:selected').attr('data-pengali'),
                    //         'jumlah': numeral.unformat( $(tr).find('input.jumlah').val() )
                    //     };

                    //     return _list_item;
                    // });

                    // var params = {
                    //     'id': $(elm).attr('data-id'),
                    //     'menu_kode': $('.menu').attr('data-kode'),
                    //     'tanggal': dateSQL( $('#TglBerlaku').data('DateTimePicker').date() ),
                    //     'list_item': list_item
                    // };

                    var bom_satuan = $.map( $(div).find('.additional_form:not(.hide) tbody tr'), function (tr) {
                        var _bom_satuan = {
                            'satuan': $(tr).find('input.satuan').val().toUpperCase(),
                            'pengali': numeral.unformat($(tr).find('input.pengali').val())
                        };

                        return _bom_satuan;
                    });

                    var list_item = $.map( $(div).find('tr.data'), function (tr) {
                        var _list_item = {
                            'item_kode': $(tr).find('select.item').val(),
                            'satuan': $(tr).find('select.satuan').val(),
                            'pengali': $(tr).find('select.satuan option:selected').attr('data-pengali'),
                            'jenis': $(tr).find('select.item option:selected').attr('data-jenis'),
                            'jumlah': numeral.unformat( $(tr).find('input.jumlah').val() )
                        };

                        return _list_item;
                    });

                    var additional = 0;
                    var nama = null;
                    var menu_kode = null;
                    if ( $(div).find('.additional').is(':checked') ) {
                        additional = 1;
                        nama = $(div).find('.nama').val();
                    } else {
                        menu_kode = $(div).find('select.menu').select2('val');
                    }

                    var params = {
                        'id': $(elm).attr('data-id'),
                        'menu_kode': menu_kode,
                        'tanggal': dateSQL( $(div).find('#TglBerlaku').data('DateTimePicker').date() ),
                        'additional': additional,
                        'nama': nama,
                        'jml_porsi': numeral.unformat( $(div).find('.jml_porsi').val() ),
                        'list_item': list_item,
                        'bom_satuan': bom_satuan
                    };

                    $.ajax({
                        url: 'parameter/BillOfMaterial/edit',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();
                            if ( data.status == 1 ) {
                                bootbox.alert( data.message, function() {
                                    bom.loadForm( $(elm).attr('data-id') );

                                    bom.getLists();
                                });
                            } else {
                                bootbox.alert( data.message );
                            }
                        }
                    });
                }
            });
        }
    }, // end - edit

    delete: function (elm) {
        bootbox.confirm('Apakah anda yakin ingin meng-hapus data BOM ?', function (result) {
            if ( result ) {
                var params = {
                    'id': $(elm).attr('data-id')
                };

                $.ajax({
                    url: 'parameter/BillOfMaterial/delete',
                    data: {
                        'params': params
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading(); },
                    success: function(data) {
                        hideLoading();
                        if ( data.status == 1 ) {
                            bootbox.alert( data.message, function() {
                                bom.loadForm();

                                bom.getLists();
                            });
                        } else {
                            bootbox.alert( data.message );
                        }
                    }
                });
            }
        });
    }, // end - delete
};

bom.startUp();