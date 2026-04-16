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
        
        function dataoperasielektif($periode){
            $query =
                    "
                    SELECT 
                        X.*,
                        (X.BATAL / NULLIF(X.TOTAL, 0)) * 100 AS PERSENTASI
                    FROM (
                        SELECT 
                            TO_CHAR(A.TGL_TINDAKAN, 'MM') AS PERIODE,
                            COUNT(*) AS TOTAL,
                            SUM(CASE WHEN A.STATUS_ID = '99' THEN 1 ELSE 0 END) AS BATAL
                        FROM SR01_MED_OK_LOG A
                        WHERE A.LOKASI_ID = '001'
                        AND A.AKTIF = '1'
                        AND A.CITO = '0'
                        AND TO_CHAR(A.TGL_TINDAKAN,'YYYY')='".$periode."'
                        GROUP BY TO_CHAR(A.TGL_TINDAKAN, 'MM')
                    ) X

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datajampulangpasien($periode){
            $query =
                    "
                        SELECT 
                            X.*,
                            ROUND((X.SEBELUM_12 / NULLIF(X.TOTAL, 0)) * 100, 2) AS PERSENTASI,
                            (X.BIAYA_SNACK + X.BIAYA_MAKAN) AS TOTAL_BIAYA
                        FROM(
                            SELECT 
                                TO_CHAR(A.TGL_KELUAR,'MM') AS PERIODE,

                                COUNT(*) AS TOTAL,

                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 12 THEN 1 ELSE 0 END) AS SEBELUM_12,
                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 THEN 1 ELSE 0 END) AS SESUDAH_12,

                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                        AND TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 15 THEN 1 ELSE 0 END) AS JML_SNACK,

                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 15 THEN 1 ELSE 0 END) AS JML_MAKAN,

                                SUM(
                                    CASE 
                                        WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                        AND TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 15
                                        OR TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 15
                                        THEN
                                            CASE 
                                                WHEN TRIM(A.KELAS_ID) = 'V' THEN 12183
                                                WHEN TRIM(A.KELAS_ID) = '1' THEN 9021
                                                WHEN TRIM(A.KELAS_ID) = '2' THEN 7963
                                                WHEN TRIM(A.KELAS_ID) = '3' THEN 7832
                                                ELSE 0
                                            END
                                        ELSE 0
                                    END
                                ) AS BIAYA_SNACK,

                                SUM(
                                    CASE 
                                        WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 15
                                        THEN
                                            CASE 
                                                WHEN TRIM(A.KELAS_ID) = 'V' THEN 41526
                                                WHEN TRIM(A.KELAS_ID) = '1' THEN 36261
                                                WHEN TRIM(A.KELAS_ID) = '2' THEN 30605
                                                WHEN TRIM(A.KELAS_ID) = '3' THEN 27849
                                                ELSE 0
                                            END
                                        ELSE 0
                                    END
                                ) AS BIAYA_MAKAN

                            FROM SR01_KEU_EPISODE A
                            WHERE A.LOKASI_ID = '001'
                            AND A.AKTIF = '1'
                            AND A.JENIS_EPISODE = 'I'
                            AND   TO_CHAR(A.TGL_KELUAR,'YYYY')='".$periode."'

                            GROUP BY TO_CHAR(A.TGL_KELUAR,'MM')
                        ) X

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datajampulangharian(){
            $query =
                    "
                        SELECT 
                            X.*,
                            ROUND((X.SEBELUM_12 / NULLIF(X.TOTAL, 0)) * 100, 2) AS PERSENTASI,
                            (X.BIAYA_SNACK + X.BIAYA_MAKAN) AS TOTAL_BIAYA
                        FROM(
                            SELECT 
                                TO_CHAR(TRUNC(A.TGL_KELUAR),'DD.MM.YYYY') AS PERIODE,

                                COUNT(*) AS TOTAL,

                                -- ⏰ KPI
                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 12 THEN 1 ELSE 0 END) AS SEBELUM_12,
                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 THEN 1 ELSE 0 END) AS SESUDAH_12,

                                -- 🔥 JUMLAH PASIEN
                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 AND TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 15 THEN 1 ELSE 0 END) AS JML_SNACK,
                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 15 THEN 1 ELSE 0 END) AS JML_MAKAN,

                                -- 🔥 BIAYA SNACK (jumlah pasien × tarif)
                                SUM(
                                    CASE 
                                        WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                            AND TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 15
                                        THEN
                                            CASE 
                                                WHEN A.KELAS_ID = 'V'  THEN 12183
                                                WHEN A.KELAS_ID = '1'    THEN 9021
                                                WHEN A.KELAS_ID = '2'   THEN 7963
                                                WHEN A.KELAS_ID = '3'  THEN 7832
                                                ELSE 0
                                            END

                                        WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 15
                                        THEN
                                            CASE 
                                                WHEN A.KELAS_ID = 'V'  THEN 12183
                                                WHEN A.KELAS_ID = '1'    THEN 9021
                                                WHEN A.KELAS_ID = '2'   THEN 7963
                                                WHEN A.KELAS_ID = '3'  THEN 7832
                                                ELSE 0
                                            END

                                        ELSE 0
                                    END
                                ) AS BIAYA_SNACK,

                                -- 🔥 BIAYA MAKAN
                                SUM(
                                    CASE 
                                        WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 15
                                        THEN
                                            CASE 
                                                WHEN A.KELAS_ID = 'V'  THEN 41526
                                                WHEN A.KELAS_ID = '1'    THEN 36261
                                                WHEN A.KELAS_ID = '2'   THEN 30605
                                                WHEN A.KELAS_ID = '3'  THEN 27849
                                                ELSE 0
                                            END
                                        ELSE 0
                                    END
                                ) AS BIAYA_MAKAN

                            FROM SR01_KEU_EPISODE A
                            WHERE A.LOKASI_ID = '001'
                            AND A.AKTIF = '1'
                            AND A.JENIS_EPISODE = 'I'

                            -- RANGE H-14
                            AND A.TGL_KELUAR >= TRUNC(SYSDATE) - 14
                            AND A.TGL_KELUAR <  TRUNC(SYSDATE)

                            GROUP BY TRUNC(A.TGL_KELUAR)

                            ORDER BY TRUNC(A.TGL_KELUAR)
                        ) X

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        
        
    }
?>