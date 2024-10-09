var sm = {
    startUp: function() {
        $('select.branch').select2();
    }, // end - startUp

    validasiSinkron: function() {
        bootbox.confirm('Apakah anda yakin ingin sinkron data master ?', function(result) {
            if ( result ) {
                sm.sinkron();
            }
        });
    }, // end - validasiSinkron

    sinkronList: function(idx = 0) {
        var branch = $('select.branch').select2('val');
        var list_menu = $.map( $('input:checkbox'), function(ipt) {
            if ( $(ipt).is(':checked') ) {
                return $(ipt).attr('data-val');
            }
        });

        if ( !empty(branch) && list_menu.length > 0 ) {
            var params = {
                'index': idx,
                'menu': list_menu,
                'branch': branch
            };

            $.ajax({
                url: 'utility/SinkronMaster/sinkronList',
                data: {
                    'params': params
                },
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function() {},
                success: function(data) {
                    if ( data.status == 1 ) {
                        sm.sinkron( data.content );
                    } else {
                        bootbox.alert();
                    }
                }
            });
        } else {
            bootbox.alert( 'Branch atau fitur belum anda pilih, harap cek kembali sebelum proses sinkron.' );
        }
    }, // end - sinkron

    sinkron: function(_data) {
        var idx = _data.idx;
        var jml_menu = _data.jml_menu;
        var branch = _data.branch;
        var keterangan = _data.data.keterangan;
        var table = _data.data.table;

        var params = {
            'idx': idx,
            'jml_menu': jml_menu,
            'branch': branch,
            'table': table
        };

        $.ajax({
            url: 'utility/SinkronMaster/sinkron',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(keterangan+' . . .'); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    if ( data.content.next == 1 ) {
                        sm.sinkronList( data.content.new_idx );
                    } else {
                        bootbox.alert(data.message, function() {
                            location.reload();
                        });
                    }
                } else {
                    bootbox.alert();
                }
            }
        });
    }, // end - sinkron
};

sm.startUp();