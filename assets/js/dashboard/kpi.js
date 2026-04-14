loaddata();

$('#selectperiode').on('change', function () {
    loaddata();
});

function loaddata(){
    destroyAllCharts();

    dataoperasielektif();
    datajampulangpasien();
};

function dataoperasielektif(){
    let selectperiode = $("select[name='selectperiode']").val();

    $.ajax({
        url      : url + "index.php/dashboard/kpi/dataoperasielektif",
        type     : "POST",
        dataType : "JSON",
        data     : { selectperiode: selectperiode },

        beforeSend: function () {
            Swal.fire({
                title: 'Processing',
                html : 'Please wait while the system displays the requested data.',
                allowOutsideClick: false,
                allowEscapeKey   : false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
        },

        success: function (response) {

            if (response.responCode !== "00") {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'No outpatient visit data found.'
                });
                return;
            }

            const result       = response.responResult || [];
            const bulanLengkap = ["01","02","03","04","05","06","07","08","09","10","11","12"];
            const namaBulan    = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];

            const dataMapKunjungan = {};

            result.forEach(item => {
                dataMapKunjungan[item.PERIODE] = item.PERSENTASI;
            });


            const chartDataKunjungan = bulanLengkap.map((b, index) => ({
                periode   : namaBulan[index],
                totalValue: dataMapKunjungan[b] ?? 0
            }));


            renderchartarea("grafikkpioperasi",chartDataKunjungan,"Periode Pelayanan","Presentasi Pembatalan",["Presentasi"],["totalValue"],null,"","totalValue","Rata-rata Pembatalan",null);
        },

        complete: function () {
            Swal.close();
        },

        error: function () {
            Swal.fire({
                icon : 'error',
                title: 'System Error',
                text : 'Failed to retrieve emergency visit data.'
            });
        }
    });
};

function datajampulangpasien(){
    let selectperiode = $("select[name='selectperiode']").val();

    $.ajax({
        url      : url + "index.php/dashboard/kpi/datajampulangpasien",
        type     : "POST",
        dataType : "JSON",
        data     : { selectperiode: selectperiode },

        beforeSend: function () {
            Swal.fire({
                title: 'Processing',
                html : 'Please wait while the system displays the requested data.',
                allowOutsideClick: false,
                allowEscapeKey   : false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
        },

        success: function (response) {

            if (response.responCode !== "00") {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'No outpatient visit data found.'
                });
                return;
            }

            const result       = response.responResult || [];
            const bulanLengkap = ["01","02","03","04","05","06","07","08","09","10","11","12"];
            const namaBulan    = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];

            const dataMapKunjungan = {};

            result.forEach(item => {
                dataMapKunjungan[item.PERIODE] = item.PERSENTASI;
            });


            const chartDataKunjungan = bulanLengkap.map((b, index) => ({
                periode   : namaBulan[index],
                totalValue: dataMapKunjungan[b] ?? 0
            }));


            renderchartarea("grafikkpipasienpulang",chartDataKunjungan,"Periode Pelayanan","Presentasi Pulang Di Bawah Pukul 12:00",["Presentasi"],["totalValue"],null,"","totalValue","Rata-rata Pulang Di Bawah Pukul 12:00",null);
        },

        complete: function () {
            Swal.close();
        },

        error: function () {
            Swal.fire({
                icon : 'error',
                title: 'System Error',
                text : 'Failed to retrieve emergency visit data.'
            });
        }
    });
};
