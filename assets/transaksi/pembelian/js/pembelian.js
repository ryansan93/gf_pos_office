var beli = {
    start_up: function () {
        beli.setting_up();
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
        $("#TglBeli").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            minDate: moment(new Date((today+' 00:00:00'))).subtract(7, 'days')
        });
        if ( !empty($("#TglBeli").find('input').data('tgl')) ) {
            var tgl = $("#TglBeli").find('input').data('tgl');
            $("#TglBeli").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $('.supplier').selectpicker();
        $('.branch').selectpicker();
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

        var tr_clone = $(tr).clone();

        $(tr_clone).find('input, select').val('');

        $(tbody).append( $(tr_clone) );

        $(tr_clone).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });
    }, // end - addRow

    removeRow: function(elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 1 ) {
            $(tr).remove();
        }
    }, // end - removeRow

    setSatuanAndGroup: function(elm) {
        var tr = $(elm).closest('tr');

        var satuan = $(elm).find('option:selected').attr('data-satuan');
        var group = $(elm).find('option:selected').attr('data-namagroup');

        $(tr).find('.satuan').val( satuan );
        $(tr).find('.group').val( group );
    }, // end - setSatuanAndGroup

    hitTotal: function(elm) {
        var tr = $(elm).closest('tr');

        var jumlah = numeral.unformat($(tr).find('.jumlah').val());
        var harga = numeral.unformat($(tr).find('.harga').val());

        var total = jumlah * harga;

        $(tr).find('.total').val( numeral.formatDec(total) );

        beli.hitGrandTotal( elm );
    }, // end - hitTotal

    hitGrandTotal: function(elm) {
        var tbody = $(elm).closest('tbody');
        var tfoot = $(tbody).next('tfoot');

        var grand_total = 0;
        $.map( $(tbody).find('tr'), function(_tr) {
            var total = numeral.unformat( $(_tr).find('.total').val() );
            grand_total += total;
        });

        $(tfoot).find('.grand_total b').text( numeral.formatDec(grand_total) );
    }, // end - hitGrandTotal

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

            beli.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/Pembelian/loadForm',
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
                beli.setting_up();
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
                url : 'transaksi/Pembelian/getLists',
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
        var dcontent = $('div#action');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#a94442'});
                } else {
                    $(ipt).parent().addClass('has-error');
                }
                err++;
            } else {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#000000'});
                } else {
                    $(ipt).parent().removeClass('has-error');
                }
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
                            'harga': numeral.unformat( $(_tr).find('.harga').val() ),
                            'total': numeral.unformat( $(_tr).find('.total').val() ),
                        };

                        return _detail;
                    });

                    var data = {
                        'branch': $(dcontent).find('select.branch').val(),
                        'supplier': $(dcontent).find('select.supplier').val(),
                        'tgl_beli': dateSQL( $(dcontent).find('#TglBeli').data('DateTimePicker').date() ),
                        'total': numeral.unformat( $(dcontent).find('tfoot .grand_total b').text() ),
                        'nama_pic': $(dcontent).find('.nama_pic').val().toUpperCase(),
                        'keterangan': $(dcontent).find('.keterangan').val().toUpperCase(),
                        'no_faktur': $(dcontent).find('.no_faktur').val().toUpperCase(),
                        'detail': detail
                    };

                    var file_tmp = $(dcontent).find('.file_lampiran').get(0).files[0];

                    var formData = new FormData();

                    formData.append('data', JSON.stringify(data));
                    formData.append('file', file_tmp);

                    $.ajax({
                        url: 'transaksi/Pembelian/save',
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
                                    beli.loadForm(data.content.id);
                                    var dcontent_riwayat = $('div#riwayat');
                                    var start_date = $(dcontent_riwayat).find('#StartDate input').val();
                                    var end_date = $(dcontent_riwayat).find('#EndDate input').val();
                                    if ( !empty(start_date) && !empty(end_date) ) {
                                        beli.getLists();
                                    }
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
        var dcontent = $('div#action');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#a94442'});
                } else {
                    $(ipt).parent().addClass('has-error');
                }
                err++;
            } else {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#000000'});
                } else {
                    $(ipt).parent().removeClass('has-error');
                }
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
                            'harga': numeral.unformat( $(_tr).find('.harga').val() ),
                            'total': numeral.unformat( $(_tr).find('.total').val() ),
                        };

                        return _detail;
                    });

                    var data = {
                        'kode_beli': $(elm).data('id'),
                        'branch': $(dcontent).find('select.branch').val(),
                        'supplier': $(dcontent).find('select.supplier').val(),
                        'nama_pic': $(dcontent).find('.nama_pic').val().toUpperCase(),
                        'tgl_beli': dateSQL( $(dcontent).find('#TglBeli').data('DateTimePicker').date() ),
                        'total': numeral.unformat( $(dcontent).find('tfoot .grand_total b').text() ),
                        'keterangan': $(dcontent).find('.keterangan').val().toUpperCase(),
                        'no_faktur': $(dcontent).find('.no_faktur').val().toUpperCase(),
                        'detail': detail
                    };

                    var file_tmp = $(dcontent).find('.file_lampiran').get(0).files[0];

                    var formData = new FormData();

                    formData.append('data', JSON.stringify(data));
                    formData.append('file', file_tmp);

                    $.ajax({
                        url: 'transaksi/Pembelian/edit',
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
                                    beli.loadForm(data.content.id);
                                    var dcontent_riwayat = $('div#riwayat');
                                    var start_date = $(dcontent_riwayat).find('#StartDate input').val();
                                    var end_date = $(dcontent_riwayat).find('#EndDate input').val();
                                    if ( !empty(start_date) && !empty(end_date) ) {
                                        beli.getLists();
                                    }
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
                var kode_beli = $(elm).data('id');

                $.ajax({
                    url: 'transaksi/Pembelian/delete',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'kode': kode_beli
                    },
                    beforeSend: function() {
                        showLoading();
                    },
                    success: function(data) {
                        hideLoading();
                        if ( data.status == 1 ) {
                            bootbox.alert(data.message, function() {
                                beli.loadForm();
                                var dcontent_riwayat = $('div#riwayat');
                                var start_date = $(dcontent_riwayat).find('#StartDate input').val();
                                var end_date = $(dcontent_riwayat).find('#EndDate input').val();
                                if ( !empty(start_date) && !empty(end_date) ) {
                                    beli.getLists();
                                }
                            });
                        } else {
                            bootbox.alert(data.message);
                        };
                    },
                });
            }
        });
    }, // end - delete
};

beli.start_up();