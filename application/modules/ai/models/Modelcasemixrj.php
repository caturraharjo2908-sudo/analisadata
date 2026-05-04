<?php
    class Modelcasemixrj extends CI_Model{

        function casemixrj($startdate,$endate){
            $query =
                    "
                        WITH EP AS (
                            SELECT *
                            FROM SR01_KEU_EPISODE
                            WHERE LOKASI_ID='001'
                            AND AKTIF='1'
                            AND JENIS_EPISODE='O'
                            AND REKANAN_ID='BPJS'
                            AND   TRUNC(TGL_MASUK) BETWEEN TRUNC(TO_DATE('".$startdate."','YYYY-MM-DD')) AND TRUNC(TO_DATE('".$endate."','YYYY-MM-DD'))
                        ),

                        KONSUL AS (
                            SELECT *
                            FROM TABLE(SIMRS_MANAGER.ALGORTIMABPJS.RJ_KONSULINTERNAL(NULL))
                        )

                        SELECT
                            A.PASIEN_ID,
                            A.EPISODE_ID,
                            TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') AS TGLMASUK,

                            GETPIDINT(A.PASIEN_ID) AS MRPASIEN,
                            SR01_GET_SUFFIX(A.PASIEN_ID) AS NAMAPASIEN,

                            MP.KETERANGAN AS NAMAPOLI,
                            MD.NAMA AS NAMADOKTER,

                            K.DPJP_UTAMA,
                            K.SEP_NOMOR,
                            K.STATUS_BPJS,
                            K.FLAG_KLAIM

                        FROM EP A

                        LEFT JOIN SR01_MED_POLI_MS MP
                            ON MP.POLI_ID = A.POLI_ID

                        LEFT JOIN SR01_MED_DOKTER_MS MD
                            ON MD.DOKTER_ID = A.DOKTER_ID

                        LEFT JOIN TABLE(SIMRS_MANAGER.ALGORTIMABPJS.RJ_KONSULINTERNAL(A.EPISODE_ID)) K
                            ON K.EPISODE_ID = A.EPISODE_ID
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

    }
?>