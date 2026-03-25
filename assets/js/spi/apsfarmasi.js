rawdataapsfarmasi();

$('#selectperiode').on('change', function () {
    rawdataapsfarmasi();
});

$(document).on("keyup", "#fieldsearch", function () {
    filterTableByKeywords("#fieldsearch", "#resultdataapsfarmasi");
});

function rawdataapsfarmasi(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url +"index.php/spi/apsfarmasi/rawdataapsfarmasi",
        data      : {selectperiode:selectperiode},
        type      : "POST",
        dataType  : "JSON",
        beforeSend: function () {
            Swal.fire({
                title            : 'Processing',
                html             : 'Please wait while the system displays the requested data.',
                allowOutsideClick: false,
                allowEscapeKey   : false,
                showConfirmButton: false,
                didOpen          : () => Swal.showLoading()
            });

            $("#resultdataapsfarmasi").html("");
        },
        success:function(data){
            if (data.responCode !== "00") {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'No outpatient visit data found.'
                });
                return;
            }

            var   tableresult       = "";
            const result            = data.responResult || [];
            const GolObat           = getDataGolongan(result);
            const topObat           = getTopObat(result, 20);

            const dataChartKunjungan = aggregateFlexible(result, "TGLMASUK");

            let dataChartGolobat = GolObat.categories.map((cat, i) => ({
                kategori: cat,
                qty     : GolObat.values[i]
            }));

            const dataChartObat = topObat.map(item => ({
                kategori: item.nama,
                qty     : item.qty
            }));

            

            renderBarHorizontal('grafikgolonganobat', 'QTY Obat', dataChartGolobat, 'kategori', 'qty', true);
            renderBarHorizontal('grafikobat', 'QTY Obat', dataChartObat, 'kategori', 'qty', true);

            renderchartarea(
                "grafikpembelianobat",  // id div chart
                dataChartKunjungan,          // data
                "Periode Kunjungan",                // title X-axis
                "Jumlah Transaksi",        // title Y-axis
                "Transaksi",            // seriesName
                "value",                // fieldName
                null,                   // slaValue
                ""                      // slaLabel
            );
            
            if(data.responCode === "00"){
                for (var i in result) {
                    
                    let obat = parseObat(result[i].OBAT);

                    tableresult += "<tr>";
                    tableresult += "<td class='ps-4'>" + (parseInt(i) + 1) + "</td>";
                    tableresult += "<td>" + result[i].MRPAS + "</td>";
                    tableresult += "<td>" + (result[i].NIKKARYAWAN || '') + "</td>";
                    tableresult += "<td>" + result[i].NAMAPAS + "</td>";
                    tableresult += "<td>" + result[i].TGLMASUK + "</td>";
                    tableresult += "<td>" + result[i].POLIKLINIK + "</td>";
                    tableresult += "<td>" + result[i].NAMADOKTER + "</td>";
                    tableresult += "<td>" + result[i].PROVIDER + "</td>";
                    tableresult += "<td>"+obat.nama+"</td>";
                    tableresult += "<td>"+obat.qty+"</td>";
                    tableresult += "<td>"+obat.golongan+"</td>";
                    tableresult += "<td class='text-end pe-4'></td>";
                    tableresult += "</tr>";
                }
            }

            $("#resultdataapsfarmasi").html(tableresult);

        },
        complete: function () {
            Swal.close();
        },
        error: function () {
            Swal.fire({
                icon : 'error',
                title: 'Error',
                text : 'Unable to retrieve visit data.'
            });
        }
    });
};

function parseObat(obat){
    if(!obat) return {nama:'', golongan:''};

    let namaList = [];
    let QtyList  = [];
    let golList  = [];

    obat.split(";").forEach(item => {
        if(!item.trim()) return;

        let [nama, qty, gol, golongan] = item.split(":");

        let color = getGolonganColor(gol);

        // Nama
        namaList.push(`<div class="mb-1">${nama}</div>`);

        // Golongan dengan warna
        golList.push(`
            <div class="mb-1">
                <span class="badge badge-light-${color} small">
                    ${golongan || 'UNCLASSIFIED'}
                </span>
            </div>
        `);
        
        QtyList.push(`<div class="mb-1">${qty}</div>`);
    });

    return {
        nama    : namaList.join(""),
        golongan: golList.join(""),
        qty     : QtyList.join("")
    };
}

function getGolonganColor(kode){
    switch(kode){
        case 'B':  return 'success';
        case 'BT': return 'info';
        case 'K':  return 'danger';
        case 'N':  return 'danger';
        case 'P':  return 'dark';
        case 'V':  return 'primary';
        case 'PR': return 'danger';
        default:   return 'info';
    }
}

function getDataGolongan(result){
    let map = {};

    result.forEach(row => {
        if(!row.OBAT) return;

        row.OBAT.split(";").forEach(item => {
            if(!item.trim()) return;

            let [nama, qty, gol, golongan] = item.split(":");
            let key = (golongan || 'UNCLASSIFIED').toUpperCase().trim();

            // 🔥 buang data aneh
            if(key.length <= 2) return;

            let jumlah = parseInt(qty);
            if(isNaN(jumlah)) jumlah = 0;

            if(!map[key]){
                map[key] = 0;
            }

            map[key] += jumlah;
        });
    });

    // 🔹 Sort descending berdasarkan jumlah
    const sorted = Object.entries(map).sort((a, b) => b[1] - a[1]); // b[1] - a[1] = descending

    // 🔹 Pisahkan kembali ke categories & values
    const categories = sorted.map(item => item[0]);
    const values     = sorted.map(item => item[1]);

    return {
        categories,
        values
    };
}

function getTopObat(result, limit = 20) {
    const map = {};

    result.forEach(row => {
        if (!row.OBAT) return;

        row.OBAT.split(";").forEach(item => {
            if (!item.trim()) return;

            let [nama, qty, gol, golongan] = item.split(":");
            nama = (nama+" [ "+(golongan || 'UNCLASSIFIED')+" ]" || 'UNKNOWN').trim();
            let jumlah = parseInt(qty);
            if (isNaN(jumlah)) jumlah = 0;

            if (!map[nama]) map[nama] = 0;
            map[nama] += jumlah;
        });
    });

    // Konversi ke array dan sort descending
    const sorted = Object.entries(map).map(([nama, qty]) => ({ nama, qty })).sort((a, b) => b.qty - a.qty).slice(0, limit); // ambil top N
    return sorted;
}
