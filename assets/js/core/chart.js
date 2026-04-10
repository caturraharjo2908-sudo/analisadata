const chartInstances = {};

function destroyAllCharts() {
    Object.keys(chartInstances).forEach(key => {
        if (chartInstances[key]) {
            chartInstances[key].destroy();
            chartInstances[key] = null;
        }
    });
}

function renderBarHorizontal(name, nameseries, data, categoryField = 'kategori', valueField = 'qty', colorLabel = '#ffffff') {
    // Hapus chart lama jika sudah ada
    if (chartInstances[name]) {
        chartInstances[name].destroy();
        chartInstances[name] = null;
    }

    // Ambil kategori dan nilai dari data
    const categories = data.map(item => item[categoryField]);
    const values     = data.map(item => item[valueField]);

    // Konfigurasi chart
    const options = {
        series: [{
            name: nameseries,
            data: values
        }],

        chart: {
            type: 'bar',
            height: Math.max(350, data.length * 30), // otomatis tinggi chart sesuai jumlah data
            toolbar: { show: false }
        },

        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 3,
                barHeight: '70%',
                dataLabels: { position: 'bottom' }
            }
        },

        xaxis: {
            categories,
            min: 0,
            type: 'logarithmic', // gunakan log scale agar bar kecil tetap terlihat
            labels: {
                formatter: val => Math.round(val).toLocaleString('id-ID')
            }
        },

        yaxis: {
            labels: { show: false } // bisa diubah sesuai kebutuhan
        },

        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            offsetX: 10,
            formatter: function(val, opts) {
                const label = opts.w.globals.labels[opts.dataPointIndex];
                return `${label} : ${val.toLocaleString('id-ID')}`;
            },
            style: {
                colors: [colorLabel],
                fontSize: '11px'
            }
        },

        tooltip: {
            y: {
                formatter: function(val, opts) {
                    const value = data[opts.dataPointIndex][valueField];
                    return nameseries+' : '+ value.toLocaleString('id-ID');
                },
                title: { formatter: () => '' }
            }
        }
    };

    // Render chart
    const chartEl = document.querySelector(`#${name}`);
    chartEl.innerHTML = ''; // bersihkan element sebelum render
    chartInstances[name] = new ApexCharts(chartEl, options);
    chartInstances[name].render();
}

function renderchartarea(name, data, titleX, titleY, seriesName, fieldName, rightAxisIndex = null, rightAxisLabel = "", avgField = null, avgLabel = "Rata-rata", annotationValue = null) {

    if (chartInstances[name]) {
        chartInstances[name].destroy();
        chartInstances[name] = null;
    }

    let series = [];

    if (Array.isArray(seriesName) && Array.isArray(fieldName)) {
        series = seriesName.map((nm, index) => ({
            name: nm,
            data: data.map(item => parseFloat(item[fieldName[index]] || 0)),
            yAxisIndex: (rightAxisIndex !== null && index === rightAxisIndex) ? 1 : 0
        }));
    } else {
        series = [{
            name: seriesName,
            data: data.map(item => parseFloat(item[fieldName] || 0))
        }];
    }

    // ? HITUNG AVG
    let avgValue = null;

    if (avgField) {
        let total = 0;
        let count = 0;

        data.forEach(item => {
            let val = parseFloat(item[avgField]);
            if (!isNaN(val) && val > 0) {
                total += val;
                count++;
            }
        });

        avgValue = count > 0 ? total / count : 0;
    }

    // ? PRIORITAS: annotationValue > avgValue
    let finalAnnotation = null;
    let finalLabel = "";

    if (annotationValue !== null) {
        finalAnnotation = annotationValue;
        finalLabel = avgLabel; // label bebas (misal: "SLA")
    } else if (avgValue !== null) {
        finalAnnotation = avgValue;
        finalLabel = `${avgLabel} (${Math.round(avgValue).toLocaleString()})`;
    }

    const options = {
        chart: { type: "area", height: 350, toolbar: { show: false }, zoom: { enabled: false } },
        series: series,
        xaxis: {
            categories: data.map(item => item.periode),
            title: { text: titleX },
            tickPlacement: 'on',
            axisBorder: { show: true },
            axisTicks: { show: true }
        },
        yaxis: rightAxisIndex !== null ? [
            {
                title: { text: titleY },
                min: 0,
                forceNiceScale: true,
                labels: { formatter: val => val.toLocaleString() }
            },
            {
                opposite: true,
                title: { text: rightAxisLabel },
                labels: { formatter: val => val.toLocaleString() }
            }
        ] : {
            title: { text: titleY },
            min: 0,
            forceNiceScale: true,
            labels: { formatter: val => val.toLocaleString() }
        },
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.75, opacityTo: 0.25, stops: [0, 100] }
        },
        dataLabels: { enabled: true, formatter: val => val.toLocaleString() },
        tooltip: { y: { formatter: val => val.toLocaleString() } },
        grid: { strokeDashArray: 4 },
        legend: { position: 'top' },

        // ? ANNOTATION FLEXIBLE
        annotations: finalAnnotation !== null ? {
            yaxis: [{
                y: finalAnnotation,
                borderColor: '#FF0000',
                strokeDashArray: 3,
                label: {
                    text: finalLabel,
                    style: { background: '#FF0000', color: '#fff' }
                }
            }]
        } : {}
    };

    const chartEl = document.querySelector(`#${name}`);
    chartEl.innerHTML = "";
    chartInstances[name] = new ApexCharts(chartEl, options);
    chartInstances[name].render();
}

