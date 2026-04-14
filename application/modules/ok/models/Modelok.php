<?php
    class Modelok extends CI_Model{

        function periode(){
            $query =
                    "
                        SELECT (2014 + LEVEL) AS PERIODE
                        FROM DUAL
                        CONNECT BY LEVEL <= EXTRACT(YEAR FROM SYSDATE) - 2014
                        ORDER BY PERIODE DESC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datakunjungan($periode){
            $query =
                    "
                        SELECT A.TRANS_ID, TGL_TINDAKAN, STATUS_ID, ALASAN_BATAL, RUANG_OK, JAM_MULAI,
                               (SELECT NAMA FROM SR01_KEU_REKANAN_MS WHERE REKANAN_ID=(SELECT REKANAN_ID FROM SR01_KEU_EPISODE WHERE PASIEN_ID=A.PASIEN_ID AND EPISODE_ID=A.EPISODE_ID))PROVIDER
                        FROM SR01_MED_OK_LOG A
                        WHERE A.LOKASI_ID='001'
                        AND   A.AKTIF='1'
                        AND   TO_CHAR(A.TGL_TINDAKAN,'YYYY')='".$periode."'
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datakunjunganprovider($periode){
            $query =
                    "
                        SELECT 
                            E.REKANAN_ID AS PROVIDERID,
                            (SELECT NAMA FROM SR01_KEU_REKANAN_MS WHERE REKANAN_ID=E.REKANAN_ID)PROVIDER,
                            COUNT(*) AS TOTAL
                        FROM SR01_MED_OK_LOG A
                        JOIN SR01_KEU_EPISODE E 
                            ON E.PASIEN_ID = A.PASIEN_ID 
                        AND E.EPISODE_ID = A.EPISODE_ID
                        WHERE A.LOKASI_ID = '001'
                        AND   A.AKTIF = '1'
                        AND   A.STATUS_ID = '02'
                        AND   TO_CHAR(A.TGL_TINDAKAN, 'YYYY') = '".$periode."'
                        GROUP BY E.REKANAN_ID
                        ORDER BY TOTAL DESC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }
    }
?>