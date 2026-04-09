// function generateAI(id_kunjungan) {

//     $.ajax({
//         url: "http://localhost/rsudpasarminggu/prod/analisadata/index.php/generateresumeai/" + id_kunjungan,
//         type: "POST",
//         dataType: "JSON",

//         beforeSend: function () {
//             $("#aiOverlay").fadeIn(200);
//             $(".ai-icon").addClass("animate-ai"); // 🔵 mulai animasi
//             $("#btnGenerateAI").prop("disabled", true);
//         },

//         success: function (response) {
//             $(".ai-icon").removeClass("animate-ai"); // 🟢 stop animasi
//             $("#aiOverlay").fadeOut(300);
//             $("#btnGenerateAI").prop("disabled", false);

//             typeWriterEffect($("#hasil_resume"), response.resume, 3);
//         },
//         complete: function () {
//             $("#aiOverlay").addClass("d-none").hide();
//         },
//         error: function () {
//             $(".ai-icon").removeClass("animate-ai"); // 🔴 stop animasi
//             $("#aiOverlay").fadeOut(300);
//             $("#btnGenerateAI").prop("disabled", false);
//             alert("AI Server Error");
//         }
//     });
// }

// function typeWriterEffect(element, text, speed = 5, callback = null) {
//     element.val('');
//     let i = 0;

//     function typing() {
//         if (i < text.length) {
//             element.val(element.val() + text.charAt(i));
//             element.scrollTop(element[0].scrollHeight);
//             i++;
//             setTimeout(typing, speed);
//         } else {
//             if (callback) callback(); // 🔥 jalankan setelah selesai
//         }
//     }

//     typing();
// }

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

            setAuto('keluhanutama_db', result['sourcedata'][0]['riwayat']['keluhanutama']['text']);
            setAuto('gejalapenyerta_db', result['sourcedata'][0]['riwayat']['gejala']['text']);
            setAuto('penyakitsekarang1_db', result['sourcedata'][0]['riwayat']['sekarang']['text']);
            setAuto('indikasiranap_db', result['sourcedata'][0]['diagnosis']['indikasiranap']['text']);
            setAuto('ttv_db', result['sourcedata'][0]['pemeriksaanfisik']['ttv']['text']);
            setAuto('lokalis_db', result['sourcedata'][0]['pemeriksaanfisik']['statuslokalis']['text']);
            setAuto('kontrol_db', result['sourcedata'][0]['kontrolulang']['text']);
            setAuto('segera_db', result['sourcedata'][0]['segeradibawa']['text']);
            setAuto('obatperawatan_db', result['sourcedata'][0]['penunjang']['obat']['perawatan']['text']);
            setAuto('obatpulang_db', result['sourcedata'][0]['penunjang']['obat']['pulang']['text']);
            setAuto('rad_db', result['sourcedata'][0]['penunjang']['radiologi']['text']);
            setAuto('lab_db', result['sourcedata'][0]['penunjang']['laboratorium']['text']);
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