function renderchartbar(name, data, seriesConfig, titleX, titleY, showLegend = false) {
    // Jika chart dengan ID ini sudah ada, destroy dulu
    if (chartInstances[name]) {
        chartInstances[name].destroy();
        chartInstances[name] = null;
    }

    // Membuat series untuk chart dari konfigurasi
    let series = seriesConfig.map(cfg => ({
        name: cfg.name,
        data: data.map(item => item[cfg.field] || 0)
    }));

    // Opsi chart ApexCharts
    let options = {
        chart: {
            type: "bar",
            height: 350,
            stacked: true,
            stackType: "100%",
            toolbar: { show: false },
            zoom: { enabled: false }
        },

        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '85%'
            }
        },

        series: series,

        xaxis: {
            categories: data.map(item => item.periode),
            title: { text: titleX }
        },

        yaxis: {
            title: { text: titleY },
            min: 0,
            max: 100,
            labels: {
                formatter: val => val.toFixed(0) + "%"
            }
        },

        dataLabels: {
            enabled: true,
            formatter: val => val.toFixed(0) + "%"
        },

        tooltip: {
            y: {
                formatter: val => val.toLocaleString()
            }
        },

        legend: {
            show: showLegend
        }
    };

    // Bersihkan container sebelum render chart
    const chartContainer = document.querySelector(`#${name}`);
    chartContainer.innerHTML = "";

    // Render chart dan simpan instance
    chartInstances[name] = new ApexCharts(chartContainer, options);
    chartInstances[name].render();
}

function renderchartpie(name, data) {
    // Jika chart dengan ID ini sudah ada, destroy dulu
    if (chartInstances[name]) {
        chartInstances[name].destroy();
        chartInstances[name] = null;
    }

    // Siapkan labels dan series
    let labels = [];
    let series = [];

    data.forEach(item => {
        labels.push(item.LABEL);
        series.push(parseInt(item.TOTAL) || 0);
    });

    // Opsi chart ApexCharts
    let options = {
        chart: {
            type: "donut",
            height: 350
        },

        series: series,
        labels: labels,

        legend: {
            position: "bottom"
        },

        dataLabels: {
            enabled: true,
            formatter: val => val.toFixed(1) + "%"
        },

        tooltip: {
            y: {
                formatter: val => val.toLocaleString("id-ID") + " Pasien"
            }
        }
    };

    // Bersihkan container sebelum render chart
    const chartContainer = document.querySelector(`#${name}`);
    chartContainer.innerHTML = "";

    // Render chart dan simpan instance
    chartInstances[name] = new ApexCharts(chartContainer, options);
    chartInstances[name].render();
}

function renderPyramidChart(name, data = [], titleY = "Kategori", seriesConfig = [], categoryField = "RANGE_UMUR") {

    // destroy chart lama
    if (chartInstances[name]) {
        chartInstances[name].destroy();
        chartInstances[name] = null;
    }

    const el = document.querySelector(`#${name}`);
    if (!el) return;

    if (!data.length) {
        el.innerHTML = "<div class='text-center text-muted'>No data available</div>";
        return;
    }

    // kategori (Y-axis)
    const categories = data.map(item => item[categoryField] || "-");

    // helper format angka
    const formatNumber = (val) => Math.abs(val).toLocaleString('id-ID');

    // build series
    const series = seriesConfig.map(cfg => ({
        name: cfg.name,
        data: data.map(item => {
            let value = parseFloat(item[cfg.field]) || 0;
            return cfg.negative ? -value : value;
        })
    }));

    // 🔥 cari max value (biar simetris kiri-kanan)
    let allValues = [];
    series.forEach(s => allValues = allValues.concat(s.data));

    const maxVal = Math.max(...allValues.map(v => Math.abs(v))) || 0;

    const options = {
        chart: {
            type: 'bar',
            height: 500,
            stacked: true,
            toolbar: { show: false }
        },

        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '80%',
                borderRadius: 6,
                borderRadiusApplication: 'end',
                borderRadiusWhenStacked: 'all'
            }
        },

        series: series,

        xaxis: {
            categories: categories,
            min: -maxVal,
            max: maxVal,
            tickAmount: 6,
            labels: {
                formatter: function (val) {
                    val = Math.abs(val);

                    // 🔥 HILANGKAN AREA TENGAH
                    if (val < (maxVal * 0.15)) return "";

                    if (val >= 1000000) return (val / 1000000).toFixed(1) + "M";
                    if (val >= 1000) return (val / 1000).toFixed(0) + "K";

                    return val;
                }
            }
        },

        yaxis: {
            title: { text: titleY }
        },

        dataLabels: {
            enabled: true,
            formatter: formatNumber
        },

        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: val => `${formatNumber(val)} Pasien`
            }
        },

        stroke: {
            width: 1,
            colors: ['#fff']
        },

        legend: {
            position: 'bottom'
        },

        colors: seriesConfig.map(cfg => cfg.color || undefined),

        // 🔥 garis tengah (center line)
        annotations: {
            xaxis: [{
                x: 0,
                borderColor: '#999',
                strokeDashArray: 3
            }]
        }
    };

    el.innerHTML = "";
    chartInstances[name] = new ApexCharts(el, options);
    chartInstances[name].render();
}