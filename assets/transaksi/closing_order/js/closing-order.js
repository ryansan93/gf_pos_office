var co = {
    startUp: function() {
        co.settingUp();
    }, // end - startUp

    settingUp: function() {
        $("#Tanggal").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $('select.branch').select2({placeholder: '-- Pilih Branch --'});
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
                'tanggal': dateSQL( $('#Tanggal').data('DateTimePicker').date() ),
                'branch': $('.branch').select2().val()
            };

            $.ajax({
                url : 'transaksi/ClosingOrder/getLists',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ showLoading(); },
                success : function(html){
                    hideLoading();
                    $('table tbody').html(html);
                },
            });
        }
    }, // end - getLists

    delete: function(elm) {
        bootbox.confirm('Apakah anda yakin ingin menghapus data Closing Order ?', function (result) {
            if ( result ) {
                var kode = $(elm).attr('data-id');

                var params = {
                    'kode': kode
                };

                $.ajax({
                    url : 'transaksi/ClosingOrder/delete',
                    data : {
                        'params' : params
                    },
                    type : 'POST',
                    dataType : 'JSON',
                    beforeSend : function(){ showLoading(); },
                    success : function(data){
                        hideLoading();
                        if ( data.status == 1 ) {
                            bootbox.alert(data.message, function() {
                                co.getLists();
                            });
                        } else {
                            bootbox.alert(data.message);
                        }
                    },
                });
            }
        });
    }, // end - delete
};

co.startUp();