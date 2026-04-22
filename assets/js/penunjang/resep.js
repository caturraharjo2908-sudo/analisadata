datatransaksi();
datatransaksidepo();

$('#selectperiode').on('change', function () {
    datatransaksi();
    datatransaksidepo();
});

function datatransaksi(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url +"index.php/penunjang/resep/datatransaksi",
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

            $("#resultdatatransaksi").html("");
        },
        success:function(data){
            var   tableresult      = "";
            const result           = data.responResult || [];

            if (data.responCode !== "00") {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'Data not found.'
                });
                return;
            }

            // ?? inisialisasi total
            let totalJAN=0, totalFEB=0, totalMAR=0, totalAPR=0, totalMEI=0, totalJUN=0,
                totalJUL=0, totalAGU=0, totalSEP=0, totalOKT=0, totalNOV=0, totalDES=0,
                grandTotal=0;

            for (var i in result) {

                let jan = parseFloat(result[i].JAN || 0);
                let feb = parseFloat(result[i].FEB || 0);
                let mar = parseFloat(result[i].MAR || 0);
                let apr = parseFloat(result[i].APR || 0);
                let mei = parseFloat(result[i].MEI || 0);
                let jun = parseFloat(result[i].JUN || 0);
                let jul = parseFloat(result[i].JUL || 0);
                let agu = parseFloat(result[i].AGU || 0);
                let sep = parseFloat(result[i].SEP || 0);
                let okt = parseFloat(result[i].OKT || 0);
                let nov = parseFloat(result[i].NOV || 0);
                let des = parseFloat(result[i].DES || 0);

                let total = jan+feb+mar+apr+mei+jun+jul+agu+sep+okt+nov+des;

                // ?? akumulasi
                totalJAN += jan;
                totalFEB += feb;
                totalMAR += mar;
                totalAPR += apr;
                totalMEI += mei;
                totalJUN += jun;
                totalJUL += jul;
                totalAGU += agu;
                totalSEP += sep;
                totalOKT += okt;
                totalNOV += nov;
                totalDES += des;
                grandTotal += total;

                // ?? row data
                tableresult += "<tr>";
                tableresult += "<td class='ps-4'>" + (parseInt(i) + 1) + "</td>";
                tableresult += "<td>" + (result[i].PROVIDER || '-') + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(jan) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(feb) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(mar) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(apr) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(mei) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(jun) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(jul) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(agu) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(sep) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(okt) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(nov) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(des) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(total) + "</td>";
                tableresult += "<td class='fw-bold text-end pe-4'>" + (result[i].LASTUPDATE || '-') + "</td>";
                tableresult += "</tr>";
            }

            // ?? TOTAL ROW (PALING BAWAH)
            tableresult += "<tr class='fw-bold bg-light'>";
            tableresult += "<td colspan='2' class='text-end'>TOTAL</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalJAN) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalFEB) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalMAR) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalAPR) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalMEI) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalJUN) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalJUL) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalAGU) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalSEP) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalOKT) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalNOV) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalDES) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(grandTotal) + "</td>";
            tableresult += "<td></td>";
            tableresult += "</tr>";

            // ?? render
            $("#resultdatatransaksi").html(tableresult);
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

function datatransaksidepo(){
    let selectperiode = $("select[name='selectperiode']").val();
    $.ajax({
        url       : url +"index.php/penunjang/resep/datatransaksidepo",
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

            $("#resultdatatransaksidepo").html("");
        },
        success:function(data){
            var   tableresult      = "";
            const result           = data.responResult || [];

            if (data.responCode !== "00") {
                Swal.fire({
                    icon : 'warning',
                    title: 'No Data Available',
                    text : 'Data not found.'
                });
                return;
            }

            // ?? inisialisasi total
            let totalJAN=0, totalFEB=0, totalMAR=0, totalAPR=0, totalMEI=0, totalJUN=0,
                totalJUL=0, totalAGU=0, totalSEP=0, totalOKT=0, totalNOV=0, totalDES=0,
                grandTotal=0;

            for (var i in result) {

                let jan = parseFloat(result[i].JAN || 0);
                let feb = parseFloat(result[i].FEB || 0);
                let mar = parseFloat(result[i].MAR || 0);
                let apr = parseFloat(result[i].APR || 0);
                let mei = parseFloat(result[i].MEI || 0);
                let jun = parseFloat(result[i].JUN || 0);
                let jul = parseFloat(result[i].JUL || 0);
                let agu = parseFloat(result[i].AGU || 0);
                let sep = parseFloat(result[i].SEP || 0);
                let okt = parseFloat(result[i].OKT || 0);
                let nov = parseFloat(result[i].NOV || 0);
                let des = parseFloat(result[i].DES || 0);

                let total = jan+feb+mar+apr+mei+jun+jul+agu+sep+okt+nov+des;

                // ?? akumulasi
                totalJAN += jan;
                totalFEB += feb;
                totalMAR += mar;
                totalAPR += apr;
                totalMEI += mei;
                totalJUN += jun;
                totalJUL += jul;
                totalAGU += agu;
                totalSEP += sep;
                totalOKT += okt;
                totalNOV += nov;
                totalDES += des;
                grandTotal += total;

                // ?? row data
                tableresult += "<tr>";
                tableresult += "<td class='ps-4'>" + (parseInt(i) + 1) + "</td>";
                tableresult += "<td>" + (result[i].NAMAGUDANG || '-') + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(jan) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(feb) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(mar) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(apr) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(mei) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(jun) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(jul) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(agu) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(sep) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(okt) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(nov) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(des) + "</td>";
                tableresult += "<td class='text-end'>" + todesimal(total) + "</td>";
                tableresult += "<td class='fw-bold text-end pe-4'>" + (result[i].LASTUPDATE || '-') + "</td>";
                tableresult += "</tr>";
            }

            // ?? TOTAL ROW (DI BAWAH)
            tableresult += "<tr class='fw-bold bg-light'>";
            tableresult += "<td colspan='2' class='text-end'>TOTAL</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalJAN) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalFEB) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalMAR) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalAPR) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalMEI) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalJUN) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalJUL) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalAGU) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalSEP) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalOKT) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalNOV) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(totalDES) + "</td>";
            tableresult += "<td class='text-end'>" + todesimal(grandTotal) + "</td>";
            tableresult += "<td></td>";
            tableresult += "</tr>";

            // ?? render ke table
            $("#resultdatatransaksidepo").html(tableresult);
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