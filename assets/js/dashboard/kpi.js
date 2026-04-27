loaddata();

$('#selectperiode').on('change', function () {
    loaddata();
});

function loaddata(){
    destroyAllCharts();

    datawaktutunggurajal();
    dataoperasielektif();

    registranaptoranap();

    datajampulangpasienbln();
    datajampulangpasienblnruangan();
    datajampulangharian();
    datajampulangpasienharianruangan();

    datakeluarigd();
    datarawatjalan();
};

function datawaktutunggurajal(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/datawaktutunggurajal",
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

            const dataMap = {};

            result.forEach(item => {
                dataMap[item.BULAN] = {
                    val1: item.PCT_TOTAL,
                    val2: item.AVG_TOTAL,
                    val3: item.PCT_CHECKIN_ANAMNESA,
                    val4: item.AVG_CHECKIN_ANAMNESA,
                    val5: item.PCT_ANAMNESA,
                    val6: item.AVG_ANAMNESA,
                    val7: item.PCT_ANAMNESA_DOKTER,
                    val8: item.AVG_ANAMNESA_DOKTER
                };
            });


            const chartData = bulanLengkap.map((b, index) => ({
                periode: namaBulan[index],
                Value1 : dataMap[b]?.val1 ?? 0,
                Value2 : dataMap[b]?.val2 ?? 0,
                Value3 : dataMap[b]?.val3 ?? 0,
                Value4 : dataMap[b]?.val4 ?? 0,
                Value5 : dataMap[b]?.val5 ?? 0,
                Value6 : dataMap[b]?.val6 ?? 0,
                Value7 : dataMap[b]?.val7 ?? 0,
                Value8 : dataMap[b]?.val8 ?? 0
            }));


            renderchartarea("grafikkpiwaktutunggurajal",chartData,"Periode Pelayanan","% Waktu Tunggu RJ ≤ 60 Menit",["Presentasi","Avg Menit"],["Value1","Value2"],true,"Avg Waktu Tunggu RJ","Value1","Avg % Waktu Tunggu RJ ≤ 60 Menit",null);
            renderchartarea("grafikkpiwaktutunggurajalcheckinanam",chartData,"Periode Pelayanan","% Waktu Tunggu RJ ≤ 20 Menit",["Presentasi","Avg Menit"],["Value3","Value4"],true,"Avg Waktu Tunggu RJ","Value3","Avg % Waktu Tunggu RJ ≤ 20 Menit",null);
            renderchartarea("grafikkpiwaktutunggurajalanamnesa",chartData,"Periode Pelayanan","% Waktu Tunggu RJ ≤ 10 Menit",["Presentasi","Avg Menit"],["Value5","Value6"],true,"Avg Waktu Tunggu RJ","Value5","Avg % Waktu Tunggu RJ ≤ 10 Menit",null);
            renderchartarea("grafikkpiwaktutunggurajaldokter",chartData,"Periode Pelayanan","% Waktu Tunggu RJ ≤ 30 Menit",["Presentasi","Avg Menit"],["Value7","Value8"],true,"Avg Waktu Tunggu RJ","Value7","Avg % Waktu Tunggu RJ ≤ 30 Menit",null);
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

            const dataMap = {};

            result.forEach(item => {
                dataMap[item.BULAN] = {
                    val1: item.PERSENTASE_BATAL,
                    val2: item.BATAL
                };
            });


            const chartData = bulanLengkap.map((b, index) => ({
                periode: namaBulan[index],
                Value1 : dataMap[b]?.val1 ?? 0,
                Value2 : dataMap[b]?.val2 ?? 0
            }));


            renderchartarea("grafikkpioperasi",chartData,"Periode Pelayanan","Presentasi Pembatalan",["Presentasi","Jumlah Pasien"],["Value1","Value2"],true,"Jumlah Pasien","Value1","Avg % Pembatalan OK",null);
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

function datajampulangpasienbln(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/datajampulangpasienbln",
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

            const dataMap = {};

            result.forEach(item => {
                dataMap[item.BULAN] = {
                    val1: item.PERSENTASE,
                    val2: item.BIAYA_MAKAN
                };
            });


            const chartData = bulanLengkap.map((b, index) => ({
                periode: namaBulan[index],
                Value1 : dataMap[b]?.val1 ?? 0,
                Value2 : dataMap[b]?.val2 ?? 0
            }));

            renderchartarea("grafikkpipasienpulang",chartData,"Periode Pelayanan","% Pulang < Pukul 12:00",["Presentasi","Biaya Makan"],["Value1","Value2"],true,"Biaya Makan","Value1","Avg % Pulang < Pukul 12:00",null);
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

function datajampulangharian(){
    let selectperiode = $("select[name='selectperiode']").val();

    $.ajax({
        url      : url + "index.php/dashboard/kpi/datajampulangharian",
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

            const chartData = result.map(item => ({
                periode: item.PERIODE,
                Value1 : item.PERSENTASE ?? 0,
                Value2 : item.BIAYA_MAKAN ?? 0
            }));


            renderchartarea("grafikkpipasienpulangharian",chartData,"Periode Pelayanan","% Pulang < Pukul 12:00",["Presentasi","Biaya"],["Value1","Value2"],true,"Biaya Gizi","Value1","Avg % Pulang < Pukul 12:00",null);
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

function datajampulangpasienblnruangan(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/datajampulangpasienblnruangan",
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
                    text : 'No inpatient discharge data found.'
                });
                return;
            }

            const result = response.responResult || [];

            renderTreemapChart("grafikkpipasienpulangheatmap",result,"UNIT","SESUDAH_12");
        },

        complete: function () {
            Swal.close();
        },

        error: function () {
            Swal.fire({
                icon : 'error',
                title: 'System Error',
                text : 'Failed to retrieve KPI data.'
            });
        }
    });
};

