<?php
    class Modelcasemixrj extends CI_Model{

        function casemixrj($startdate, $enddate){

            $sql = "
                WITH EP AS (
                    SELECT /*+ INDEX(A IDX_EPISODE_TGL) */
                        A.PASIEN_ID,
                        A.EPISODE_ID,
                        A.TGL_MASUK,
                        A.POLI_ID,
                        A.DOKTER_ID
                    FROM SR01_KEU_EPISODE A
                    WHERE A.LOKASI_ID = '001'
                    AND A.AKTIF = '1'
                    AND A.JENIS_EPISODE = 'O'
                    AND A.REKANAN_ID = 'BPJS'
                    AND A.POLI_ID <> 'UGD01'
                    AND A.TGL_MASUK >= TO_DATE(?,'YYYY-MM-DD')
                    AND A.TGL_MASUK <  TO_DATE(?,'YYYY-MM-DD') + 1
                ),

                LIST_EP AS (
                    SELECT EPISODE_ID FROM EP
                ),

                -- 🔥 KONSUL INTERNAL (1 ROW / EPISODE)
                KONSUL AS (
                    SELECT 
                        K.EPISODE_ID,
                        MAX(K.DPJP_UTAMA) DPJP_UTAMA,
                        MAX(K.SEP_NOMOR) SEP_NOMOR,
                        MAX(K.STATUS_BPJS) STATUS_BPJS,
                        MAX(K.FLAG_KLAIM) FLAG_KLAIM
                    FROM LIST_EP L
                    JOIN TABLE(SIMRS_MANAGER.ALGORTIMABPJS.RJ_KONSULINTERNAL(L.EPISODE_ID)) K
                        ON 1=1
                    GROUP BY K.EPISODE_ID
                ),

                -- 🔥 LAMPIRAN
                LAMPIRAN AS (
                    SELECT 
                        M.EPISODE_ID,
                        MAX(M.STATUS_BPJS) STATUS_BPJS,
                        MAX(M.FLAG_KLAIM) FLAG_KLAIM
                    FROM LIST_EP L
                    JOIN TABLE(SIMRS_MANAGER.ALGORTIMABPJS.LAMPIRAN_TINDAKAN(L.EPISODE_ID)) M
                        ON 1=1
                    GROUP BY M.EPISODE_ID
                ),

                -- 🔥 PROCEDURE ICD9
                PROCEDURE_ICD9 AS (
                    SELECT 
                        B.EPISODE_ID,
                        MAX(B.STATUS_BPJS) STATUS_BPJS,
                        MAX(B.FLAG_KLAIM) FLAG_KLAIM
                    FROM LIST_EP L
                    JOIN TABLE(SIMRS_MANAGER.ALGORTIMABPJS.PROCEDURE_ICD9(L.EPISODE_ID)) B
                        ON 1=1
                    GROUP BY B.EPISODE_ID
                )

                SELECT
                    A.PASIEN_ID,
                    A.EPISODE_ID,
                    TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') TGLMASUK,

                    GETPIDINT(A.PASIEN_ID) MRPASIEN,
                    SR01_GET_SUFFIX(A.PASIEN_ID) NAMAPASIEN,

                    MP.KETERANGAN NAMAPOLI,
                    MD.NAMA NAMADOKTER,

                    K.DPJP_UTAMA,
                    K.SEP_NOMOR,
                    K.STATUS_BPJS STATUSBPJS_KONSULINTERNAL,
                    K.FLAG_KLAIM FLAGKLAIM_KONSULINTERNAL,

                    L.STATUS_BPJS STATUSBPJS_LAMPIRAN,
                    L.FLAG_KLAIM FLAGKLAIM_LAMPIRAN,

                    B.STATUS_BPJS STATUSBPJS_PROCEDURE,
                    B.FLAG_KLAIM FLAGKLAIM_PROCEDURE

                FROM EP A

                LEFT JOIN SR01_MED_POLI_MS MP
                    ON MP.POLI_ID = A.POLI_ID

                LEFT JOIN SR01_MED_DOKTER_MS MD
                    ON MD.DOKTER_ID = A.DOKTER_ID

                LEFT JOIN KONSUL K
                    ON K.EPISODE_ID = A.EPISODE_ID

                LEFT JOIN LAMPIRAN L
                    ON L.EPISODE_ID = A.EPISODE_ID

                LEFT JOIN PROCEDURE_ICD9 B
                    ON B.EPISODE_ID = A.EPISODE_ID
            ";

            // 🔥 EXECUTE (POSITIONAL BIND)
            $query = $this->db->query($sql, [
                $startdate,
                $enddate
            ]);

            // 🔥 ANTI MEMORY JEBOL (STREAMING)
            $data = [];
            while ($row = $query->unbuffered_row('array')) {
                $data[] = $row;
            }

            return $data;
        }

    }
?>