let startDate, endDate;

flatpickr('[name="dateperiode"]', {
    mode      : "range",
    enableTime: false,
    dateFormat: "d.m.Y",
    maxDate   : "today",
    onChange  : function (selectedDates) {
        startDate = selectedDates[0] ? selectedDates[0].toLocaleDateString('en-CA') : null;
        endDate   = selectedDates[1] ? selectedDates[1].toLocaleDateString('en-CA') : null;
    }
});

$(document).on("click", ".btn-apply", function (e) {
    e.preventDefault();

    if (!startDate || !endDate) {
        toastr["warning"]("Please select a valid date range", "Warning");
        return;
    }

    monitoringbilling(startDate, endDate);
});

monitoringbilling(startDate, endDate);

function monitoringbilling(startDate, endDate){
    $.ajax({
        url       : url +"index.php/inpatient/monbilling/monitoringbilling",
        data      : {startdate:startDate,endate:endDate},
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

            $("#resultrawdatabilling").html("");
        },
        success:function(data){
            var   tableresult      = "";
            const result           = data.responResult || [];

            if(data.responCode==="00"){
                for(var i in result){
                    let nilai = todesimal(result[i].T_SELISIH) || "0";

                    tableresult +="<tr>";
                    tableresult +="<td class='ps-4'>"+(parseInt(i)+1)+"</td>";
                    tableresult +="<td>"+(result[i].MRPAS || "")+"</td>";
                    tableresult +="<td>" + (result[i].NAMAPASIEN || "") + (result[i].STATUS_EPISODE == 55 ? " <span class='badge badge-light-success small'>Closing</span>" : "") + "</td>";
                    tableresult +="<td class='text-center'>"+(result[i].TGLMASUK || "")+"</td>";  
                    tableresult +="<td class='text-center'>"+(result[i].LOS || "-")+"</td>";  
                    tableresult +="<td class='text-end'>"+(todesimal(result[i].S_OBAT) || "0")+"</td>";   
                    tableresult +="<td class='text-end'>"+(todesimal(result[i].S_LAB) || "0")+"</td>"; 
                    tableresult +="<td class='text-end'>"+(todesimal(result[i].S_RAD) || "0")+"</td>"; 
                    tableresult +="<td class='text-end'>"+(todesimal(result[i].S_TIND) || "0")+"</td>";   
                    tableresult +="<td class='text-end'>"+(todesimal(result[i].S_LAIN) || "0")+"</td>";  
                    tableresult +="<td class='text-end'>"+(todesimal(result[i].T_RAWATRS) || "0")+"</td>";    
                    tableresult +="<td class='text-end'>" +(result[i].S_INACBG == 0 ? "<a class='btn btn-icon' title='Klik Untuk Masukan Harga Ina Cbg'><i class='bi bi-pencil-square text-primary'></i></a>" : todesimal(result[i].S_INACBG)) + "</td>";
                    tableresult += "<td class='text-end " + (nilai != 0 ? "text-danger" : "") + "'>" + nilai + "</td>";  
                    tableresult +="<td>"+(result[i].DIAGNOSA || "")+"</td>"; 
                    tableresult +="<td>"+(result[i].KETERANGAN || "")+"</td>";         
                    
                    tableresult +="</tr>";                    
                }
            }

            $("#resultrawdatabilling").html(tableresult);
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