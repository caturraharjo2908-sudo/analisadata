<?php
    class Modeltopdiagnosa extends CI_Model{

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

        function datarjgeriatri($periode){
            $query = "
                        WITH ICD AS (
                            SELECT 
                                R.EPISODE_ID,
                                CASE 
                                    WHEN R.ICD10_ID = 'Z09.8' THEN (
                                        SELECT R2.ICD10_ID
                                        FROM SR01_RM_RESUME_ICD10 R2
                                        WHERE R2.LOKASI_ID='001'
                                        AND R2.AKTIF='1'
                                        AND R2.JENIS='2'
                                        AND R2.URUT='1'
                                        AND R2.JNS_R='F'
                                        AND R2.EPISODE_ID = R.EPISODE_ID
                                        FETCH FIRST 1 ROW ONLY
                                    )
                                    ELSE R.ICD10_ID
                                END AS ICD10ID,
                                ROW_NUMBER() OVER (
                                    PARTITION BY R.EPISODE_ID 
                                    ORDER BY R.CREATED_DATE DESC
                                ) RN
                            FROM SR01_RM_RESUME_ICD10 R
                            WHERE R.LOKASI_ID='001'
                            AND   R.AKTIF='1'
                            AND   R.JENIS='1'
                            AND   R.JNS_R='F'
                        ),

                        DATA AS (
                            SELECT 
                                CASE 
                                    WHEN ICD.ICD10ID LIKE 'D%' THEN 
                                        (SELECT M.KODE_ICD 
                                        FROM SR01_MED_ICD10_MS M 
                                        WHERE M.KODE = ICD.ICD10ID)
                                    ELSE 
                                        ICD.ICD10ID
                                END AS ICD10PRIMARY,

                                SR01_HITUNG_UMURDLMTHN(
                                    (SELECT TRUNC(TGL_LAHIR) 
                                    FROM SR01_GEN_PASIEN_MS 
                                    WHERE PASIEN_ID = A.PASIEN_ID),
                                    TRUNC(A.TGL_MASUK)
                                ) AS UMUR

                            FROM SR01_KEU_EPISODE A
                            LEFT JOIN ICD 
                                ON ICD.EPISODE_ID = A.EPISODE_ID 
                                AND ICD.RN = 1
                            WHERE A.LOKASI_ID='001'
                            AND   A.AKTIF='1'
                            AND   A.JENIS_EPISODE='O'
                            AND   A.STATUS_EPISODE='55'
                            AND   A.POLI_ID NOT IN ('UGD01','UGD02')
                            AND   EXTRACT(YEAR FROM A.TGL_MASUK) = ".$periode."
                            AND   ICD.ICD10ID IS NOT NULL

                            -- 🔥 EXCLUDE Z & R
                            AND ICD.ICD10ID NOT LIKE 'Z%'
                            AND ICD.ICD10ID NOT LIKE 'R%'

                            -- 🔥 FILTER GERIATRI
                            AND SR01_HITUNG_UMURDLMTHN(
                                    (SELECT TRUNC(TGL_LAHIR) 
                                    FROM SR01_GEN_PASIEN_MS 
                                    WHERE PASIEN_ID = A.PASIEN_ID),
                                    TRUNC(A.TGL_MASUK)
                                ) >= 60
                        )

                        SELECT 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION,
                            COUNT(*) AS JUMLAH
                        FROM DATA D
                        LEFT JOIN SR01_MED_ICD_IDRG I
                            ON I.KODE_ICD = D.ICD10PRIMARY

                        WHERE D.ICD10PRIMARY NOT LIKE 'Z%'
                        AND   D.ICD10PRIMARY NOT LIKE 'R%'

                        GROUP BY 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION

                        ORDER BY JUMLAH DESC
                        FETCH FIRST 10 ROWS ONLY
            ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datarj($periode){
            $query = "
                        WITH ICD AS (
                            SELECT 
                                R.EPISODE_ID,
                                CASE 
                                    WHEN R.ICD10_ID = 'Z09.8' THEN (
                                        SELECT R2.ICD10_ID
                                        FROM SR01_RM_RESUME_ICD10 R2
                                        WHERE R2.LOKASI_ID='001'
                                        AND R2.AKTIF='1'
                                        AND R2.JENIS='2'
                                        AND R2.URUT='1'
                                        AND R2.JNS_R='F'
                                        AND R2.EPISODE_ID = R.EPISODE_ID
                                        FETCH FIRST 1 ROW ONLY
                                    )
                                    ELSE R.ICD10_ID
                                END AS ICD10ID,
                                ROW_NUMBER() OVER (
                                    PARTITION BY R.EPISODE_ID 
                                    ORDER BY R.CREATED_DATE DESC
                                ) RN
                            FROM SR01_RM_RESUME_ICD10 R
                            WHERE R.LOKASI_ID='001'
                            AND   R.AKTIF='1'
                            AND   R.JENIS='1'
                            AND   R.JNS_R='F'
                        ),

                        DATA AS (
                            SELECT 
                                CASE 
                                    WHEN ICD.ICD10ID LIKE 'D%' THEN 
                                        (SELECT M.KODE_ICD 
                                        FROM SR01_MED_ICD10_MS M 
                                        WHERE M.KODE = ICD.ICD10ID)
                                    ELSE 
                                        ICD.ICD10ID
                                END AS ICD10PRIMARY
                            FROM SR01_KEU_EPISODE A
                            LEFT JOIN ICD 
                                ON ICD.EPISODE_ID = A.EPISODE_ID 
                                AND ICD.RN = 1
                            WHERE A.LOKASI_ID='001'
                            AND   A.AKTIF='1'
                            AND   A.JENIS_EPISODE='O'
                            AND   A.STATUS_EPISODE='55'
                            AND   A.POLI_ID NOT IN ('UGD01','UGD02')
                            AND   EXTRACT(YEAR FROM A.TGL_MASUK) = ".$periode."
                            AND   ICD.ICD10ID IS NOT NULL

                            -- 🔥 EXCLUDE Z & R
                            AND ICD.ICD10ID NOT LIKE 'Z%'
                            AND ICD.ICD10ID NOT LIKE 'R%'
                        )

                        SELECT 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION,
                            COUNT(*) AS JUMLAH
                        FROM DATA D
                        LEFT JOIN SR01_MED_ICD_IDRG I
                            ON I.KODE_ICD = D.ICD10PRIMARY

                        -- 🔥 safety filter (final)
                        WHERE D.ICD10PRIMARY NOT LIKE 'Z%'
                        AND   D.ICD10PRIMARY NOT LIKE 'R%'

                        GROUP BY 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION

                        ORDER BY JUMLAH DESC
                        FETCH FIRST 10 ROWS ONLY
            ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function dataigd($periode){
            $query = "
                        WITH ICD AS (
                            SELECT 
                                R.EPISODE_ID,
                                CASE 
                                    WHEN R.ICD10_ID = 'Z09.8' THEN (
                                        SELECT R2.ICD10_ID
                                        FROM SR01_RM_RESUME_ICD10 R2
                                        WHERE R2.LOKASI_ID='001'
                                        AND R2.AKTIF='1'
                                        AND R2.JENIS='2'
                                        AND R2.URUT='1'
                                        AND R2.JNS_R='F'
                                        AND R2.EPISODE_ID = R.EPISODE_ID
                                        FETCH FIRST 1 ROW ONLY
                                    )
                                    ELSE R.ICD10_ID
                                END AS ICD10ID,
                                ROW_NUMBER() OVER (
                                    PARTITION BY R.EPISODE_ID 
                                    ORDER BY R.CREATED_DATE DESC
                                ) RN
                            FROM SR01_RM_RESUME_ICD10 R
                            WHERE R.LOKASI_ID='001'
                            AND   R.AKTIF='1'
                            AND   R.JENIS='1'
                            AND   R.JNS_R='F'
                        ),

                        DATA AS (
                            SELECT 
                                CASE 
                                    WHEN ICD.ICD10ID LIKE 'D%' THEN 
                                        (SELECT M.KODE_ICD 
                                        FROM SR01_MED_ICD10_MS M 
                                        WHERE M.KODE = ICD.ICD10ID)
                                    ELSE 
                                        ICD.ICD10ID
                                END AS ICD10PRIMARY
                            FROM SR01_KEU_EPISODE A
                            LEFT JOIN ICD 
                                ON ICD.EPISODE_ID = A.EPISODE_ID 
                                AND ICD.RN = 1
                            WHERE A.LOKASI_ID='001'
                            AND   A.AKTIF='1'
                            AND   A.JENIS_EPISODE='O'
                            AND   A.STATUS_EPISODE='55'
                            AND   A.POLI_ID IN ('UGD01','UGD02')
                            AND   EXTRACT(YEAR FROM A.TGL_MASUK) = ".$periode."
                            AND   ICD.ICD10ID IS NOT NULL

                            -- 🔥 EXCLUDE Z & R
                            AND ICD.ICD10ID NOT LIKE 'Z%'
                            AND ICD.ICD10ID NOT LIKE 'R%'
                        )

                        SELECT 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION,
                            COUNT(*) AS JUMLAH
                        FROM DATA D
                        LEFT JOIN SR01_MED_ICD_IDRG I
                            ON I.KODE_ICD = D.ICD10PRIMARY

                        -- 🔥 safety filter (final)
                        WHERE D.ICD10PRIMARY NOT LIKE 'Z%'
                        AND   D.ICD10PRIMARY NOT LIKE 'R%'

                        GROUP BY 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION

                        ORDER BY JUMLAH DESC
                        FETCH FIRST 10 ROWS ONLY
            ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datari($periode){
            $query = "
                        WITH ICD AS (
                            SELECT 
                                R.EPISODE_ID,
                                CASE 
                                    WHEN R.ICD10_ID = 'Z09.8' THEN (
                                        SELECT R2.ICD10_ID
                                        FROM SR01_RM_RESUME_ICD10 R2
                                        WHERE R2.LOKASI_ID='001'
                                        AND R2.AKTIF='1'
                                        AND R2.JENIS='2'
                                        AND R2.URUT='1'
                                        AND R2.JNS_R='F'
                                        AND R2.EPISODE_ID = R.EPISODE_ID
                                        FETCH FIRST 1 ROW ONLY
                                    )
                                    ELSE R.ICD10_ID
                                END AS ICD10ID,
                                ROW_NUMBER() OVER (
                                    PARTITION BY R.EPISODE_ID 
                                    ORDER BY R.CREATED_DATE DESC
                                ) RN
                            FROM SR01_RM_RESUME_ICD10 R
                            WHERE R.LOKASI_ID='001'
                            AND   R.AKTIF='1'
                            AND   R.JENIS='1'
                            AND   R.JNS_R='F'
                        ),

                        DATA AS (
                            SELECT 
                                CASE 
                                    WHEN ICD.ICD10ID LIKE 'D%' THEN 
                                        (SELECT M.KODE_ICD 
                                        FROM SR01_MED_ICD10_MS M 
                                        WHERE M.KODE = ICD.ICD10ID)
                                    ELSE 
                                        ICD.ICD10ID
                                END AS ICD10PRIMARY
                            FROM SR01_KEU_EPISODE A
                            LEFT JOIN ICD 
                                ON ICD.EPISODE_ID = A.EPISODE_ID 
                                AND ICD.RN = 1
                            WHERE A.LOKASI_ID='001'
                            AND   A.AKTIF='1'
                            AND   A.JENIS_EPISODE='I'
                            AND   A.STATUS_EPISODE='55'
                            AND   EXTRACT(YEAR FROM A.TGL_MASUK) = ".$periode."
                            AND   ICD.ICD10ID IS NOT NULL

                            -- 🔥 EXCLUDE Z & R
                            AND ICD.ICD10ID NOT LIKE 'Z%'
                            AND ICD.ICD10ID NOT LIKE 'R%'
                        )

                        SELECT 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION,
                            COUNT(*) AS JUMLAH
                        FROM DATA D
                        LEFT JOIN SR01_MED_ICD_IDRG I
                            ON I.KODE_ICD = D.ICD10PRIMARY

                        -- 🔥 safety filter (final)
                        WHERE D.ICD10PRIMARY NOT LIKE 'Z%'
                        AND   D.ICD10PRIMARY NOT LIKE 'R%'

                        GROUP BY 
                            D.ICD10PRIMARY,
                            I.DESCRIPTION

                        ORDER BY JUMLAH DESC
                        FETCH FIRST 10 ROWS ONLY
            ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }



    }
?>