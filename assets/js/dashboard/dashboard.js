// const today = new Date();
// startDate = today.toLocaleDateString('en-CA');
// endDate   = today.toLocaleDateString('en-CA');

// flatpickr('[name="dateperiode"]', {
//     mode: "range",
//     enableTime: false,
//     dateFormat: "d.m.Y",
//     maxDate: "today",
//     onChange: function (selectedDates, dateStr, instance) {
//         startDate = selectedDates[0] ? selectedDates[0].toLocaleDateString('en-CA') : null;
//         endDate   = selectedDates[1]  ? selectedDates[1].toLocaleDateString('en-CA') : null;
//     }
// });

// $(document).on("click", ".btn-apply", function (e) {
//     e.preventDefault();

//     if (!startDate || !endDate) {
//         toastr["warning"]("Please select a valid date range", "Warning");
//         return;
//     }

//     pasienmeninggal(startDate,endDate);
// });


// pasientransit();
// pasienmeninggal(startDate,endDate);



// function pasienmeninggal(startDate,endDate){
//     $.ajax({
//         url       : url +"index.php/dashboard/dashboard/pasienmeninggal",
//         data      : {startdate:startDate,endate:endDate},
//         type      : "POST",
//         dataType  : "JSON",
//         beforeSend: function () {
//             Swal.fire({
//                 title            : 'Processing',
//                 html             : 'Please wait while the system displays the requested data.',
//                 allowOutsideClick: false,
//                 allowEscapeKey   : false,
//                 showConfirmButton: false,
//                 didOpen          : () => Swal.showLoading()
//             });

//             $("#totalpasienmeninggal").html("0 Px");
//             $("#descpasienmeninggal").html("Loading");
//             $("#resultdatapasienmeninggal").html("");
//         },
//         success:function(data){
//             let   tableresult      = "";
//             const result           = data.responResult || [];

//             if(data.responCode==="00"){
//                 for(var i in result){
//                     const timerId = "timer_" + i;
//                     const sexLabel    = result[i].SEXID === 'L' ? 'Laki-laki' : result[i].SEXID === 'P' ? 'Perempuan' : '';

//                     tableresult +="<tr>";
//                     tableresult +="<td class='ps-4'>"+(parseInt(i)+1)+"</td>";
//                     tableresult +="<td class='text-end pe-4'><div>"+(result[i].MRPAS || "")+"</div><div>"+(result[i].NAMAPASIEN || "")+"</div></td>";
//                     tableresult +="</tr>"; 
//                 }
//             }

//             $("#totalpasienmeninggal").html(todesimal(result.length)+" Px");
//             $("#descpasienmeninggal").html(startDate+" - "+endDate);
//             $("#resultdatapasienmeninggal").html(tableresult);
            
//         },
//         complete: function () {
//             Swal.close();
//         },
//         error: function () {
//             Swal.fire({
//                 icon : 'error',
//                 title: 'Error',
//                 text : 'Unable to retrieve visit data.'
//             });
//         }
//     });
// };

let globalDataKunjunganIGD  = [];
let globalDataKunjunganRJ   = [];
let globalDataKunjunganRI   = [];
let globalDataProviderIGD   = [];
let globalDataProviderRJ    = [];
let globalDataProviderRI    = [];
let globalDataPendidikanIGD = [];
let globalDataPendidikanRJ  = [];
let globalDataPendidikanRI  = [];
let globalDataPoli          = [];

destroyAllCharts();
demografiumur();
pasientransit();
datakunjunganrj();
datakunjunganri();
datakunjunganigd();
datakunjunganigdprovider();
datakunjunganrjprovider();
datakunjunganriprovider();
pendidikanigd();
pendidikanrj();
pendidikanri();
top10poli();

