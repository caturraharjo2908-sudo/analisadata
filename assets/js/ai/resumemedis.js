listpasien();

$(document).on('click', '.pilih-pasien', function() {
    let namapasien = $(this).data('namapasien');
    let pasienid   = $(this).data('pasienid');
    let episodeid  = $(this).data('episodeid');

    $('#nampasien').val(namapasien);
    $('#pasienid').val(pasienid);
    $('#episodeid').val(episodeid);

    $('#btnsejarah').attr('onclick',"openSejarah('" + pasienid + "')");

    resumemedis(episodeid)
});

function listpasien(){
    $.ajax({
        url       : url +"index.php/ai/resumemedis/listpasien",
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

                    tableresult += "<tr>";
                    tableresult += "<td class='ps-4'>" + (parseInt(i) + 1) + "</td>";
                    tableresult += "<td>" + result[i].INT_PASIEN_ID + "</td>";
                    tableresult += "<td><a href='#' class='pilih-pasien' "+getvariabel+">" + result[i].NAMA + "</a></td>";
                    tableresult += "<td>" + result[i].TGL_LAHIR + "</td>";
                    tableresult += "<td>" + result[i].UMUR + "</td>";
                    tableresult += "<td>" + result[i].KELAMIN + "</td>";
                    tableresult += "<td>" + result[i].TGL_MASUK + "</td>";
                    tableresult += "<td>" + (result[i].TGL_KELUAR || '') + "</td>";
                    tableresult += "<td>" + result[i].RUANGRWT_ID + "</td>";
                    tableresult += "<td class='fw-bold text-end pe-4'>" + result[i].KELAS + "</td>";
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

function resumemedis(episodeid){
    $.ajax({
        url       : url +"index.php/ai/resumemedis/resumemedis",
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

            function setAuto(id, value){
                let el = document.getElementById(id);
                el.value = value || '';
                autoHeight(el);
            }

            setAuto('keluhanutama_ai', result['sourcedata'][0]['riwayat']['keluhanutama']['text']);
            // setAuto('keluhanutama_final', result['finalresume'][0]['riwayat']['keluhanutama']['text']);
            setAuto('gejala_ai', result['sourcedata'][0]['riwayat']['gejala']['text']);
            // setAuto('gejala_final', result['finalresume'][0]['riwayat']['gejala']['text']);
            setAuto('rps_ai', result['sourcedata'][0]['riwayat']['sekarang']['text']);
            // setAuto('rps_final', result['finalresume'][0]['riwayat']['sekarang']['text']);
            setAuto('rpd_ai', result['sourcedata'][0]['riwayat']['dahulu']['text']);
            setAuto('indikasiranap_ai', result['sourcedata'][0]['diagnosis']['indikasiranap']['text']);
            setAuto('ttv_ai', result['sourcedata'][0]['pemeriksaanfisik']['ttv']['text']);
            setAuto('lokalis_ai', result['sourcedata'][0]['pemeriksaanfisik']['statuslokalis']['text']);
            setAuto('kontrol_ai', result['sourcedata'][0]['kontrolulang']['text']);
            setAuto('segera_ai', result['sourcedata'][0]['segeradibawa']['text']);
            setAuto('obatperawatan_ai', result['sourcedata'][0]['penunjang']['obat']['perawatan']['text']);
            setAuto('obatpulang_ai', result['sourcedata'][0]['penunjang']['obat']['pulang']['text']);
            setAuto('rad_ai', result['sourcedata'][0]['penunjang']['radiologi']['text']);
            setAuto('lab_ai', result['sourcedata'][0]['penunjang']['laboratorium']['text']);
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

function autoHeight(el) {
    el.style.height = 'auto'; // reset dulu
    el.style.height = (el.scrollHeight) + 'px'; // sesuaikan isi
}