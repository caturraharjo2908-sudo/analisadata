<?php
    class Modelcasemixri extends CI_Model{

        function casemixri($startdate, $enddate){

            $sql = "
                WITH EP AS (
                    SELECT 
                        A.PASIEN_ID,
                        A.EPISODE_ID,
                        A.TGL_MASUK,
                        A.TGL_KELUAR,
                        A.RUANGRWT_ID,
                        A.DOKTER_ID
                    FROM SR01_KEU_EPISODE A
                    WHERE A.LOKASI_ID = '001'
                    AND A.AKTIF = '1'
                    AND A.JENIS_EPISODE = 'I'
                    AND A.REKANAN_ID = 'BPJS'
                    AND A.TGL_MASUK >= TO_DATE(?,'YYYY-MM-DD')
                    AND A.TGL_MASUK <  TO_DATE(?,'YYYY-MM-DD') + 1
                ),

                LIST_EP AS (
                    SELECT EPISODE_ID FROM EP
                ),

                READMISI AS (
                    SELECT 
                        K.EPISODE_ID,
                        MAX(K.PREV_DOKTER_ID) PREV_DOKTER_ID,
                        MAX(K.NEXT_DOKTER_ID) NEXT_DOKTER_ID,
                        MAX(K.SEP_NOMOR) SEP_NOMOR,
                        MAX(K.STATUS_BPJS) STATUS_BPJS,
                        MAX(K.FLAG_KLAIM) FLAG_KLAIM
                    FROM LIST_EP L
                    JOIN TABLE(SIMRS_MANAGER.ALGORTIMABPJS.RI_READMISI(L.EPISODE_ID)) K
                        ON 1=1
                    GROUP BY K.EPISODE_ID
                )

                SELECT
                    A.PASIEN_ID,
                    A.EPISODE_ID,
                    A.RUANGRWT_ID,
                    TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') TGLMASUK,
                    TO_CHAR(A.TGL_KELUAR,'DD.MM.YYYY') TGLKELUAR,

                    GETPIDINT(A.PASIEN_ID) MRPASIEN,
                    SR01_GET_SUFFIX(A.PASIEN_ID) NAMAPASIEN,

                    MD.NAMA NAMADOKTER,
                    DOKTERPREV.NAMA NAMADOKTERPREV,
                    DOKTERNEXT.NAMA NAMADOKTERNEXT,

                    K.PREV_DOKTER_ID,
                    K.NEXT_DOKTER_ID,
                    K.SEP_NOMOR,
                    K.STATUS_BPJS STATUSBPJS_READMISI,
                    K.FLAG_KLAIM FLAGKLAIM_READMISI

                FROM EP A

                /* JOIN READMISI DULU */
                LEFT JOIN READMISI K
                    ON K.EPISODE_ID = A.EPISODE_ID

                /* DOKTER SAAT INI */
                LEFT JOIN SR01_MED_DOKTER_MS MD
                    ON MD.DOKTER_ID = A.DOKTER_ID

                /* DOKTER PREV */
                LEFT JOIN SR01_MED_DOKTER_MS DOKTERPREV
                    ON DOKTERPREV.DOKTER_ID = K.PREV_DOKTER_ID

                /* DOKTER NEXT */
                LEFT JOIN SR01_MED_DOKTER_MS DOKTERNEXT
                    ON DOKTERNEXT.DOKTER_ID = K.NEXT_DOKTER_ID
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