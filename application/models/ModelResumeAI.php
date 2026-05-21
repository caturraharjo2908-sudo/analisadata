<?php
    class ModelResumeAI extends CI_Model{

        function listrresume(){
            $query =
                    "
                        SELECT 
                            A.PASIEN_ID,
                            A.EPISODE_ID,
                            TO_CHAR(A.TGL_KELUAR,'DD.MM.YYYY HH24:MI:SS') AS TGLKELUAR,
                            A.DOKTER_ID
                        FROM SR01_KEU_EPISODE A
                        WHERE A.LOKASI_ID      = '001'
                        AND   A.AKTIF          = '1'
                        AND   A.JENIS_EPISODE  = 'I'
                        AND   A.STATUS_EPISODE = '55'
                        AND   A.TGL_KELUAR IS NOT NULL
                        -- AND   A.TGL_KELUAR >= TRUNC(SYSDATE)
                        AND A.EPISODE_ID IN (

'126043100816',
'126043100115',
'126033090680',
'126043101180',
'126043104530',
'126043105207',
'126043105412',
'126043094217',
'126043104729',
'126043106527',
'126033090991',
'126043094141',
'126043101318',
'126043105393',
'126043105592',
'126043105206',
'126043105377',
'126043094349',
'126043094176',
'126043101443',
'126043105275',
'126043105218',
'126043105398',
'126043105306',
'126043093186',
'126043104272',
'126043093715',
'126043105414',
'126043106634',
'126043105416',
'126043107302',
'126043107494',
'126043105442',
'126043107347',
'126043109066',
'126043105215',
'126043107338',
'126043106700',
'126043105551',
'126043105247',
'126043108177',
'126043108944',
'126043107531',
'126043114169',
'126043107556',
'126043106291',
'126043107486',
'126043104966',
'126043108539',
'126043114001',
'126043112918',
'126043113441',
'126043114196',
'126043105824',
'126043113828',
'126043114182',
'126043107308',
'126043105041',
'126043113622',
'126043106944',
'126043107563',
'126043112128',
'126043107258',
'126043108926',
'126043105911',
'126043107663',
'126043109071',
'126043114644',
'126043113119',
'126043112601',
'126043107292',
'126043105226',
'126043105593',
'126043112669',
'126043113085',
'126043105841',
'126043113115',
'126043105389',
'126043113389',
'126043109121',
'126043113170',
'126043108987',
'126043116402',
'126043117861',
'126043113303',
'126043117390',
'126043117777',
'126043107227',
'126043114642',
'126043107595',
'126043113982',
'126043116875',
'126043114409',
'126043108952',
'126043112263',
'126043116905',
'126043113777',
'126043108919',
'126043113574',
'126043113037',
'126043117526',
'126043117757',
'126043117882',
'126043114347',
'126043105770',
'126043117385',
'126043112090',
'126043119977',
'126043120556',
'126043105840',
'126043113246',
'126043114344',
'126043112465',
'126043109060',
'126043112890',
'126043113970',
'126043119347',
'126043113176',
'126043114637',
'126043106350',
'126043108365',
'126043109120',
'126043123345',
'126043129015',
'126043123493',
'126053131365',
'126043130214',
'126043123648',
'126043127314',
'126043130341',
'126053131403',
'126043123777',
'126043119557',
'126043120561',
'126043120002',
'126043117364',
'126043130226',
'126043123544',
'126043123695',
'126043123369',
'126043128005',
'126043120249',
'126043123708',
'126043117864',
'126043129454',
'126043119898',
'126043120770',
'126043113029',
'126043130062',
'126043117896',
'126043120625',
'126043128990',
'126043114285',
'126053131988',
'126043128952',
'126043130418',
'126053131269',
'126043123705',
'126043123729',
'126053131860',
'126053134266',
'126043117379',
'126043128749',
'126043123509',
'126043123435'

)
                        AND NOT EXISTS (
                            SELECT 1
                            FROM WEB_CO_RESUME_RANAP_AI B
                            WHERE B.EPISODE_ID = A.EPISODE_ID
                        )
                        ORDER BY A.TGL_KELUAR DESC
                        FETCH FIRST 10 ROWS ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function kunjungan($episodeid){
            $query =
                    "
                        SELECT X.*,
                            (SELECT KETERANGAN FROM SR01_MED_POLI_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND POLI_ID=X.POLIID)POLIKLINIK,
                            CASE
                                WHEN X.ASALSPRI = 'A001' THEN
                                'POLI'
                                ELSE
                                CASE
                                    WHEN X.RUANGRWT_ID LIKE 'KBY%' OR (X.RUANGRWT_ID LIKE 'PERINA%' AND X.RUANGIDFIRST LIKE 'KBY%') THEN
                                    'BAYI'
                                    ELSE
                                    CASE
                                        WHEN X.RUANGIDFIRST LIKE 'NICU%' AND X.RUANGRWT_ID LIKE 'PERINA%' THEN
                                        'NPP'
                                        ELSE
                                        'NORMAL'
                                    END
                                END
                            END STATUSJENIS
                        FROM(
                            SELECT A.PASIEN_ID, EPISODE_ID, PULANG_ID, DOKTER_ID, STATUS_EPISODE, RUANGRWT_ID,
                                GETPIDINT(A.PASIEN_ID)MRPASIEN,
                                SR01_GET_SUFFIX(A.PASIEN_ID)NAMAPSIEN,
                                TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY')TGLMASUK, TO_CHAR(A.TGL_KELUAR,'DD.MM.YYYY HH24:MI:SS')TGLKELUAR,
                                (SELECT KETERANGAN  FROM SR01_MED_MSKKLR_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND KATEGORI_ID='MP' AND MSKKLR_ID=A.PULANG_ID)CARAPULANG,
                                (SELECT NAMA        FROM SR01_MED_DOKTER_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND DOKTER_ID=A.DOKTER_ID)NAMADOKTER,
                                (SELECT DEF_POLI_ID FROM SR01_MED_DOKTER_MS WHERE LOKASI_ID='001' AND AKTIF='1' AND DOKTER_ID=A.DOKTER_ID)POLIID,
                                (SELECT RUANG_ID    FROM SR01_KEU_TRANSKMR_IT WHERE LOKASI_ID='001' AND AKTIF='1' AND PASIEN_ID=A.PASIEN_ID AND EPISODE_ID=A.EPISODE_ID ORDER BY CREATED_DATE ASC FETCH FIRST 1 ROW ONLY)RUANGIDFIRST,
                                (SELECT ASAL_POLI   FROM WEB_CO_MINTA_RANAP WHERE LOKASI_ID='001' AND AKTIF='1' AND PASIEN_ID=A.PASIEN_ID AND EPISODE_ID=A.EPISODE_ID ORDER BY CREATED_DATE ASC FETCH FIRST 1 ROW ONLY)ASALSPRI,
                                (SELECT POLI_ID     FROM WEB_CO_MINTA_RANAP WHERE LOKASI_ID='001' AND AKTIF='1' AND PASIEN_ID=A.PASIEN_ID AND EPISODE_ID=A.EPISODE_ID ORDER BY CREATED_DATE ASC FETCH FIRST 1 ROW ONLY)POLIIDLAST
                                
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

        function diagnosa($episodeid){
            $query =
                    "
                        SELECT A.ICD10, DIAGNOSA
                        FROM WEB_CO_DIAGNOSA_MS A
                        WHERE A.LOKASI_ID='001'
                        AND   A.SHOW_ITEM='1'
                        AND   A.EPISODE_ID='".$episodeid."'
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result_array();
            return $recordset;
        }

        function keluhanutama($episodeid){
            $query =
                    "
                        SELECT A.CREATED_DATE, A.S, A.A, A.O, A.S2, A.S3, A.P
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.POLI_ID='UGD01'
                        AND   A.CREATED_BY LIKE 'DR%'
                        ORDER BY A.CREATED_DATE DESC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function keluhanutamapoli($poliid,$pasienid){
            $query =
                    "
                        SELECT A.CREATED_DATE, A.S, A.A, A.O, A.S2, A.S3, A.P
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.PASIEN_ID = '".$pasienid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.POLI_ID='".$poliid."'
                        ORDER BY A.CREATED_DATE DESC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function keluhanutamaranappoli($episodeid){
            $query =
                    "
                        SELECT A.CREATED_DATE, A.S, A.A, A.O, A.S2, A.S3, A.P
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.RUANG_ID IS NOT NULL
                        ORDER BY A.CREATED_DATE ASC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function keluhanutamabayibarulahir($episodeid){
            $query =
                    "
                        SELECT A.CREATED_DATE, A.S, A.A, A.O, A.S2, A.S3, A.P
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.RUANG_ID='KBY'
                        ORDER BY A.CREATED_DATE ASC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function keluhanutamabayibarulahirdokter($episodeid){
            $query =
                    "
                        SELECT A.CREATED_DATE, A.S, A.A, A.O, A.S2, A.S3, A.P
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.RUANG_ID='KBY'
                        AND   A.CREATED_BY LIKE 'DR%'
                        ORDER BY A.CREATED_DATE ASC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function keluhanutamabayibarulahirnicu($episodeid){
            $query =
                    "
                        SELECT A.CREATED_DATE, A.S, A.A, A.O, A.S2, A.S3, A.P
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.RUANG_ID='NICU'
                        ORDER BY A.CREATED_DATE ASC
                        FETCH FIRST 1 ROW ONLY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->row();
            return $recordset;
        }

        function keluhanutamabayibarulahirnicudokter($episodeid){
            $query =
                    "
                        SELECT A.CREATED_DATE, A.S, A.A, A.O, A.S2, A.S3, A.P
                        FROM WEB_CO_DIAGNOSA_DT A
                        WHERE A.EPISODE_ID = '".$episodeid."'
                        AND   A.FLAG_HAPUS = '1'
                        AND   A.SHOW_ITEM = '1'
                        AND   A.RUANG_ID='NICU'
                        AND   A.CREATED_BY LIKE 'DR%'
                        ORDER BY A.CREATED_DATE ASC
                        FETCH FIRST 1 ROW ONLY
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

        function radiologi($episodeid){
            $query =
                    "
                        SELECT 
                            TO_CHAR(A.RADIOLOG_DATETIME_END,'DD.MM.YYYY HH24:MI:SS') AS CREATEDDATE,

                            CASE 
                                -- ? Jika ada KESAN
                                WHEN REGEXP_LIKE(DBMS_LOB.SUBSTR(A.EXPERTISE_TEXT_CONCLUSION, 4000, 1), 'KESAN', 'i') 
                                THEN REGEXP_SUBSTR(
                                        DBMS_LOB.SUBSTR(A.EXPERTISE_TEXT_CONCLUSION, 4000, 1),
                                        'KESAN\s*:?\s*(.*)',
                                        1, 1, 'i', 1
                                    )

                                -- ? Jika tidak ada ? ambil SEMUA bullet
                                ELSE REGEXP_SUBSTR(
                                        DBMS_LOB.SUBSTR(A.EXPERTISE_TEXT_CONCLUSION, 4000, 1),
                                        '(-\s*.*(' || CHR(10) || '-\s*.*)*)',
                                        1, 1
                                    )
                            END AS RESULT,

                            (SELECT NAMA_PEMERIKSAAN 
                            FROM RAD_MANAGER.RIS_IN 
                            WHERE ID = A.ID) AS NAMAPEMERIKSAAN

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

        function laboratoriumdt($sampelid,$norm){
            $query =
                    "
                        SELECT A.*,
                        TO_CHAR(A.CREATED_DATE, 'DD.MM.YYYY') TANGGAL_LAB
                        FROM(
                            SELECT 
                            SUBSTR(LPAD(' ',((ITEM_LEVEL-2)*2))||C.NAME1,1,400) AS NAMA_TES, D.TEST_ID,
                            C.UNITS, C.RESULT_ID, D.HASIL_NOTE_ID,
                            CASE 
                            WHEN C.TEST_ID = 'KRBMU4' THEN 
                            DECODE( (SELECT SUBSTR(REGEXP_REPLACE(E.HASIL_TEKS, '(\{.\})|}|(\\\S+)', ''),3, LENGTH(REGEXP_REPLACE(E.HASIL_TEKS, '(\{.\})|}|(\\\S+)', ''))-5) FROM HASIL_NOTE E WHERE E.HASIL_NOTE_ID = D.HASIL_NOTE_ID),
                                '',
                                E.KETERANGAN,
                                (SELECT SUBSTR(REGEXP_REPLACE(E.HASIL_TEKS, '(\{.\})|}|(\\\S+)', ''),3, LENGTH(REGEXP_REPLACE(E.HASIL_TEKS, '(\{.\})|}|(\\\S+)', ''))-5) FROM HASIL_NOTE E WHERE E.HASIL_NOTE_ID = D.HASIL_NOTE_ID)
                            )
                            ELSE 
                                DECODE(D.HASIL_NOTE_ID,
                                '',
                                DECODE(C.RESULT_ID,
                                                '1',
                                                D.SIGN||D.RESULT_VALUE||' ('||
                                                SUBSTR(
                                                    GET_INTERPRETASI_AMBANG_BATAS(
                                                        D.DEMOGRAPHIC_ID, D.SAMPEL_ID, D.TEST_ID, D.REFERENCE_ID, D.REFERENCE_ID_AGE, D.RESULT_VALUE, D.SIGN
                                                    ),1,15
                                                )||')',
                                                D.SIGN||D.RESULT_VALUE
                                                ),
                                (SELECT SUBSTR(REGEXP_REPLACE(E.HASIL_TEKS, '(\{.*\})|}|(\\\S+)', ''),3,
                                        LENGTH(REGEXP_REPLACE(E.HASIL_TEKS, '(\{.*\})|}|(\\\S+)', ''))-5)
                                FROM HASIL_NOTE E
                                WHERE E.HASIL_NOTE_ID = D.HASIL_NOTE_ID)
                            )
                            END AS RESULT_VALUE,  --khusus Hasil Pembiakan, jika hasil_note kosong lihat dt_hasil_biakan, kalau gak kosong lihat hasil_note
                            D.RESULT_FLAG,
                            D.FLAG,
                            D.DONE_STATUS,
                            D.SAMPEL_ID,
                            D.PASIEN_ID,
                            D.CREATED_DATE,
                            SUBSTR(GETNORMAL_STATUS(C.RESULT_ID,  D.REFERENCE_ID, D.REFERENCE_ID_AGE, D.TEST_ID, D.PASIEN_ID),1,100) AS ANGKA_NORMAL, TO_CHAR(C.SORT_PRIORITY) SORT_PRIORITY,
                            1 URUT
                            FROM MS_TEST_LAB C
                            LEFT JOIN DT_TES_ORDER D ON D.TEST_ID = C.TEST_ID AND D.SAMPEL_ID = '".$sampelid."' AND D.PASIEN_ID = '10-".$norm."' AND TO_NUMBER(D.DONE_STATUS) = 4
                            LEFT JOIN DT_HASIL_BIAKAN B ON B.SAMPEL_ID = D.SAMPEL_ID AND C.TEST_ID = 'KRBMU4'
                            LEFT JOIN DT_KUMAN E ON E.DT_KUMAN_ID = B.DT_KUMAN_ID AND E.MS_KUMAN_ID = B.MS_KUMAN_ID
                            WHERE C.TEST_ID IN ( SELECT DISTINCT(A.TEST_ID)
                                FROM MS_TEST_LAB A 
                                START WITH A.TEST_ID IN (SELECT TEST_ID FROM DT_TES_ORDER WHERE SAMPEL_ID = '".$sampelid."' AND SHOW_ITEM='1' AND TO_NUMBER(DONE_STATUS) = 4)
                                CONNECT BY PRIOR A.OWNER=A.TEST_ID)
                                AND C.SHOW_ITEM='1'             
                                AND C.OWNER <> 'Master Tes'
                            UNION
                            SELECT DECODE(A.TEST_ID, NULL, A.NAMA_TEST, '    '||A.NAMA_TEST) NAMA_TEST, A.TEST_LAB_ID TEST_ID, A.SATUAN UNITS, NULL RESULT_ID, NULL HASIL_NOTE_ID, 
                            CASE
                                WHEN A.TEST_LAB_ID = 'KRBMU4' THEN C.KUMAN
                                ELSE
                                A.HASIL
                            END RESULT_VALUE
                            , A.FLAG RESULT_FLAG, NULL FLAG, NULL DONE_STATUS, A.SAMPEL_ID, NULL PASIEN_ID, B.ACC_DATE CREATED_DATE, A.NILAI_NORMAL ANGKA_NORMAL, DECODE(A.RESERVE4, NULL, '0', A.RESERVE4) SORT_PRIORITY, 2 URUT
                            FROM WEB_CO_HASIL_LAB_DT A
                            LEFT JOIN WEB_CO_HASIL_LAB_ST C ON C.EPISODE_ID = A.EPISODE_ID AND C.SAMPEL_ID = A.SAMPEL_ID --AND A.TEST_ID = 'KRBMU4'
                            , WEB_CO_HASIL_LAB_HD B
                            WHERE A.AKTIF = '1' AND B.AKTIF = '1'
                            AND A.SAMPEL_ID = '".$sampelid."'
                            AND A.EPISODE_ID = B.EPISODE_ID
                            AND A.TRANS_CO = B.TRANS_CO
                            AND A.SAMPEL_ID = B.SAMPEL_ID
                        )A
                        ORDER BY A.URUT, A.SORT_PRIORITY
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result_array();
            return $recordset;
        }

        function cekdata($episodeid){
            $query =
                    "
                        SELECT A.EPISODE_ID
                        FROM WEB_CO_RESUME_RANAP_AI A
                        WHERE A.EPISODE_ID='".$episodeid."'
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function updateresume($episodeid,$data){           
            $sql =   $this->db->update("WEB_CO_RESUME_RANAP_AI",$data,array("EPISODE_ID"=>$episodeid));
            return $sql;
        }

        function insertresume($data){           
            $sql =   $this->db->insert("WEB_CO_RESUME_RANAP_AI",$data);
            return $sql;
        }

    }
?>