function datajampulangpasienharianruangan(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/datajampulangpasienharianruangan",
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
                    text : 'No inpatient discharge data found.'
                });
                return;
            }

            const result = response.responResult || [];

            renderTreemapChart("grafikkpipasienpulangharianheatmap",result,"UNIT","SESUDAH_12");
        },

        complete: function () {
            Swal.close();
        },

        error: function () {
            Swal.fire({
                icon : 'error',
                title: 'System Error',
                text : 'Failed to retrieve KPI data.'
            });
        }
    });
};

function registranaptoranap(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/registranaptoranap",
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

            const dataMap = {};

            result.forEach(item => {
                dataMap[item.BULAN] = {
                    val1: item.MASUK_RANAP_KURANG_60_MENIT_PERSENTASE,
                    val2: item.AVG_MENIT_MASUK_RANAP,
                };
            });


            const chartData = bulanLengkap.map((b, index) => ({
                periode: namaBulan[index],
                Value1 : dataMap[b]?.val1 ?? 0,
                Value2 : dataMap[b]?.val2 ?? 0
            }));


            renderchartarea("grafikkpiwaktumasukranap",chartData,"Periode Pelayanan","% Masuk Ranap ≤ 60 menit",["Presentasi","Avg Menit"],["Value1","Value2"],true,"Avg Waktu Tunggu Masuk Ranap","Value1","Rata-rata Masuk Rana",null);
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

function datakeluarigd(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/datakeluarigd",
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

            const dataMap = {};

            result.forEach(item => {
                dataMap[item.BULAN] = {
                    val1: item.KELUAR_IGD_KURANG_240_MENIT_PERSENTASE,
                    val2: item.AVG_MENIT_KELUAR_IGD,
                };
            });


            const chartData = bulanLengkap.map((b, index) => ({
                periode: namaBulan[index],
                Value1 : dataMap[b]?.val1 ?? 0,
                Value2 : dataMap[b]?.val2 ?? 0
            }));


            renderchartarea("grafikkpikeluarigd",chartData,"Periode Pelayanan","% Keluar IGD ≤ 4 Jam",["Presentasi","Avg Menit"],["Value1","Value2"],true,"Avg Waktu Tunggu Masuk Ranap","Value1","Rata-rata Keluar IGD",null);
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

function datarawatjalan(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/datarawatjalan",
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

            const dataMap = {};

            result.forEach(item => {
                dataMap[item.BULAN] = {
                    val1: item.RJ_SELESAI_120_MENIT_PERSENTASE,
                    val2: item.AVG_RJ_SELESAI,
                };
            });


            const chartData = bulanLengkap.map((b, index) => ({
                periode: namaBulan[index],
                Value1 : dataMap[b]?.val1 ?? 0,
                Value2 : dataMap[b]?.val2 ?? 0
            }));


            renderchartarea("grafikkpirj",chartData,"Periode Pelayanan","% Selesai Rawat Jalan ≤ 10 Menit",["Presentasi","Avg Menit"],["Value1","Value2"],true,"Avg Waktu Tunggu Masuk Ranap","Value1","Rata-rata Selesai Rawat Jalan",null);
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