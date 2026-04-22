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

                                SUM(CASE WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 AND TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) < 15 THEN 1 ELSE 0 END) AS JML_SNACK,

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
                            AND A.PULANG_ID NOT IN ('P02','P04','P10','P11','P0X')
                            AND A.KELAS_ID<>'V'
                            AND TO_CHAR(A.TGL_KELUAR,'YYYY')='".$periode."'

                            GROUP BY TO_CHAR(A.TGL_KELUAR,'MM')
                        ) X

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function dataheatmap($periode){
            $query =
                    "
                        SELECT 
                            U.UNIT_ID,
                            U.NAMA_UNIT AS UNIT,
                            
                            COUNT(*) AS TOTAL,
                            
                            -- pasien keluar >= jam 12
                            SUM(CASE 
                                WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                THEN 1 ELSE 0 
                            END) AS SESUDAH_12,

                            -- persentase
                            ROUND(
                                (SUM(CASE 
                                    WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                    THEN 1 ELSE 0 
                                END) / NULLIF(COUNT(*),0)) * 100
                            ,2) AS PERSENTASE

                        FROM SR01_KEU_EPISODE A

                        -- 🔥 mapping ruang rawat → ruang
                        LEFT JOIN SR01_MED_RUANG_PRWT RP
                            ON RP.RUANGRWT_ID = A.RUANGRWT_ID

                        -- 🔥 ruang → unit
                        LEFT JOIN SR01_MED_RUANG_MS R
                            ON R.RUANG_ID = RP.RUANG_ID

                        -- 🔥 unit master
                        LEFT JOIN SR01_GEN_UNIT_MS U
                            ON U.UNIT_ID = R.UNIT_ID

                        WHERE A.LOKASI_ID = '001'
                        AND A.AKTIF = '1'
                        AND A.JENIS_EPISODE = 'I'
                        AND A.PULANG_ID NOT IN ('P02','P04','P10','P11','P0X')
                        AND A.KELAS_ID <> 'V'

                        AND TO_CHAR(A.TGL_KELUAR,'YYYY')='".$periode."'

                        GROUP BY 
                            U.UNIT_ID,
                            U.NAMA_UNIT

                        HAVING SUM(CASE 
                                    WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                    THEN 1 ELSE 0 
                            END) > 0

                        ORDER BY SESUDAH_12 DESC

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
                            AND A.PULANG_ID NOT IN ('P02','P04','P10','P11','P0X')
                            AND A.KELAS_ID<>'V'
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

        function dataheatmapharian(){
            $query =
                    "
                        SELECT 
                            U.UNIT_ID,
                            U.NAMA_UNIT AS UNIT,
                            
                            COUNT(*) AS TOTAL,
                            
                            -- pasien keluar >= jam 12
                            SUM(CASE 
                                WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                THEN 1 ELSE 0 
                            END) AS SESUDAH_12,

                            -- persentase
                            ROUND(
                                (SUM(CASE 
                                    WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                    THEN 1 ELSE 0 
                                END) / NULLIF(COUNT(*),0)) * 100
                            ,2) AS PERSENTASE

                        FROM SR01_KEU_EPISODE A

                        -- 🔥 mapping ruang rawat → ruang
                        LEFT JOIN SR01_MED_RUANG_PRWT RP
                            ON RP.RUANGRWT_ID = A.RUANGRWT_ID

                        -- 🔥 ruang → unit
                        LEFT JOIN SR01_MED_RUANG_MS R
                            ON R.RUANG_ID = RP.RUANG_ID

                        -- 🔥 unit master
                        LEFT JOIN SR01_GEN_UNIT_MS U
                            ON U.UNIT_ID = R.UNIT_ID

                        WHERE A.LOKASI_ID = '001'
                        AND A.AKTIF = '1'
                        AND A.JENIS_EPISODE = 'I'
                        AND A.PULANG_ID NOT IN ('P02','P04','P10','P11','P0X')
                        AND A.KELAS_ID <> 'V'

                        -- range H-14
                        AND A.TGL_KELUAR >= TRUNC(SYSDATE) - 14
                        AND A.TGL_KELUAR <  TRUNC(SYSDATE)

                        GROUP BY 
                            U.UNIT_ID,
                            U.NAMA_UNIT

                        HAVING SUM(CASE 
                                    WHEN TO_NUMBER(TO_CHAR(A.TGL_KELUAR,'HH24')) >= 12 
                                    THEN 1 ELSE 0 
                            END) > 0

                        ORDER BY SESUDAH_12 DESC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function registranaptoranap($periode){
            $query =
                    "
                        WITH RANAP AS (
                            SELECT *
                            FROM (
                                SELECT 
                                    T.*,
                                    ROW_NUMBER() OVER (
                                        PARTITION BY T.PASIEN_ID, T.EPISODE_ID
                                        ORDER BY T.CREATED_DATE ASC
                                    ) RN
                                FROM SR01_KEU_TRANSKMR_IT T
                                WHERE T.AKTIF = '1'
                                AND T.RUANG_ID NOT LIKE 'TRANSIT%'
                            )
                            WHERE RN = 1
                        ),

                        TRANSFER AS (
                            SELECT *
                            FROM (
                                SELECT 
                                    T.*,
                                    ROW_NUMBER() OVER (
                                        PARTITION BY T.PASIEN_ID, T.EPISODE_ID
                                        ORDER BY T.CREATED_DATE ASC
                                    ) RN
                                FROM SR01_MED_TRNSF_RUANG T
                                WHERE T.AKTIF = '1'
                            )
                            WHERE RN = 1
                        ),

                        SPRI AS (
                            SELECT *
                            FROM (
                                SELECT 
                                    T.*,
                                    ROW_NUMBER() OVER (
                                        PARTITION BY T.PASIEN_ID, T.EPISODE_ID
                                        ORDER BY T.CREATED_DATE ASC
                                    ) RN
                                FROM WEB_CO_MINTA_RANAP T
                                WHERE T.AKTIF = '1'
                            )
                            WHERE RN = 1
                        ),

                        DATA AS (
                            SELECT 
                                A.PASIEN_ID,
                                A.EPISODE_ID,
                                A.TGL_MASUK,
                                A.CREATED_DATE          AS REGISTRASI_IGD,
                                SP.CREATED_DATE         AS BUAT_SPRI,
                                RN.CREATED_DATE         AS REGISTRASI_RANAP,
                                TF.CREATED_DATE         AS BUAT_FORMTRANSFER

                            FROM SR01_KEU_EPISODE A

                            LEFT JOIN SPRI SP
                                ON SP.PASIEN_ID = A.PASIEN_ID
                                AND SP.EPISODE_ID = A.EPISODE_ID

                            LEFT JOIN RANAP RN
                                ON RN.PASIEN_ID = A.PASIEN_ID
                                AND RN.EPISODE_ID = A.EPISODE_ID

                            LEFT JOIN TRANSFER TF
                                ON TF.PASIEN_ID = A.PASIEN_ID
                                AND TF.EPISODE_ID = A.EPISODE_ID

                            WHERE A.LOKASI_ID = '001'
                            AND A.AKTIF = '1'
                            AND A.JENIS_EPISODE = 'I'
                            AND A.STATUS_EPISODE <> '99'
                            AND A.RUANGRWT_ID NOT LIKE 'TRANSIT%'
                            AND A.RUANGRWT_ID NOT LIKE 'KBY%'
                            AND A.RUANGRWT_ID NOT LIKE 'PERINA%'
                            AND A.RUANGRWT_ID NOT LIKE 'NICU%'
                            AND A.RUANGRWT_ID NOT LIKE 'PICU%'
                            AND A.RUANGRWT_ID NOT LIKE 'IC%'
                            AND A.RUANGRWT_ID NOT LIKE 'IC%'
                            AND TO_CHAR(A.TGL_MASUK,'YYYY') = '".$periode."'
                        )

                        SELECT 
                            TO_CHAR(TGL_MASUK,'MM') AS PERIODE,

                            COUNT(*) AS TOTAL_KASUS,

                            -- Numerator (< 60 menit)
                            SUM(
                                CASE 
                                    WHEN REGISTRASI_RANAP IS NOT NULL 
                                    AND BUAT_FORMTRANSFER IS NOT NULL
                                    AND (REGISTRASI_RANAP - BUAT_FORMTRANSFER) * 24 * 60 < 60
                                    THEN 1 
                                    ELSE 0 
                                END
                            ) AS MASUK_RANAP_KURANG_60_MENIT,

                            -- Persentase
                            ROUND(
                                SUM(
                                    CASE 
                                        WHEN REGISTRASI_RANAP IS NOT NULL 
                                        AND BUAT_FORMTRANSFER IS NOT NULL
                                        AND (REGISTRASI_RANAP - BUAT_FORMTRANSFER) * 24 * 60 < 60
                                        THEN 1 
                                        ELSE 0 
                                    END
                                ) / NULLIF(COUNT(*),0) * 100
                            ,2) AS MASUK_RANAP_KURANG_60_MENIT_PERSENTASE

                        FROM DATA
                        GROUP BY TO_CHAR(TGL_MASUK,'MM')
                        ORDER BY PERIODE

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }
        
    }
?>