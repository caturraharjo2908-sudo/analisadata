<?php
    class Modelkpi extends CI_Model{

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

        function datalamakonsultasi($periode){
            $query = "

                SELECT 
                    A.DOKTER_ID,
                    COUNT(*) AS JUMLAH_PASIEN,
                    (SELECT UPPER(NAMA) FROM SR01_MED_DOKTER_MS WHERE DOKTER_ID=A.DOKTER_ID)NAMADOKTER,

                    ROUND(
                        AVG(
                            ( (SELECT MIN(CREATED_DATE) 
                            FROM WEB_CO_SELESAI_PERIKSA S
                            WHERE S.PASIEN_ID=A.PASIEN_ID
                                AND S.EPISODE_ID=A.EPISODE_ID)
                            -
                            (SELECT MIN(CREATED_DATE) 
                            FROM WEB_CO_MULAI_PERIKSA M
                            WHERE M.PASIEN_ID=A.PASIEN_ID
                                AND M.EPISODE_ID=A.EPISODE_ID)
                            ) * 24 * 60
                        ), 2
                    ) AS AVG_KONSULTASI_MENIT

                FROM WEB_CO_REGISTRASI_ONLINE_HD A
                WHERE A.LOKASI_ID='001'
                AND A.AKTIF='1'
                AND A.HADIR='Y'
                AND A.DOKTER_ID<>'DR. F0000000001'
                AND TO_CHAR(A.TGL_MASUK,'YYYY')='".$periode."'
                AND A.POLI_ID NOT IN ('UGD01','UGD02')

                GROUP BY A.DOKTER_ID
                ORDER BY AVG_KONSULTASI_MENIT DESC
            ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datapeakdokter($periode){
            $query = "

                SELECT 
                    POLI_ID,
                    DOKTER_ID,
                    NAMADOKTER,
                    JAM,
                    ROUND(AVG(JUMLAH_PASIEN), 0) AS AVG_PASIEN
                FROM (
                    SELECT 
                        POLI_ID,
                        DOKTER_ID,
                        NAMADOKTER,
                        TO_CHAR(MULAIPERIKSA, 'YYYY-MM-DD') AS HARI,
                        TO_CHAR(MULAIPERIKSA, 'HH24') AS JAM,
                        COUNT(*) AS JUMLAH_PASIEN
                    FROM (
                        SELECT 
                            A.POLI_ID,
                            A.DOKTER_ID,

                            (SELECT UPPER(NAMA) 
                            FROM SR01_MED_DOKTER_MS 
                            WHERE LOKASI_ID='001' 
                            AND AKTIF='1' 
                            AND DOKTER_ID=A.DOKTER_ID
                            ) AS NAMADOKTER,

                            (SELECT MIN(CREATED_DATE)
                            FROM WEB_CO_MULAI_PERIKSA M
                            WHERE M.PASIEN_ID = A.PASIEN_ID
                            AND M.EPISODE_ID = A.EPISODE_ID
                            ) AS MULAIPERIKSA

                        FROM WEB_CO_REGISTRASI_ONLINE_HD A
                        WHERE A.LOKASI_ID = '001'
                        AND A.AKTIF = '1'
                        AND A.HADIR = 'Y'
                        AND A.POLI_ID NOT IN ('UGD01','UGD02')
                        AND TO_CHAR(A.TGL_MASUK,'YYYY') = '".$periode."'
                    ) X
                    WHERE MULAIPERIKSA IS NOT NULL

                    GROUP BY 
                        POLI_ID,
                        DOKTER_ID,
                        NAMADOKTER,
                        TO_CHAR(MULAIPERIKSA, 'YYYY-MM-DD'),
                        TO_CHAR(MULAIPERIKSA, 'HH24')
                ) Y
                GROUP BY 
                    POLI_ID,
                    DOKTER_ID,
                    NAMADOKTER,
                    JAM
                ORDER BY 
                    POLI_ID,
                    DOKTER_ID,
                    JAM
            ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }
        
        
    }
?>