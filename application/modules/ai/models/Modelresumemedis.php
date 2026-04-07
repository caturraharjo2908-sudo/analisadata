<?php
    class Modelresumemedis extends CI_Model{

        function listpasien($dokterid){
            $query =
                    "
                        SELECT
                            E.EPISODE_ID, P.PASIEN_ID, TO_CHAR(E.TGL_MASUK, 'DD.MM.YYYY') TGL_MASUK, TO_CHAR(E.TGL_KELUAR, 'DD.MM.YYYY') TGL_KELUAR,
                            E.RUANGRWT_ID, P.INT_PASIEN_ID, SR01_GET_SUFFIX(P.PASIEN_ID) NAMA, P.NAMA_AKHIR, TO_CHAR(P.TGL_LAHIR, 'DD.MM.YYYY') TGL_LAHIR,
                            HITUNG_UMUR(P.TGL_LAHIR, TRUNC(SYSDATE))UMUR,
                            (SELECT G.KETERANGAN  FROM SR01_GEN_GLOBAL_MS G WHERE G.JENIS_ID = 'SEX' AND G.GLOBAL_ID = P.SEX_ID AND G.AKTIF = '1')KELAMIN,
                            (SELECT D.TRANS_CO  FROM WEB_CO_DIAGNOSA_MS D WHERE D.EPISODE_ID = E.EPISODE_ID AND D.PASIEN_ID = E.PASIEN_ID AND D.SHOW_ITEM = '1')TRANS_CO,
                            (SELECT K.KETERANGAN  FROM SR01_KEU_KELAS_MS K WHERE K.LOKASI_ID = '001' AND K.KELAS_ID = E.KELAS_ID AND K.AKTIF = '1')KELAS,
                            E.DOKTER_ID, D.NAMA NAMA_DOKTER
                            FROM SR01_KEU_EPISODE E, SR01_GEN_PASIEN_MS P, SR01_MED_DOKTER_MS D
                            WHERE
                            E.DOKTER_ID = '".$dokterid."'
                            AND E.LOKASI_ID = '001' 
                            AND E.EPISODE_ID NOT IN 
                            (
                                SELECT
                                    R.EPISODE_ID 
                                FROM
                                    WEB_CO_RESUME_RANAP R 
                                WHERE
                                    R.DOKTER_ID = '".$dokterid."'
                                    AND R.SHOW_ITEM IN 
                                    (
                                        '1',
                                        '2',
                                        '3',
                                        '9'
                                    )
                                    AND R.EPISODE_ID IS NOT NULL
                            )
                            AND E.PASIEN_ID = P.PASIEN_ID 
                            AND D.DOKTER_ID = E.DOKTER_ID 
                            AND E.STATUS_EPISODE <> '99' 
                            AND E.AKTIF = '1' 
                            AND P.AKTIF = '1' 
                            AND P.LOKASI_ID = '001' 
                            AND 
                            (
                                E.TGL_KELUAR IS NULL 
                                OR E.TGL_KELUAR >= TO_DATE('01-03-2018', 'DD-MM-YYYY')
                            )
                            AND E.RUANGRWT_ID IS NOT NULL 
                            ORDER BY E.CREATED_DATE DESC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result_array();
            return $recordset;
        }

    }
?>