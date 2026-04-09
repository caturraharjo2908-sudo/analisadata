<?php
    class Modelmonbilling extends CI_Model{

        function monitoringbilling($startdate,$endate){
            $query =
                    "
                        SELECT 
                            A.STATUS_EPISODE, TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY')TGLMASUK,
                            TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') TGLLAHIR,
                            CASE
                                WHEN A.STATUS_EPISODE ='00' THEN
                                    (TRUNC(SYSDATE)-TRUNC(A.TGL_MASUK))+1
                                ELSE
                                    (TRUNC(SYSDATE)-TRUNC(A.TGL_KELUAR))+1
                            END LOS,
                            GETPIDINT(A.PASIEN_ID) MRPAS,
                            SR01_GET_SUFFIX(A.PASIEN_ID) NAMAPASIEN,
                            (SELECT NAMA FROM SR01_MED_DOKTER_MS WHERE DOKTER_ID=A.DOKTER_ID) NAMADOKTER,
                            BILLING.*
                            
                        FROM SR01_KEU_EPISODE A
                        LEFT JOIN SR01_KEU_RIBPJS BILLING
                            ON BILLING.PASIEN_ID = A.PASIEN_ID
                            AND BILLING.EPISODE_ID = A.EPISODE_ID
                                            
                        WHERE A.LOKASI_ID='001'
                        AND   A.AKTIF='1'
                        AND   A.JENIS_EPISODE='I'
                        AND   A.STATUS_EPISODE <> '99'
                        AND TRUNC(A.TGL_MASUK) BETWEEN TRUNC(TO_DATE('".$startdate."','YYYY-MM-DD')) AND TRUNC(TO_DATE('".$endate."','YYYY-MM-DD'))
                        
                        ORDER BY LOS DESC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

    }
?>