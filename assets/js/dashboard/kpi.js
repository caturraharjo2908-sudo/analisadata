loaddata();

$('#selectperiode').on('change', function () {
    loaddata();
});

function loaddata(){
    destroyAllCharts();

    dataoperasielektif();
    datajampulangpasien();
    datajampulangharian();
    dataheatmap();
    dataheatmapharian();
    registranaptoranap();
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
                dataMapKunjungan[item.PERIODE] = {
                    persentasi : item.PERSENTASI ?? 0,
                    totalBiaya : item.TOTAL_BIAYA ?? 0
                };
            });


            const chartDataKunjungan = bulanLengkap.map((b, index) => ({
                periode     : namaBulan[index],
                totalValue  : dataMapKunjungan[b]?.persentasi ?? 0,
                totalBiaya  : dataMapKunjungan[b]?.totalBiaya ?? 0
            }));


            renderchartarea("grafikkpipasienpulang",chartDataKunjungan,"Periode Pelayanan","Presentasi Pulang Di Bawah Pukul 12:00",["Presentasi","Biaya"],["totalValue","totalBiaya"],true,"Biaya Gizi","totalValue","Rata-rata Pulang Di Bawah Pukul 12:00",null);
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

            const dataMapKunjungan = {};

            result.forEach(item => {
                dataMapKunjungan[item.PERIODE] = item.PERSENTASI;
            });


            const chartDataKunjungan = result.map(item => ({
                periode   : item.PERIODE,
                totalValue: item.PERSENTASI ?? 0,
                totalBiaya: item.TOTAL_BIAYA ?? 0
            }));


            renderchartarea("grafikkpipasienpulangharian",chartDataKunjungan,"Periode Pelayanan","Presentasi Pulang < Pukul 12:00",["Presentasi","Biaya"],["totalValue","totalBiaya"],true,"Biaya Gizi","totalValue","Rata-rata Pulang Di Bawah Pukul 12:00",null);
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

function dataheatmapharian(){

    $.ajax({
        url      : url + "index.php/dashboard/kpi/dataheatmapharian",
        type     : "POST",
        dataType : "JSON",

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

            if(result.length === 0){
                Swal.fire({
                    icon : 'warning',
                    title: 'Empty Data',
                    text : 'No data returned from server.'
                });
                return;
            }

            // =========================
            // 🌳 TREEMAP FULL DATA
            // =========================

            // sorting (optional, biar yang besar duluan)
            const sortedUnit = [...result].sort((a, b) => 
                (b.SESUDAH_12 ?? 0) - (a.SESUDAH_12 ?? 0)
            );

            // ❗ TIDAK pakai slice → tampil semua
            const allUnit = sortedUnit;

            const totalAll = allUnit.reduce((a,b)=>a+(b.SESUDAH_12 ?? 0),0);

            const treemapSeries = [{
                data: allUnit.map(item => ({
                    x: item.UNIT || "Unknown",
                    y: item.SESUDAH_12 ?? 0
                }))
            }];

            const optionsTreemap = {
                chart: {
                    type: 'treemap',
                    height: 350,
                    toolbar: { show: false }
                },
                series: treemapSeries,
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '10px'
                    },
                    formatter: function(text, op) {
                        const val = op.value;
                        const persen = totalAll ? ((val/totalAll)*100).toFixed(1) : 0;
                        return text + "\n" + val;
                    }
                },
                plotOptions: {
                    treemap: {
                        distributed: false,
                        enableShades: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " pasien";
                        }
                    }
                }
            };

            if (window.chartHeatmapHarian ) {
                window.chartHeatmapHarian .destroy();
            }

            window.chartHeatmapHarian  = new ApexCharts(
                document.querySelector("#grafikkpipasienpulangharianheatmap"),
                optionsTreemap
            );

            window.chartHeatmapHarian .render();
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

function dataheatmap(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/kpi/dataheatmap",
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

            if(result.length === 0){
                Swal.fire({
                    icon : 'warning',
                    title: 'Empty Data',
                    text : 'No data returned from server.'
                });
                return;
            }

            // =========================
            // 🌳 TREEMAP FULL DATA
            // =========================

            // sorting (optional, biar yang besar duluan)
            const sortedUnit = [...result].sort((a, b) => 
                (b.SESUDAH_12 ?? 0) - (a.SESUDAH_12 ?? 0)
            );

            // ❗ TIDAK pakai slice → tampil semua
            const allUnit = sortedUnit;

            const totalAll = allUnit.reduce((a,b)=>a+(b.SESUDAH_12 ?? 0),0);

            const treemapSeries = [{
                data: allUnit.map(item => ({
                    x: item.UNIT || "Unknown",
                    y: item.SESUDAH_12 ?? 0
                }))
            }];

            const optionsTreemap = {
                chart: {
                    type: 'treemap',
                    height: 350,
                    toolbar: { show: false }
                },
                series: treemapSeries,
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '10px'
                    },
                    formatter: function(text, op) {
                        const val = op.value;
                        const persen = totalAll ? ((val/totalAll)*100).toFixed(1) : 0;
                        return text + "\n" + val;
                    }
                },
                plotOptions: {
                    treemap: {
                        distributed: false,
                        enableShades: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " pasien";
                        }
                    }
                }
            };

            if (window.chartHeatmapBulanan) {
                window.chartHeatmapBulanan.destroy();
            }

            window.chartHeatmapBulanan = new ApexCharts(
                document.querySelector("#grafikkpipasienpulangheatmap"),
                optionsTreemap
            );

            window.chartHeatmapBulanan.render();
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

            const dataMapKunjungan = {};

            result.forEach(item => {
                dataMapKunjungan[item.PERIODE] = item.MASUK_RANAP_KURANG_60_MENIT_PERSENTASE;
            });


            const chartDataKunjungan = bulanLengkap.map((b, index) => ({
                periode   : namaBulan[index],
                totalValue: dataMapKunjungan[b] ?? 0
            }));


            renderchartarea("grafikkpiwaktumasukranap",chartDataKunjungan,"Periode Pelayanan","Presentasi Masuk Ranap ≤ 60 menit",["Presentasi"],["totalValue"],null,"","totalValue","Rata-rata Presentasi Masuk Ranap ≤ 60 menit",null);
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