$("#btnDownloadExcelKunjungan").on("click", function () {

    if (
        !globalDataKunjunganIGD.length &&
        !globalDataKunjunganRJ.length &&
        !globalDataKunjunganRI.length
    ) {
        Swal.fire('Info', 'Data belum tersedia', 'warning');
        return;
    }

    const workbook = XLSX.utils.book_new();

    // 🔥 IGD & RI
    function buildSheetKunjungan(data, sheetName) {
        if (!data.length) return;

        const dataExport = data.map((item, index) => ({
            No        : index + 1,
            Bulan     : item.BULAN,
            Kunjungan : parseInt(item.TOTAL_KUNJUNGAN) || 0
        }));

        const worksheet = XLSX.utils.json_to_sheet(dataExport);
        XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);
    }

    // 🔥 RAWAT JALAN EXECUTIVE
    function buildSheetRJExec(data, sheetName) {
        if (!data.length) return;

        const dataExport = data.map((item, index) => ({
            No        : index + 1,
            Bulan     : item.BULAN,
            Kunjungan : parseInt(item.KUNJUNGAN_EXECUTIVE) || 0
        }));

        const worksheet = XLSX.utils.json_to_sheet(dataExport);
        XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);
    }

    // 🔥 RAWAT JALAN NON EXECUTIVE
    function buildSheetRJNonExec(data, sheetName) {
        if (!data.length) return;

        const dataExport = data.map((item, index) => ({
            No        : index + 1,
            Bulan     : item.BULAN,
            Kunjungan : parseInt(item.KUNJUNGAN_NON_EXECUTIVE) || 0
        }));

        const worksheet = XLSX.utils.json_to_sheet(dataExport);
        XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);
    }

    // 🔥 IGD
    buildSheetKunjungan(globalDataKunjunganIGD, "IGD");

    // 🔥 RJ EXECUTIVE
    buildSheetRJExec(globalDataKunjunganRJ, "RJ Executive");

    // 🔥 RJ NON EXECUTIVE
    buildSheetRJNonExec(globalDataKunjunganRJ, "RJ Non Executive");

    // 🔥 RI
    buildSheetKunjungan(globalDataKunjunganRI, "Rawat Inap");

    // 🔥 Download

    XLSX.writeFile(workbook, "Kunjungan_Pasien.xlsx");
});

$("#btnDownloadExcelProviderIGD").on("click", function () {
    exportProviderToExcel(
        globalDataProviderIGD,
        "Provider IGD",
        "Provider_IGD.xlsx"
    );
});

$("#btnDownloadExcelProviderRJ").on("click", function () {
    exportProviderToExcel(
        globalDataProviderRJ,
        "Provider Rawat Jalan",
        "Provider_Rawat_Jalan.xlsx"
    );
});

$("#btnDownloadExcelProviderRI").on("click", function () {
    exportProviderToExcel(
        globalDataProviderRI,
        "Provider Rawat Inap",
        "Provider_Rawat_Inap.xlsx"
    );
});

$("#btnDownloadExcelPendidikanIGD").on("click", function () {
    exportProviderToExcel(
        globalDataPendidikanIGD,
        "Pendidikan IGD",
        "Pendidikan_IGD.xlsx"
    );
});

$("#btnDownloadExcelPendidikanRJ").on("click", function () {
    exportProviderToExcel(
        globalDataPendidikanRJ,
        "Pendidikan Rawat Jalan",
        "Pendidikan_Rawat_Jalan.xlsx"
    );
});

$("#btnDownloadExcelPendidikanRI").on("click", function () {
    exportProviderToExcel(
        globalDataPendidikanRI,
        "Pendidikan Rawat Inap",
        "Pendidikan_Rawat_Inap.xlsx"
    );
});

$("#btnDownloadExcelPoli").on("click", function () {
    exportProviderToExcel(
        globalDataPoli,
        "Poliklinik",
        "Poliklinik.xlsx"
    );
});

// 🔥 FUNCTION UNIVERSAL
function exportProviderToExcel(data, sheetName, fileName) {

    if (!data.length) {
        Swal.fire('Info', 'Data belum tersedia', 'warning');
        return;
    }

    const dataExport = data.map((item, index) => ({
        No        : index + 1,
        Keterangan: item.LABEL || item.PENDIDIKAN || item.KETERANGAN || '-',
        Total     : parseInt(item.TOTAL) || 0
    }));

    const worksheet = XLSX.utils.json_to_sheet(dataExport);
    const workbook  = XLSX.utils.book_new();

    XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);
    XLSX.writeFile(workbook, fileName);
}

$('#selectperiode').on('change', function () {
    destroyAllCharts();

    datakunjunganrj();
    datakunjunganri();
    datakunjunganigd();
    datakunjunganigdprovider();
    datakunjunganrjprovider();
    datakunjunganriprovider();

    pendidikanigd();
    pendidikanrj();
    pendidikanri();

    top10poli();
});

