<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modeldatatb extends CI_Model
{
    function periode(){
        $query =
                "
                    SELECT 
                        TO_CHAR(dt,'FMMonth YYYY','NLS_DATE_LANGUAGE=INDONESIAN') AS PERIODE,
                        TO_CHAR(dt, 'MM.YYYY') AS PERIODE_KEY
                    FROM (
                        SELECT ADD_MONTHS(DATE '2015-01-01', LEVEL-1) dt
                        FROM DUAL
                        CONNECT BY ADD_MONTHS(DATE '2015-01-01', LEVEL-1) <= TRUNC(SYSDATE, 'MM')
                    )
                    ORDER BY dt DESC
                ";

        $recordset = $this->db->query($query);
        $recordset = $recordset->result();
        return $recordset;
        }

    // Tambahkan parameter $tgl_awal dan $tgl_akhir
    public function datatb($tgl_awal, $tgl_akhir)
    {
        $query = "
                        WITH Lab_TCM AS (
                -- Mengambil hasil pemeriksaan TCM terakhir per episode
                SELECT * FROM (
                    SELECT 
                        S.REGISTRASI_ID,
                        TRUNC(S.CREATED_DATE) as TGL_PEMERIKSAAN,
                        CASE 
                            WHEN DT.HASIL_NOTE_ID IS NULL THEN TO_CHAR(DT.RESULT_VALUE)
                            ELSE (SELECT TRIM(REGEXP_REPLACE((SUBSTR(REGEXP_REPLACE(C.HASIL_TEKS, '(\{.\})|}|(\\\S+)', ''), 0, LENGTH(REGEXP_REPLACE(C.HASIL_TEKS, '(\{.\})|}|(\\\S+)', ''))-1)), '[[:space:]]{2,}', ' '))
                                FROM HASIL_NOTE C WHERE C.HASIL_NOTE_ID = DT.HASIL_NOTE_ID)
                        END as HASIL_TCM,
                        ROW_NUMBER() OVER (PARTITION BY S.REGISTRASI_ID ORDER BY DT.CREATED_DATE DESC) as RN
                    FROM DT_TES_ORDER DT
                    JOIN SAMPEL S ON DT.SAMPEL_ID = S.SAMPEL_ID
                    WHERE DT.TEST_ID = 'HSLTB' AND DT.SHOW_ITEM = '1' AND DT.DONE_STATUS = '04'
                ) WHERE RN = 1
            ),
            Hasil_Radiologi AS (
                -- Mengambil hasil toraks terakhir per episode
                SELECT * FROM (
                    SELECT 
                        J.NO_REGISTER,
                        DBMS_LOB.SUBSTR(J.EXPERTISE_TEXT_CONCLUSION, 4000, 1) as HASIL_TORAKS,
                        ROW_NUMBER() OVER (PARTITION BY J.NO_REGISTER ORDER BY J.RADIOLOG_DATETIME_START DESC) as RN
                    FROM RAD_MANAGER.RIS_OUT J
                    JOIN SR01_WORKLIST_RAD_DT K ON J.NO_REGISTER = K.EPISODE_ID AND J.NO_RONTGEN = K.TRANS_RAD
                    JOIN WEB_CO_RAD_DT L ON L.EPISODE_ID = K.EPISODE_ID AND L.TRANS_CO = K.TRANS_CO
                    WHERE K.AKTIF = '1' AND L.SHOW_ITEM = '1' AND L.TEST_ID = 'RAD010'
                ) WHERE RN = 1
            ),
            Diagnosa_Coding AS (
                -- Mengambil diagnosa coding ICD10 terakhir dengan logika Fallback ke IDRG
                SELECT * FROM (
                    SELECT 
                        R.EPISODE_ID,
                        R.DIAGNOSA as DIAG_CODING,
                        -- LOGIKA BARU: Jika I.KODE_ICD kosong, ambil IDRG.KODE_ICD
                        COALESCE(I.KODE_ICD, IDRG.KODE_ICD) as KODE_ICD,
                        ROW_NUMBER() OVER (PARTITION BY R.EPISODE_ID ORDER BY R.CREATED_DATE DESC) as RN
                    FROM SR01_RM_RESUME_ICD10 R
                    LEFT JOIN SR01_MED_ICD10_MS I ON R.ICD10_ID = I.KODE
                    -- Tambahan LEFT JOIN ke tabel IDRG berdasarkan pencocokan nama kolom KODE
                    LEFT JOIN SR01_MED_ICD_IDRG IDRG ON R.ICD10_ID = IDRG.KODE
                    WHERE R.JENIS = '1' AND R.JNS_R = 'F' AND R.AKTIF = '1'
                ) WHERE RN = 1
            ),
            OAT_Meds AS (
                -- Mengambil data OAT pertama kali diberikan (MIN)
                SELECT * FROM (
                    SELECT 
                        V.PASIEN_ID,
                        TRUNC(V.CREATED_DATE) as TANGGAL_MULAI_OBAT,
                        LISTAGG(V.NAMA_OBAT, ', ') WITHIN GROUP (ORDER BY V.NAMA_OBAT) OVER (PARTITION BY V.PASIEN_ID, V.CREATED_DATE) as PADUAN_OAT,
                        ROW_NUMBER() OVER (PARTITION BY V.PASIEN_ID ORDER BY V.CREATED_DATE ASC) as RN
                    FROM SR01_FRM_VALIDASI_IT V
                    WHERE V.SHOW_ITEM = '1' 
                    AND V.OBAT_ID IN ('OAT F0000000001','OAT F0000000002','OAT K0000000000','OAT K0000000001','OAT K0000000002','OAT K0000000004')
                ) WHERE RN = 1
            ),
            Assessment_Data AS (
                -- Mengambil Assessment SOAP terakhir
                SELECT * FROM (
                    SELECT 
                        K.EPISODE_ID,
                        K.A as ASSESSMENT_DOKTER,
                        ROW_NUMBER() OVER (PARTITION BY K.EPISODE_ID ORDER BY K.TRANS_SOAP DESC) as RN
                    FROM WEB_CO_DIAGNOSA_DT K
                    WHERE K.SHOW_ITEM = '1'
                ) WHERE RN = 1
            )
            SELECT 
                ' ' || A.EPISODE_ID, 
                B.INT_PASIEN_ID as NO_RM, A.TGL_MASUK as TGL_REGISTER, B.NAMA as NAMA_PASIEN, 
                ' ' || B.NO_IDENTITAS as NIK_KTP,
                DECODE(B.SEX_ID, 'L', 'LAKI-LAKI', 'PEREMPUAN') as JENIS_KELAMIN, B.TGL_LAHIR as TANGGAL_LAHIR,
                SR01_HITUNG_UMUR(B.TGL_LAHIR, A.TGL_MASUK) as UMUR, B.ALAMAT1 as ALAMAT_LENGKAP,
                
                -- Master Wilayah tetap subquery karena biasanya tabel kecil/fast lookup
                (SELECT K.KETERANGAN FROM SR01_GEN_KKK_MS K WHERE K.KKK_ID = B.PROPINSI_ID AND K.JENIS = '1') as PROPINSI,
                (SELECT K.KETERANGAN FROM SR01_GEN_KKK_MS K WHERE K.KKK_ID = B.KABUPATEN_ID AND K.JENIS = '2') as KABUPATEN,
                (SELECT K.KETERANGAN FROM SR01_GEN_KKK_MS K WHERE K.KKK_ID = B.KECAMATAN_ID AND K.JENIS = '3') as KECAMATAN,
                (SELECT K.KETERANGAN FROM SR01_GEN_KKK_MS K WHERE K.KKK_ID = B.KELURAHAN_ID AND K.JENIS = '4') as KELURAHAN,
                (SELECT P.KETERANGAN FROM SR01_MED_POLI_MS P WHERE P.POLI_ID = A.POLI_ID) as POLI,
                
                L.TGL_PEMERIKSAAN,
                L.HASIL_TCM,
                R.HASIL_TORAKS,
                C.DIAGNOSA as DIAGNOSA_DOKTER,
                DC.DIAG_CODING as DIAGNOSA_CODING,
                DC.KODE_ICD as ICD_KODE,
                O.TANGGAL_MULAI_OBAT,
                O.PADUAN_OAT,
                AD.ASSESSMENT_DOKTER

            FROM SR01_KEU_EPISODE A
            JOIN SR01_GEN_PASIEN_MS B ON A.PASIEN_ID = B.PASIEN_ID AND A.LOKASI_ID = B.LOKASI_ID
            JOIN WEB_CO_DIAGNOSA_MS C ON A.EPISODE_ID = C.EPISODE_ID AND A.PASIEN_ID = C.PASIEN_ID
            JOIN SR01_KEU_REKANAN_MS D ON A.REKANAN_ID = D.REKANAN_ID AND A.LOKASI_ID = D.LOKASI_ID
            JOIN SR01_MED_PRWT_TR E ON A.EPISODE_ID = E.EPISODE_ID AND A.PASIEN_ID = E.PASIEN_ID AND A.LOKASI_ID = E.LOKASI_ID
            JOIN SR01_MED_ANAMAWAL F ON E.TRANS_ID = F.TRANS_ID AND A.PASIEN_ID = F.PASIEN_ID

            -- Join data pendukung dari CTE
            LEFT JOIN Lab_TCM L ON A.EPISODE_ID = L.REGISTRASI_ID
            LEFT JOIN Hasil_Radiologi R ON A.EPISODE_ID = R.NO_REGISTER
            LEFT JOIN Diagnosa_Coding DC ON A.EPISODE_ID = DC.EPISODE_ID
            LEFT JOIN OAT_Meds O ON A.PASIEN_ID = O.PASIEN_ID
            LEFT JOIN Assessment_Data AD ON A.EPISODE_ID = AD.EPISODE_ID

            WHERE A.TGL_MASUK >= TO_DATE(?, 'DDMMYYYY') 
            AND A.TGL_MASUK < TO_DATE(?, 'DDMMYYYY')
            AND A.STATUS_EPISODE <> '99' 
            AND E.AKTIF = '1' 
            AND F.AKTIF = '1'
            AND C.SHOW_ITEM = '1'
            AND UPPER(C.DIAGNOSA) LIKE '%TB%'

            ORDER BY A.TGL_MASUK
        ";   
                        
        // Gunakan parameter array untuk binding "?" dan kembalikan result_array()
        $recordset = $this->db->query($query, array($tgl_awal, $tgl_akhir));
        return $recordset->result_array(); 
    }
}
?>