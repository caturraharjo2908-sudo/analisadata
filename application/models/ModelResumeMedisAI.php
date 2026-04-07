<?php
    class ModelResumeMedisAI extends CI_Model{

        function kunjungan($episodeid){
            $query =
                    "
                        SELECT X.*,
                            (SELECT KETERANGAN FROM SR01_MED_POLI_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND POLI_ID=X.POLIID)POLIKLINIK
                        FROM(
                            SELECT A.PASIEN_ID, EPISODE_ID, PULANG_ID,
                                TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY')TGLMASUK, TO_CHAR(A.TGL_KELUAR,'DD.MM.YYYY')TGLKELUAR,
                                (SELECT KETERANGAN  FROM SR01_MED_MSKKLR_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND KATEGORI_ID='MP' AND MSKKLR_ID=A.PULANG_ID)CARAPULANG,
                                (SELECT NAMA        FROM SR01_MED_DOKTER_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND DOKTER_ID=A.DOKTER_ID)NAMADOKTER,
                                (SELECT DEF_POLI_ID FROM SR01_MED_DOKTER_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND DOKTER_ID=A.DOKTER_ID)POLIID
                                
                            FROM SR01_KEU_EPISODE A
                            WHERE A.LOKASI_ID='001'
                            AND   A.AKTIF='1'
                            AND   A.JENIS_EPISODE='I'
                            AND   A.EPISODE_ID='".$episodeid."'
                        )X
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function obat($episodeid){
            $query =
                    "
                        SELECT DISTINCT A.OBAT_ID, NAMA_OBAT, JENIS_RESEP
                        FROM WEB_CO_RESEP_DT A
                        WHERE A.LOKASI_ID='001'
                        AND   A.SHOW_ITEM='1'
                        AND   A.EPISODE_ID='".$episodeid."'
                        ORDER BY JENIS_RESEP, NAMA_OBAT ASC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result_array();
            return $recordset;
        }

        function keluhanutama($episodeid){
            $query =
                    "
                        SELECT A.S KELUHAN, A INDIKASIRANAP, TO_CHAR(A.CREATED_DATE,'DD.MM.YYYY HH24:MI:SS')CREATEDDATE
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.POLI_ID='UGD01'
                        AND   A.S IS NOT NULL
                        AND   A.CREATED_BY LIKE 'DR%'
                        ORDER BY A.CREATED_DATE DESC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function pemeriksaanfisik($episodeid){
            $query =
                    "
                        SELECT DBMS_LOB.SUBSTR(A.O, 4000, 1) AS TEXT_DATA
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.POLI_ID = 'UGD01'
                        AND   A.O IS NOT NULL
                        AND   A.CREATED_BY LIKE 'DR%'
                        ORDER BY A.CREATED_DATE DESC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function gejala($episodeid){
            $query =
                    "
                        SELECT A.S2 RESULT, TO_CHAR(A.CREATED_DATE,'DD.MM.YYYY HH24:MI:SS')CREATEDDATE
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.POLI_ID='UGD01'
                        AND   A.S2 IS NOT NULL
                        AND   A.CREATED_BY LIKE 'DR%'
                        ORDER BY A.CREATED_DATE DESC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function radiologi($episodeid){
            $query =
                    "
                        SELECT TO_CHAR(A.RADIOLOG_DATETIME_END,'DD.MM.YYYY HH24:MI:SS')CREATEDDATE,
                        REGEXP_SUBSTR(
                            DBMS_LOB.SUBSTR(A.EXPERTISE_TEXT_CONCLUSION, 4000, 1),
                            'KESAN\s*:?\s*(.*)',
                            1, 1, 'i', 1
                        ) AS RESULT,
                        (SELECT NAMA_PEMERIKSAAN FROM RAD_MANAGER.RIS_IN WHERE ID=A.ID)NAMAPEMERIKSAAN

                        FROM RAD_MANAGER.RIS_OUT A
                        WHERE A.NO_REGISTER = '".$episodeid."'
                        AND   A.KODE_RADIOLOG IS NOT NULL
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result_array();
            return $recordset;
        }

        function laboratoriumhd($episodeid){
            $query =
                    "
                        SELECT A.*
                        FROM(
                            SELECT A.SAMPEL_ID, A.PASIEN_ID, A.REGISTRASI_ID, TO_CHAR(A.CREATED_DATE,'DD.MM.YYYY HH24:MI:SS') CREATEDDATE
                            FROM SAMPEL A
                            WHERE A.REGISTRASI_ID = '".$episodeid."'

                            UNION

                            SELECT A.SAMPEL_ID, A.PASIEN_ID, A.EPISODE_ID REGISTRASI_ID, TO_CHAR(A.ACC_DATE,'DD.MM.YYYY HH24:MI:SS') CREATEDDATE
                            FROM WEB_CO_HASIL_LAB_HD A
                            WHERE A.EPISODE_ID = '".$episodeid."'
                            AND A.AKTIF = '1'
                        )A
                        ORDER BY A.SAMPEL_ID
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result_array();
            return $recordset;
        }
    }
?>