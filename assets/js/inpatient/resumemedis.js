resumemedis();

$('#selectperiode').on('change', function () {
    resumemedis();
});

$(document).on("keyup", "#fieldsearch", function () {
    filterTableByKeywords("#fieldsearch", "#resultdatapendingresume");
});

function resumemedis(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url +"index.php/inpatient/resumemedis/resumemedis",
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

            $("#resultdatapendingresume").html("");
            $("#totalpasienpulang").html("Total Pasien Pulang Rawat Inap : 0 Px");
            $('#totalresume').html('Total Resume Yang Telah Di Buat : 0 Px');
            $('#pendingresumekurang').html('Pending Resume Medis <= 48 Jam : 0 Px');
            $('#pendingresumelebih').html('Pending Resume > 48 Jam : 0 Px');
        },
        success:function(data){

            let totalResume       = 0;
            let resumekurang48jam = 0;
            let resumelebih48jam  = 0;
            let tableresult       = "";

            const result = data.responResult || [];

            let tableBulanan = {
                "01":{}, "02":{}, "03":{}, "04":{}, "05":{}, "06":{},
                "07":{}, "08":{}, "09":{}, "10":{}, "11":{}, "12":{}
            };

            if(data.responCode==="00"){    

                const chartDataBulanan = aggregateBulanResume(result,"TGL_KELUAR");
                const chartDataHarian  = aggregateHarianResumeLAST30(result,"TGL_KELUAR");
                const chartDataGlobal  = aggregateResumeGlobal(result);

                renderchartbar(
                    "grafikresumemedis",
                    chartDataBulanan,
                    [
                        { name: "Resume > 48 Jam", field: "lebih48" },
                        { name: "Resume <= 48 Jam", field: "kurang48" }
                    ],
                    "Periode Tanggal Pulang Rawat Inap",
                    "Persentase",
                    true
                );

                renderchartbar(
                    "grafikresumemedisharian",
                    chartDataHarian,
                    [
                        { name: "Resume > 48 Jam", field: "lebih48" },
                        { name: "Resume <= 48 Jam", field: "kurang48" }
                    ],
                    "Tanggal Pulang Rawat Inap",
                    "Persentase",
                    true
                );

                renderchartpie("grafikresumemedisglobal",chartDataGlobal);

                // =========================
                // 🔥 PROCESS DATA
                // =========================

                result.forEach((item, i) => {

                    if(!item.TGLKELUAR) return;

                    // ambil tanggal saja (tanpa jam)
                    let tanggalOnly = item.TGLKELUAR.split(" ")[0];

                    let splitTgl = tanggalOnly.includes(".")
                        ? tanggalOnly.split(".")
                        : tanggalOnly.split("-");

                    let bulan   = splitTgl[1];
                    let tanggal = tanggalOnly;

                    if(!tableBulanan[bulan][tanggal]){
                        tableBulanan[bulan][tanggal] = {
                            total:0,
                            selesai:0,
                            belum:0
                        };
                    }

                    // total pasien
                    tableBulanan[bulan][tanggal].total++;

                    let durasi     = parseInt(item.DURASI) || 0;
                    let adaResume  = item.TRANSCORESUME !== null && item.TRANSCORESUME !== "";

                    // =========================
                    // 🔥 RULE FINAL
                    // =========================
                    if(durasi > 2 || !adaResume){

                        // ❌ BELUM
                        tableBulanan[bulan][tanggal].belum++;

                        if(durasi > 2){
                            resumelebih48jam++;
                        }else{
                            resumekurang48jam++;
                        }

                    }else{

                        // ✅ SELESAI (<=48 jam & ada resume)
                        tableBulanan[bulan][tanggal].selesai++;
                        totalResume++;

                    }

                    // =========================
                    // 🔥 TABLE DETAIL
                    // =========================

                    let btnaction = "<a class='dropdown-item btn btn-sm' href='#' onclick=\"openSejarah('" + item.PASIEN_ID + "')\"><i class='bi bi-clock-history text-primary pe-4'></i>Sejarah</a>";

                    tableresult += "<tr>";
                    tableresult += "<td class='ps-4'>"+(i+1)+"</td>";
                    tableresult += "<td>"+(item.MRPAS || "")+"</td>";
                    tableresult += "<td>"+(item.NAMAPASIEN || "")+"</td>";
                    tableresult += "<td>"+(item.SEXID || "")+"</td>";
                    tableresult += "<td>"+(item.RUANGRWT_ID || "")+"</td>";
                    tableresult += "<td>"+(item.KELAS_ID || "")+"</td>";
                    tableresult += "<td>"+(item.DPJP || "")+"</td>";
                    tableresult += "<td>"+(item.TGLMASUK || "")+"</td>";
                    tableresult += "<td>"+(item.TGLKELUAR || "")+"</td>";
                    tableresult += "<td>"+(item.PROVIDER || "")+"</td>";
                    tableresult += "<td>"+(item.CARAPULANG || "")+"</td>";

                    if(adaResume){
                        tableresult += "<td><span class='badge badge-light-success'>Resume Sudah Dibuat</span></td>";
                    }else{
                        if(durasi > 2){
                            tableresult += "<td><span class='badge badge-light-danger'>Resume Belum Dibuat > 48 Jam</span></td>";
                        }else{
                            tableresult += "<td><span class='badge badge-light-warning'>Resume Belum Dibuat <= 48 Jam</span></td>";         
                        }
                    }

                    tableresult += "<td>"+(item.CREATEDDATERESUME || "")+"</td>";

                    tableresult += "<td class='text-end'>";
                    tableresult += "<div class='btn-group'>";
                    tableresult += "<button type='button' class='btn btn-light-primary dropdown-toggle btn-sm' data-bs-toggle='dropdown'>Actions</button>";
                    tableresult += "<div class='dropdown-menu'>";
                    tableresult += btnaction;
                    tableresult += "</div></div></td>";

                    tableresult += "</tr>";

                });

                // =========================
                // 📊 RENDER TABLE
                // =========================

                for(let bulan in tableBulanan){

                    let html = "";
                    let no   = 1;

                    let tanggalSorted = Object.keys(tableBulanan[bulan]).sort((a,b)=>{

                        let da = a.includes(".") ? a.split(".").reverse().join("-") : a;
                        let db = b.includes(".") ? b.split(".").reverse().join("-") : b;

                        return new Date(da) - new Date(db);

                    });

                    tanggalSorted.forEach(tanggal => {

                        let row = tableBulanan[bulan][tanggal];

                        let persen = 0;
                        if(row.total > 0){
                            persen = (row.selesai / row.total) * 100;
                        }

                        html += "<tr>";
                        html += "<td class='text-center'>"+no+"</td>";
                        html += "<td class='text-center'>"+tanggal+"</td>";
                        html += "<td class='text-center'>"+todesimal(row.belum)+"</td>";
                        html += "<td class='text-center'>"+todesimal(row.selesai)+"</td>";
                        html += "<td class='text-center'>"+todesimal(row.total)+"</td>";
                        html += "<td class='text-end pe-4'>"+persen.toFixed(2)+" %</td>";
                        html += "</tr>";

                        no++;
                    });

                    $("#resultdatabln"+bulan).html(html);
                }
            }

            // =========================
            // 📌 SUMMARY
            // =========================

            $("#resultdatapendingresume").html(tableresult);

            $("#totalpasienpulang").html(
                "Total Pasien Pulang Rawat Inap : " + todesimal(result.length) + " Px"
            );

            $("#totalresume").html(
                "Total Resume Tepat Waktu (<=48 Jam) : " + todesimal(totalResume) + " Px"
            );

            $("#pendingresumekurang").html(
                "Pending Resume (<=48 Jam belum isi) : " + todesimal(resumekurang48jam) + " Px"
            );

            $("#pendingresumelebih").html(
                "Pending Resume (>48 Jam) : " + todesimal(resumelebih48jam) + " Px"
            );

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