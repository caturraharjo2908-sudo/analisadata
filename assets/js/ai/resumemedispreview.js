listpasien();

function getdata(btn){
    var pasienid   = btn.attr("data-pasienid");
    var episodeid  = btn.attr("data-episodeid");
    var namapasien = btn.attr("data-namapasien");

    clearTextarea();

    $("#episodeid").val(episodeid);

	resumeAI(episodeid);
    resumeFinal(episodeid);
};

function clearTextarea(){
    document.querySelectorAll('textarea').forEach(el => {
        el.value = '';
    });
}

function listpasien(){
    $.ajax({
        url       : url +"index.php/ai/resumemedispreview/listpasien",
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

            $("#resultdatapendingresume").html("");
        },

        success:function(data){
            var   tableresult      = "";
            const result           = data.responResult || [];

            if(data.responCode === "00"){
                for (var i in result) {
                    getvariabel = " data-pasienid='"+result[i].PASIEN_ID+"'"+
                                  " data-episodeid='"+result[i].EPISODE_ID+"'"+
                                  " data-namapasien='"+result[i].NAMA+"'";

                    let btnaction = "<a class='dropdown-item btn btn-sm' href='#' onclick=\"openSejarah('" + result[i].PASIEN_ID + "')\"><i class='bi bi-clock-history text-primary pe-4'></i>Sejarah</a>";
                        btnaction +="<a class='dropdown-item btn btn-sm' data-bs-toggle='modal' data-bs-target='#modal_view_resume' "+getvariabel+" onclick='getdata($(this));'><i class='bi bi-pencil'></i> View Resume Medis</a>";

                    tableresult += "<tr>";
                    tableresult += "<td class='ps-4'>" + (parseInt(i) + 1) + "</td>";
                    tableresult += "<td>" + result[i].INT_PASIEN_ID + "</td>";
                    tableresult += "<td>" + result[i].EPISODE_ID + "</td>";
                    tableresult += "<td>" + result[i].NAMA + "</td>";
                    tableresult += "<td>" + result[i].TGL_LAHIR + "</td>";
                    tableresult += "<td>" + result[i].UMUR + "</td>";
                    tableresult += "<td>" + result[i].KELAMIN + "</td>";
                    tableresult += "<td>" + result[i].TGL_MASUK + "</td>";
                    tableresult += "<td>" + (result[i].TGL_KELUAR || '') + "</td>";
                    tableresult += "<td>" + result[i].RUANGRWT_ID + "</td>";
                    tableresult += "<td>" + result[i].KELAS + "</td>";
                    tableresult += "<td class='fw-bold text-end pe-4'>";
                    tableresult += "<div class='btn-group'>";
                    tableresult += "<button type='button' class='btn btn-light-primary dropdown-toggle btn-sm' data-bs-toggle='dropdown'>Actions</button>";
                    tableresult += "<div class='dropdown-menu'>";
                    tableresult += btnaction;
                    tableresult += "</div></div>";
                    tableresult +="</td>";
                    tableresult += "</tr>";
                }
            }


            $("#resultdatapendingresume").html(tableresult);
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

function resumeAI(episodeid){
    $.ajax({
        url       : url +"index.php/ai/resumemedispreview/resumeAI",
        data      : {episodeid:episodeid},
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
        },

        success:function(data){
            let result = data.responResult;

            function setValue(id, value){
                let el = document.getElementById(id);
                if(!el) return;

                el.value = value || '';
            }

            setValue('keluhanutama_ai', result[0].KELUHAN);
            setValue('gejala_ai', result[0].GEJALA);
            setValue('rps_ai', result[0].RIWAYATPS);
            setValue('rpd_ai', result[0].RIWAYATPD);
            setValue('indikasiranap_ai', result[0].INDIKASI);
            setValue('ttv_ai', result[0].VITAL);
            setValue('lokalis_ai', result[0].STATUS);
            setValue('kontrol_ai', result[0].KONTROL);
            setValue('segera_ai', result[0].INTRUKSI);
            setValue('obatpulang_ai', result[0].OBATP);
            setValue('rad_ai', result[0].LAINNYA);
            setValue('lab_ai', result[0].LAB);
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

function resumeFinal(episodeid){
    $.ajax({
        url       : url +"index.php/ai/resumemedispreview/resumeFinal",
        data      : {episodeid:episodeid},
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
        },

        success:function(data){
            let result = data.responResult;

            function setValue(id, value){
                let el = document.getElementById(id);
                if(!el) return;

                el.value = value || '';
            }

            setValue('keluhanutama_final', result[0].KELUHAN);
            setValue('gejala_final', result[0].GEJALA);
            setValue('rps_final', result[0].RIWAYATPS);
            setValue('rpd_final', result[0].RIWAYATPD);
            setValue('indikasiranap_final', result[0].INDIKASI);
            setValue('ttv_final', result[0].VITAL);
            setValue('lokalis_final', result[0].STATUS);
            setValue('kontrol_final', result[0].KONTROL);
            setValue('segera_final', result[0].INTRUKSI);
            setValue('obatpulang_final', result[0].OBATP);
            setValue('rad_final', result[0].LAINNYA);
            setValue('lab_final', result[0].LAB);
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