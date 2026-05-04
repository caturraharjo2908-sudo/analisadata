<?php
    class Modelresumemedispreview extends CI_Model{

        function listpasien(){
            $query =
                    "
                        SELECT
                            E.EPISODE_ID,
                            P.PASIEN_ID,
                            TO_CHAR(E.TGL_MASUK,  'DD.MM.YYYY') AS TGL_MASUK,
                            TO_CHAR(E.TGL_KELUAR, 'DD.MM.YYYY') AS TGL_KELUAR,
                            E.RUANGRWT_ID,
                            P.INT_PASIEN_ID,
                            SR01_GET_SUFFIX(P.PASIEN_ID) AS NAMA,
                            P.NAMA_AKHIR,
                            TO_CHAR(P.TGL_LAHIR, 'DD.MM.YYYY') AS TGL_LAHIR,
                            HITUNG_UMUR(P.TGL_LAHIR, TRUNC(SYSDATE)) AS UMUR,

                            -- Jenis Kelamin
                            (
                                SELECT G.KETERANGAN
                                FROM SR01_GEN_GLOBAL_MS G
                                WHERE G.JENIS_ID = 'SEX'
                                AND G.GLOBAL_ID = P.SEX_ID
                                AND G.AKTIF = '1'
                            ) AS KELAMIN,

                            -- Diagnosa CO
                            (
                                SELECT D.TRANS_CO
                                FROM WEB_CO_DIAGNOSA_MS D
                                WHERE D.EPISODE_ID = E.EPISODE_ID
                                AND D.PASIEN_ID  = E.PASIEN_ID
                                AND D.SHOW_ITEM  = '1'
                            ) AS TRANS_CO,

                            -- Kelas
                            (
                                SELECT K.KETERANGAN
                                FROM SR01_KEU_KELAS_MS K
                                WHERE K.LOKASI_ID = '001'
                                AND K.KELAS_ID  = E.KELAS_ID
                                AND K.AKTIF     = '1'
                            ) AS KELAS,

                            E.DOKTER_ID,
                            D.NAMA AS NAMA_DOKTER

                        FROM SR01_KEU_EPISODE E
                        JOIN SR01_GEN_PASIEN_MS P 
                            ON P.PASIEN_ID = E.PASIEN_ID
                        JOIN SR01_MED_DOKTER_MS D 
                            ON D.DOKTER_ID = E.DOKTER_ID

                        WHERE E.LOKASI_ID = '001'

                            AND E.EPISODE_ID IN (
                                SELECT R.EPISODE_ID
                                FROM WEB_CO_RESUME_RANAP R
                                WHERE R.SHOW_ITEM IN ('1','2','3','9')
                                AND R.EPISODE_ID IS NOT NULL
                            )

                            AND E.EPISODE_ID IN (
                                SELECT R.EPISODE_ID
                                FROM WEB_CO_RESUME_RANAP_AI R
                                WHERE R.SHOW_ITEM IN ('1','2','3','9')
                                AND R.EPISODE_ID IS NOT NULL
                            )

                            AND E.STATUS_EPISODE <> '99'
                            AND E.AKTIF = '1'
                            AND P.AKTIF = '1'
                            AND P.LOKASI_ID = '001'

                            -- Filter tanggal keluar
                            AND (
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

        function resumeAI($episodeid){
            $query =
                    "
                        SELECT A.*
                        FROM WEB_CO_RESUME_RANAP_AI A
                        WHERE A.SHOW_ITEM='1'
                        AND   A.EPISODE_ID='".$episodeid."'
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function resumeFinal($episodeid){
            $query =
                    "
                        SELECT A.*
                        FROM WEB_CO_RESUME_RANAP A
                        WHERE A.SHOW_ITEM<>'0'
                        AND   A.EPISODE_ID='".$episodeid."'
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

    }
?>