function pasientransit(){
    $.ajax({
        url       : url +"index.php/dashboard/dashboard/pasientransit",
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

            $("#totalpasientransit").html("0 Px");
            $("#resultdatapasientransit").html("");
        },
        success:function(data){
            let   tableresult      = "";
            const result           = data.responResult || [];

            if(data.responCode==="00"){
                for(var i in result){
                    const timerId = "timer_" + i;
                    const sexLabel    = result[i].SEXID === 'L' ? 'Laki-laki' : result[i].SEXID === 'P' ? 'Perempuan' : '';

                    tableresult +="<tr>";
                    tableresult +="<td class='ps-4'>"+(parseInt(i)+1)+"</td>";
                    tableresult +="<td><div>"+(result[i].MRPAS || "")+"</div><div>"+(result[i].NAMAPASIEN || "")+"</div></td>";
                    tableresult +="<td>"+sexLabel+"</td>";
                    tableresult +="<td class='text-end'><div>"+(result[i].TGLMASUKTRANSIT || "")+"</div><div><span class='badge fw-bold' id='" + timerId + "'>Loading...</span></div></div></td>";
                    tableresult +="</tr>"; 
                }
            }

            $("#resultdatapasientransit").html(tableresult);
            $("#totalpasientransit").html(todesimal(result.length)+" Px");

            for(var i in result){
                const timerId = "timer_" + i;
                setCountdownSLA(result[i].TGLMASUKTRANSIT, timerId, 6);
            }
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

function demografiumur(){
    $.ajax({
        url     : url + "index.php/dashboard/dashboard/demografiumur",
        type    : "POST",
        dataType: "JSON",
        cache   : false,

        beforeSend: function () {            
            Swal.fire({
                title            : 'Processing',
                html             : 'Please wait while the system displays the requested data...',
                allowOutsideClick: false,
                allowEscapeKey   : false,
                showConfirmButton: false,
                didOpen          : () => Swal.showLoading()
            });
        },

        success: function (data) {
            const result = data.responResult || [];
            renderPyramidChart(
                "grafikumur",
                result,
                "Kelompok Umur",
                [
                    {name: "Laki-laki",field: "LAKI_LAKI",negative: true,color: "#3b82f6"},
                    {name: "Perempuan",field: "PEREMPUAN",negative: false,color: "#ec4899"}
                ]
            );           
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

function datakunjunganigd(){
    let selectperiode = $("select[name='selectperiode']").val();

    $.ajax({
        url      : url + "index.php/dashboard/dashboard/datakunjunganigd",
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
                dataMapKunjungan[item.BULAN] = parseInt(item.TOTAL_KUNJUNGAN);
            });

            globalDataKunjunganIGD = result;

            const chartDataKunjungan = bulanLengkap.map((b, index) => ({
                periode   : namaBulan[index],
                totalValue: dataMapKunjungan[b] ?? 0
            }));

            renderchartarea("grafikkunjunganigd",chartDataKunjungan,"Periode Pelayanan","Jumlah Kunjungan",["Transaksi"],["totalValue"],null,"","totalValue","Rata-rata Kunjungan",null);
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

function datakunjunganrj(){
    let selectperiode = $("select[name='selectperiode']").val();

    $.ajax({
        url      : url + "index.php/dashboard/dashboard/datakunjunganrj",
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

            const mapExec    = {};
            const mapNonExec = {};

            result.forEach(item => {
                mapExec[item.BULAN]     = parseInt(item.KUNJUNGAN_EXECUTIVE);
                mapNonExec[item.BULAN]  = parseInt(item.KUNJUNGAN_NON_EXECUTIVE);
            });

            globalDataKunjunganRJ = result;

            const chartDataKunjungan = bulanLengkap.map((b, index) => ({
                periode     : namaBulan[index],
                executive   : mapExec[b] ?? 0,
                nonexecutive: mapNonExec[b] ?? 0
            }));

            // renderchartarea("grafikkunjunganrj",chartDataKunjungan,"Periode Pelayanan","Non Executive",["Non Executive", "Executive"],["nonexecutive", "executive"],1,"Executive","nonexecutive","Rata-rata Kunjungan Non Executive");
            renderchartarea("grafikkunjunganrj",chartDataKunjungan,"Periode Pelayanan","Jumlah Kunjungan",["Non Executive"],["nonexecutive"],null,"","nonexecutive","Rata-rata Kunjungan Non Executive",null);
            renderchartarea("grafikkunjunganexecutive",chartDataKunjungan,"Periode Pelayanan","Jumlah Kunjungan",["Executive"],["executive"],null,"","executive","Rata-rata Kunjungan Executive",null);

        },

        complete: function () {
            Swal.close();
        },

        error: function () {
            Swal.fire({
                icon : 'error',
                title: 'System Error',
                text : 'Failed to retrieve outpatient visit data.'
            });
        }
    });
}

function datakunjunganri(){
    let selectperiode = $("select[name='selectperiode']").val();

    $.ajax({
        url      : url + "index.php/dashboard/dashboard/datakunjunganri",
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
                dataMapKunjungan[item.BULAN] = parseInt(item.TOTAL_KUNJUNGAN);
            });

            globalDataKunjunganRI = result;

            const chartDataKunjungan = bulanLengkap.map((b, index) => ({
                periode   : namaBulan[index],
                totalValue: dataMapKunjungan[b] ?? 0
            }));

            renderchartarea("grafikkunjunganri",chartDataKunjungan,"Periode Pelayanan","Jumlah Kunjungan",["Transaksi"],["totalValue"],null,"","totalValue","Rata-rata Kunjungan",null);
        },

        complete: function () {
            Swal.close();
        },

        error: function () {
            Swal.fire({
                icon : 'error',
                title: 'System Error',
                text : 'Failed to retrieve inpatient visit data.'
            });
        }
    });
};

function datakunjunganigdprovider(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/dashboard/datakunjunganigdprovider",
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

            const result = response.responResult || [];
            globalDataProviderIGD = result;

            renderchartpie("grafikkunjunganigdprovider", result);
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

function datakunjunganrjprovider(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/dashboard/datakunjunganrjprovider",
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

            const result = response.responResult || [];
            globalDataProviderRJ = result;

            renderchartpie("grafikkunjunganrjprovider", result);
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

function datakunjunganriprovider(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/dashboard/datakunjunganriprovider",
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

            const result = response.responResult || [];
            globalDataProviderRI = result;

            renderchartpie("grafikkunjunganriprovider", result);
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

function pendidikanigd(){
    let selectperiode   = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url + "index.php/dashboard/dashboard/pendidikanigd",
        data      : {selectperiode:selectperiode},
        method    : "POST",
        dataType  : "JSON",
        cache     : false,
        beforeSend: function () {            
            Swal.fire({
                title            : 'Processing',
                html             : 'Please wait while the system displays the requested data.',
                allowOutsideClick: false,
                allowEscapeKey   : false,
                showConfirmButton: false,
                didOpen          : () => Swal.showLoading()
            });
        },
        success: function (data) {
            const result              = data.responResult || [];
            globalDataPendidikanIGD = result;

            const dataChartPendidikan = result.map(item => ({
                kategori: item.PENDIDIKAN,
                qty     : item.TOTAL
            }));

            renderBarHorizontal('grafikkunjunganigdpendidikan', 'Jumlah', dataChartPendidikan, 'kategori', 'qty', true);
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

function pendidikanrj(){
    let selectperiode   = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url + "index.php/dashboard/dashboard/pendidikanrj",
        data      : {selectperiode:selectperiode},
        method    : "POST",
        dataType  : "JSON",
        cache     : false,
        beforeSend: function () {            
            Swal.fire({
                title            : 'Processing',
                html             : 'Please wait while the system displays the requested data.',
                allowOutsideClick: false,
                allowEscapeKey   : false,
                showConfirmButton: false,
                didOpen          : () => Swal.showLoading()
            });
        },
        success: function (data) {
            const result              = data.responResult || [];
            globalDataPendidikanRJ = result;

            const dataChartPendidikan = result.map(item => ({
                kategori: item.PENDIDIKAN,
                qty     : item.TOTAL
            }));

            renderBarHorizontal('grafikkunjunganrjpendidikan', 'Jumlah', dataChartPendidikan, 'kategori', 'qty', true);
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

function pendidikanri(){
    let selectperiode   = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url + "index.php/dashboard/dashboard/pendidikanri",
        data      : {selectperiode:selectperiode},
        method    : "POST",
        dataType  : "JSON",
        cache     : false,
        beforeSend: function () {            
            Swal.fire({
                title            : 'Processing',
                html             : 'Please wait while the system displays the requested data.',
                allowOutsideClick: false,
                allowEscapeKey   : false,
                showConfirmButton: false,
                didOpen          : () => Swal.showLoading()
            });
        },
        success: function (data) {
            const result              = data.responResult || [];
            globalDataPendidikanRI = result;

            const dataChartPendidikan = result.map(item => ({
                kategori: item.PENDIDIKAN,
                qty     : item.TOTAL
            }));

            renderBarHorizontal('grafikkunjunganripendidikan', 'Jumlah', dataChartPendidikan, 'kategori', 'qty', true);
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

function top10poli(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url      : url + "index.php/dashboard/dashboard/top10poli",
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

            const result = response.responResult || [];
            globalDataPoli = result;

            const dataChartPoli = result.map(item => ({
                kategori: item.KETERANGAN,
                qty     : item.TOTAL
            }));

            renderBarHorizontal('grafikkunjunganrjpoli', 'Jumlah', dataChartPoli, 'kategori', 'qty', true);
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