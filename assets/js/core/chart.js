const chartInstances = {};

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
                return `${label}: ${val.toLocaleString('id-ID')}`;
            },
            style: {
                colors: [colorLabel],
                fontSize: '11px'
            }
        },

        tooltip: {
            y: {
                formatter: function(val, opts) {
                    const kategori = data[opts.dataPointIndex][categoryField];
                    const value = data[opts.dataPointIndex][valueField];
                    // return `${kategori}: ${value.toLocaleString('id-ID')}`;
                    return 'Qty : '+ value.toLocaleString('id-ID');
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

function renderchartarea(name, data, titleX, titleY, seriesName, fieldName, slaValue, slaLabel, rightAxisIndex = null, rightAxisLabel = "") {

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

    const options = {
        chart: { type: "area", height: 350, toolbar: { show: false }, zoom: { enabled: false } },
        series: series,
        xaxis: {
            categories   : data.map(item => item.periode),
            title        : { text: titleX },
            tickPlacement: 'on',
            axisBorder   : { show: true },
            axisTicks    : { show: true }
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
        annotations: slaValue ? {
            yaxis: [{
                y: slaValue,
                borderColor: '#FF0000',
                strokeDashArray: 3,
                label: { text: slaLabel, style: { background: '#FF0000', color: '#fff' } }
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