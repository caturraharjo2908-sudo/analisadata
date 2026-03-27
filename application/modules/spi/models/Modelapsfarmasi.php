<?php
    class Modelapsfarmasi extends CI_Model{

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

        function rawdataapsfarmasi($periode){
            $query =
                    "
                        WITH OBAT AS (
                            SELECT
                                RSP.EPISODE_ID,
                                RSP.PASIEN_ID,
                                REGEXP_REPLACE(
                                    XMLAGG(
                                        XMLELEMENT(E,
                                            RSP.NAMA_OBAT || ':' || 
                                            RSP.QTY || ':' ||
                                            NVL(RSP.GOL_OBAT,'') || ':' || 
                                            NVL(RSP.GOLONGANOBAT,'') || ';'
                                        )
                                        ORDER BY RSP.NAMA_OBAT
                                    ).EXTRACT('//text()').GETCLOBVAL(),
                                    ';$',''
                                ) AS OBAT
                            FROM (
                                SELECT
                                    A.EPISODE_ID,
                                    A.PASIEN_ID,
                                    A.OBAT_ID,
                                    SUM(A.QTY) AS QTY, -- 🔥 FIX DUPLIKAT (INI KUNCI)
                                    B.NAMA AS NAMA_OBAT,
                                    B.GOL_OBAT,
                                    C.KETERANGAN AS GOLONGANOBAT
                                FROM WEB_CO_RESEP_DT A
                                LEFT JOIN SR01_FRM_OBAT_MS B 
                                    ON B.OBAT_ID = A.OBAT_ID
                                LEFT JOIN SR01_FRM_GOLOBAT_MS C 
                                    ON C.GOLOBAT_ID = B.GOL_OBAT
                                WHERE A.LOKASI_ID = '001'
                                AND A.SHOW_ITEM = '1'
                                AND A.TYPE NOT IN ('01','02')
                                GROUP BY
                                    A.EPISODE_ID,
                                    A.PASIEN_ID,
                                    A.OBAT_ID,
                                    B.NAMA,
                                    B.GOL_OBAT,
                                    C.KETERANGAN
                            ) RSP
                            GROUP BY RSP.EPISODE_ID, RSP.PASIEN_ID
                        )

                        SELECT 
                            A.PASIEN_ID,
                            A.EPISODE_ID,
                            A.TGL_MASUK,
                            TO_CHAR(A.TGL_MASUK,'DD.MM.YYYY') AS TGLMASUK,
                            A.POLI_ID,
                            A.DOKTER_ID,
                            A.REKANAN_ID,

                            GETPIDINT(A.PASIEN_ID) AS MRPAS,
                            SR01_GET_SUFFIX(A.PASIEN_ID) AS NAMAPAS,

                            P.KETERANGAN AS POLIKLINIK,
                            D.NAMA       AS NAMADOKTER,
                            R.NAMA       AS PROVIDER,
                            K.NIK        AS NIKKARYAWAN,

                            B.OBAT,

                            SR01_PENDAPATAN_RAJAL.OBAT_RAJAL(A.EPISODE_ID,A.PASIEN_ID) AS TOTALHARGAOBAT

                        FROM SR01_KEU_EPISODE A

                        LEFT JOIN OBAT B 
                            ON B.PASIEN_ID  = A.PASIEN_ID
                            AND B.EPISODE_ID = A.EPISODE_ID

                        LEFT JOIN SR01_MED_POLI_MS P 
                            ON P.POLI_ID = A.POLI_ID

                        LEFT JOIN SR01_MED_DOKTER_MS D 
                            ON D.DOKTER_ID = A.DOKTER_ID

                        LEFT JOIN SR01_KEU_REKANAN_MS R 
                            ON R.REKANAN_ID = A.REKANAN_ID

                        LEFT JOIN HRD_KARYAWAN_MS K 
                            ON K.MEDREC = A.PASIEN_ID
                            AND K.SHOW_ITEM = '1'
                            AND K.AKTIFASI  = '1'

                        WHERE A.LOKASI_ID = '001'
                        AND A.AKTIF = '1'
                        AND A.STATUS_EPISODE <> '99'
                        AND A.POLI_ID = 'KLINI0000000001'
                        AND   A.TGL_MASUK >= TO_DATE('01-01-" . $periode . "','DD-MM-YYYY')
                        AND   A.TGL_MASUK <  TO_DATE('01-01-" . ($periode+1) . "','DD-MM-YYYY')
                        AND B.OBAT IS NOT NULL

                        ORDER BY A.TGL_MASUK DESC
                    ";

            $queryExec = $this->db->query($query);
            $rows = $queryExec->result_array();

            foreach ($rows as &$row) {
                // HANDLE CLOB OBAT
                if (isset($row['OBAT']) && is_object($row['OBAT'])) {
                    $row['OBAT'] = $row['OBAT']->load();
                }

                $row['OBAT'] = $row['OBAT'] ?? '';

                // Fix UTF8 (anti JSON error)
                $row['OBAT'] = mb_convert_encoding($row['OBAT'], 'UTF-8', 'UTF-8');
            }

            return $rows;
        }

    }
?>