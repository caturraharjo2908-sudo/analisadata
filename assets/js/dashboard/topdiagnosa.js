let globalDataTopDiagnosaIGD = [];
let globalDataTopDiagnosaRJ = [];
let globalDataTopDiagnosaRI = [];

datarjgeriatri();
datarj();
dataigd();
datari();

$('#selectperiode').on('change', function () {
    datarjgeriatri();
    datarj();
    dataigd();
    datari();
});


$("#btnDownloadExcelIGD").on("click", function () {
    exportToExcel(
        globalDataTopDiagnosaIGD,
        "Top Diagnosa IGD",
        "Top_Diagnosa_IGD.xlsx"
    );
});

$("#btnDownloadExcelRJ").on("click", function () {
    exportToExcel(
        globalDataTopDiagnosaRJ,
        "Top Diagnosa Rawat Jalan",
        "Top_Diagnosa_Rawat_Jalan.xlsx"
    );
});

$("#btnDownloadExcelRI").on("click", function () {
    exportToExcel(
        globalDataTopDiagnosaRI,
        "Top Diagnosa Rawat Inap",
        "Top_Diagnosa_Rawat_Inap.xlsx"
    );
});


function datarjgeriatri(){
    let selectperiode   = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url + "index.php/dashboard/topdiagnosa/datarjgeriatri",
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

            const dataCharttopDiagnosa = result.map(item => ({
                kategori: item.DESCRIPTION,
                qty     : item.JUMLAH
            }));

            renderBarHorizontal('grafiktopdiagnosarjgeriatri', 'Jumlah Kasus', dataCharttopDiagnosa, 'kategori', 'qty', true);
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
}

function datarj(){
    let selectperiode   = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url + "index.php/dashboard/topdiagnosa/datarj",
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
            globalDataTopDiagnosaRJ = result;

            const dataCharttopDiagnosa = result.map(item => ({
                kategori: item.DESCRIPTION,
                qty     : item.JUMLAH
            }));

            renderBarHorizontal('grafiktopdiagnosarjall', 'Jumlah Kasus', dataCharttopDiagnosa, 'kategori', 'qty', true);
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
}

function dataigd(){
    let selectperiode   = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url + "index.php/dashboard/topdiagnosa/dataigd",
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
            globalDataTopDiagnosaIGD = result;

            const dataCharttopDiagnosa = result.map(item => ({
                kategori: item.DESCRIPTION,
                qty     : item.JUMLAH
            }));

            renderBarHorizontal('grafiktopdiagnosaigd', 'Jumlah Kasus', dataCharttopDiagnosa, 'kategori', 'qty', true);
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
}

function datari(){
    let selectperiode   = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url + "index.php/dashboard/topdiagnosa/datari",
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
            globalDataTopDiagnosaRI = result;

            const dataCharttopDiagnosa = result.map(item => ({
                kategori: item.DESCRIPTION,
                qty     : item.JUMLAH
            }));

            renderBarHorizontal('grafiktopdiagnosainapall', 'Jumlah Kasus', dataCharttopDiagnosa, 'kategori', 'qty', true);
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
}