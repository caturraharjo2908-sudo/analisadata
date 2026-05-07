datatransaksi();

$('#selectperiode').on('change', function () {
    datatransaksi();
});


function datatransaksi(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url     : url + "index.php/penunjang/workloadrad/datatransaksi",
        method  : "POST",
        dataType: "JSON",
        data    : { selectperiode: selectperiode },

        beforeSend: function () {
            Swal.fire({
                title: 'Processing',
                html : 'Please wait while loading data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
        },

        success: function (data) {
            if (data.responCode !== "00") {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'No outpatient visit data found.'
                });
                return;
            }

            const result = data.responResult || [];

            // filter data valid
            const clean   = result.filter(x => x.NAMADOKTER && x.JAM);
            const doctors = [...new Set(clean.map(x => x.NAMADOKTER))];
            const hours   = [...new Set(clean.map(x => x.JAM))].filter(h => h !== null && h !== "").sort();

            if (!doctors.length || !hours.length) {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'No outpatient visit data found.'
                });
                return;
            }

            const series = doctors.map(doc => {
                return {
                    name: doc,
                    data: hours.map(h => {
                        const found = clean.find(x => x.NAMADOKTER === doc && x.JAM === h);

                        return {
                            x: h,
                            y: found && !isNaN(found.AVG_PASIEN) ? parseFloat(found.AVG_PASIEN) : 0
                        };
                    })
                };
            });

            const options = {
                chart: {
                    type   : 'heatmap',
                    height : 350,
                    toolbar: { show: false }
                },

                series: series,

                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#000']
                    },
                    formatter: function (val) {
                        return val ? val.toFixed(1) : '';
                    }
                },

                plotOptions: {
                    heatmap: {
                        shadeIntensity: 0.5,
                        radius: 0,
                        useFillColorAsStroke: false,
                        colorScale: {
                            ranges: [
                                {
                                    from : 0,
                                    to   : 0,
                                    name : 'Kosong',
                                    color: '#ffffff'
                                },
                                {
                                    from : 0.01,
                                    to   : 5,
                                    name : 'Rendah',
                                    color: '#00e396'  // hijau
                                },
                                {
                                    from : 5.01,
                                    to   : 10,
                                    name : 'Sedang',
                                    color: '#feb019'  // kuning
                                },
                                {
                                    from : 10.01,
                                    to   : 15,
                                    name : 'Tinggi',
                                    color: '#ff9800'  // orange
                                },
                                {
                                    from : 15.01,
                                    to   : 100,
                                    name : 'Sangat Tinggi',
                                    color: '#ff4560'         // merah
                                }
                            ]
                        }
                    }
                },

                xaxis: {
                    title: { text: "Jam" }
                },

                yaxis: {
                    title: { text: "Dokter" },
                    labels: {
                        maxWidth: 500   // 🔥 ini yang bikin lebar
                    }
                },
            };

            if (window.heatmapChart) {
                window.heatmapChart.destroy();
            }

            const el = document.querySelector("#grafikpeakloaddokter");

            if (!el) {
                console.error("Element grafik tidak ditemukan");
                return;
            }

            window.heatmapChart = new ApexCharts(el, options);
            window.heatmapChart.render();
        },

        complete: function () {
            Swal.close();
        },

        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load data'
            });
        }
    });
}