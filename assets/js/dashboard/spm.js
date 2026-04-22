dataspm();

function dataspm(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url +"index.php/dashboard/spm/dataspm",
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

            $("#resuldataspmtw1").html("");
        },
        success:function(data){
            let   tableresult = "";
            let   grouped     = {};
            const result      = data.responResult || [];

            if (data.responCode !== "00") {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'Data not found.'
                });
                return;
            }

            result.forEach(row => {
                let key = row.KATEGORI === 'H' ? row.SPM_ID : row.HEADER_SPM_ID;

                if (!grouped[key]) {
                    grouped[key] = { header: null, detail: [] };
                }

                if (row.KATEGORI === 'H') {
                    grouped[key].header = row;
                } else {
                    grouped[key].detail.push(row);
                }
            });

            let no = 1;

            function isTidakAda(val) {
                return (val || "").toString().trim().toLowerCase() === "tidak ada";
            }

            // 🔥 render
            for (let key in grouped) {

                let group = grouped[key];
                let header = group.header;
                let details = group.detail;
                let rowspan = details.length > 0 ? details.length : 1;

                if (details.length === 0) {
                    tableresult += `
                    <tr>
                        <td class="ps-4">${no++}</td>
                        <td>${header ? header.SPM : '-'}</td>
                        <td colspan="12">-</td>
                    </tr>`;
                    continue;
                }

                details.forEach((d, i) => {

                    let tw1 = "-";
                    let status = "-";
                    let isTercapai = false;

                    let bln1 = d.BLN1;
                    let bln2 = d.BLN2;
                    let bln3 = d.BLN3;

                    // =========================
                    // 🔥 HITUNG TW1
                    // =========================
                    if (d.TIPE === '1' || d.TIPE === '3') {

                        let n1 = parseFloat(bln1 ?? 0);
                        let n2 = parseFloat(bln2 ?? 0);
                        let n3 = parseFloat(bln3 ?? 0);

                        tw1 = (n1 + n2 + n3) / 3;
                        tw1 = Math.round(tw1 * 100) / 100;

                    } else if (d.TIPE === '2') {

                        let tidakAda =
                            isTidakAda(bln1) ||
                            isTidakAda(bln2) ||
                            isTidakAda(bln3);

                        tw1 = tidakAda ? "Tidak Ada" : "Ada";
                    }

                    // =========================
                    // 🔥 STATUS
                    // =========================
                    if (d.TIPE === '1' || d.TIPE === '3') {

                        let target = parseFloat(d.TARGET_2);
                        let realisasi = parseFloat(tw1);

                        if (!isNaN(target) && !isNaN(realisasi)) {
                            if(d.TARGET_1 === "<"){
                                isTercapai = realisasi < target;
                            }else{
                                isTercapai = realisasi >= target;
                            }
                            

                            status = isTercapai ? `<span class="badge badge-light-success">Tercapai</span>` : `<span class="badge badge-light-danger">Tidak Tercapai</span>`;
                        } else {
                            status = `<span class="badge badge-light-warning">Invalid</span>`;
                        }

                    } else if (d.TIPE === '2') {

                        isTercapai = (tw1 === "Ada");

                        status = isTercapai
                            ? `<span class="badge badge-light-success">Tercapai</span>`
                            : `<span class="badge badge-light-danger">Tidak Tercapai</span>`;
                    }

                    // =========================
                    // 🔥 MASALAH & RTL
                    // =========================
                    let masalah = "-";
                    let rtl     = "-";

                    if (!isTercapai) {

                        // MASALAH
                        if (!d.MASALAH_TW1 || d.MASALAH_TW1.trim() === '') {
                            masalah = `
                                <span class="text-danger">Permasalahan belum disampaikan</span><br>
                                <button class="btn btn-sm btn-light-info mt-1" onclick="inputMasalah('${d.SPM_ID}')">Input</button>
                            `;
                        } else {
                            masalah = d.MASALAH_TW1;
                        }

                        // RTL
                        if (!d.RTL_TW1 || d.RTL_TW1.trim() === '') {
                            rtl = `
                                <span class="text-danger">Rencana tindak lanjut belum disampaikan</span><br>
                                <button class="btn btn-sm btn-light-info mt-1" onclick="inputRTL('${d.SPM_ID}')">Input</button>
                            `;
                        } else {
                            rtl = d.RTL_TW1;
                        }
                    }

                    // =========================
                    // 🔥 RENDER
                    // =========================
                    tableresult += "<tr>";

                    if (i === 0) {
                        tableresult += `<td class="ps-4" rowspan="${rowspan}">${no++}</td>`;
                        tableresult += `<td rowspan="${rowspan}">${header ? header.SPM : '-'}</td>`;
                    } else {
                        tableresult += `<td style="display:none;"></td>`;
                        tableresult += `<td style="display:none;"></td>`;
                    }

                    tableresult += `<td>${d.JENIS || ''}</td>`;
                    tableresult += `<td>${d.URUT}. ${d.SPM}</td>`;
                    tableresult += `<td class="text-end">${d.TARGET_1 || ''}</td>`;
                    tableresult += `<td class="text-end">${d.TARGET_2 || ''}</td>`;
                    tableresult += `<td class="text-end">${d.PENGISIAN || ''}</td>`;
                    tableresult += `<td class="text-end">${bln1 || ''}</td>`;
                    tableresult += `<td class="text-end">${bln2 || ''}</td>`;
                    tableresult += `<td class="text-end">${bln3 || ''}</td>`;
                    tableresult += `<td class="text-end">${tw1}</td>`;
                    tableresult += `<td>${status}</td>`;
                    tableresult += `<td>${masalah}</td>`;
                    tableresult += `<td>${rtl}</td>`;
                    tableresult += `<td class="text-end pe-4"></td>`;

                    tableresult += "</tr>";
                });
            }

            $("#resuldataspmtw1").html(tableresult);
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