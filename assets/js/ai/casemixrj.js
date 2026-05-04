let today = new Date().toLocaleDateString('en-CA'); // format YYYY-MM-DD
let startDate = today;
let endDate   = today;

$(document).on("keyup", "#fieldsearch", function () {
    filterTableByKeywords("#fieldsearch", "#resultdatacasemixrj");
});

flatpickr('[name="dateperiode"]', {
    mode      : "range",
    enableTime: false,
    dateFormat: "d.m.Y",
    maxDate   : "today",
    onChange  : function (selectedDates, dateStr, instance) {
        startDate = selectedDates[0] ? selectedDates[0].toLocaleDateString('en-CA') : null;
        endDate   = selectedDates[1]  ? selectedDates[1].toLocaleDateString('en-CA') : null;
    }
});

$(document).on("click", ".btn-apply", function (e) {
    e.preventDefault();

    if (!startDate || !endDate) {
        toastr["warning"]("Please select a valid date range", "Warning");
        return;
    }

    casemixrj(startDate, endDate);
});

casemixrj(startDate, endDate);

function casemixrj(startDate, endDate){
    $.ajax({
        url       : url +"index.php/ai/casemixrj/casemixrj",
        data      : {startDate:startDate,endDate:endDate},
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

            $("#resultdatacasemixrj").html("");
        },
        success:function(data){
            var   tableresult = "";
            const result      = data.responResult || [];

            if(data.responCode==="00"){
                for(var i in result){

                    let btnaction = "<a class='dropdown-item btn btn-sm' href='#' onclick=\"openSejarah('" + result[i].PASIEN_ID + "')\"><i class='bi bi-clock-history text-primary pe-4'></i>Sejarah</a>";

                    tableresult +="<tr>";
                    tableresult +="<td class='ps-4'>"+(parseInt(i)+1)+"</td>";
                    tableresult +="<td>"+(result[i].MRPASIEN || "")+"</td>";  
                    tableresult +="<td>"+(result[i].NAMAPASIEN || "")+"</td>";  
                    tableresult +="<td>"+(result[i].TGLMASUK || "")+"</td>";  
                    tableresult +="<td>"+(result[i].NAMAPOLI || "")+"</td>"; 
                    tableresult +="<td>"+(result[i].NAMADOKTER || "")+"</td>";  
                    tableresult +="<td>"+(result[i].SEP_NOMOR || "")+"</td>";  
                    if(result[i].FLAG_KLAIM!="Clear"){
                        tableresult +="<td>"+("<span class='badge badge-light-danger'>"+result[i].STATUS_BPJS+"</span>" || "")+"</td>";
                        tableresult +="<td>"+(result[i].DPJP_UTAMA || "")+"</td>";  
                    }else{
                        tableresult +="<td></td>";
                        tableresult +="<td></td>";
                    }  
                      
                    
                    tableresult += "<td class='fw-bold text-end pe-4'>";
                    tableresult += "<div class='btn-group'>";
                    tableresult += "<button type='button' class='btn btn-light-primary dropdown-toggle btn-sm' data-bs-toggle='dropdown'>Actions</button>";
                    tableresult += "<div class='dropdown-menu'>";
                    tableresult += btnaction;
                    tableresult += "</div></div>";
                    tableresult +="</td>";
                    
                    tableresult +="</tr>";                    
                }
            }

            $("#resultdatacasemixrj").html(tableresult);
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