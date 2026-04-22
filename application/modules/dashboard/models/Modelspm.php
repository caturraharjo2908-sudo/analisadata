<?php
    class Modelspm extends CI_Model{

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

        function dataspm($periode){
            $query =
                    "
                        SELECT A.SPM_ID, HEADER_SPM_ID, SPM, DECODE(JENIS,'1','Input','2','Proses','3','Output','Outcome')JENIS, KATEGORI, URUT, TARGET_1, TARGET_2, PENGISIAN, TIPE,
                                (
                                    SELECT
                                            DECODE(
                                                    A.TIPE,
                                                    '1',BLN_1,
                                                    '2',DECODE(BLN_1,'Y','Ada','Tidak Ada'),
                                                    '3',BLN_1,
                                                    ''
                                                )
                                    FROM SR01_SPM_TRANSAKSI WHERE LOKASI_ID='001' AND AKTIF='1' AND PERIODE='".$periode."' AND SPM_ID=A.SPM_ID
                                )BLN1,
                                (
                                    SELECT
                                            DECODE(
                                                    A.TIPE,
                                                    '1',BLN_2,
                                                    '2',DECODE(BLN_2,'Y','Ada','Tidak Ada'),
                                                    '3',BLN_2,
                                                    ''
                                                )
                                    FROM SR01_SPM_TRANSAKSI WHERE LOKASI_ID='001' AND AKTIF='1' AND PERIODE='".$periode."' AND SPM_ID=A.SPM_ID
                                )BLN2,
                                (
                                    SELECT
                                            DECODE(
                                                    A.TIPE,
                                                    '1',BLN_3,
                                                    '2',DECODE(BLN_3,'Y','Ada','Tidak Ada'),
                                                    '3',BLN_3,
                                                    ''
                                                )
                                    FROM SR01_SPM_TRANSAKSI WHERE LOKASI_ID='001' AND AKTIF='1' AND PERIODE='".$periode."' AND SPM_ID=A.SPM_ID
                                )BLN3,
                                (
                                    SELECT MASALAH_TW1
                                    FROM SR01_SPM_TRANSAKSI WHERE LOKASI_ID='001' AND AKTIF='1' AND PERIODE='".$periode."' AND SPM_ID=A.SPM_ID
                                )MASALAH_TW1,
                                (
                                    SELECT RTL_TW1
                                    FROM SR01_SPM_TRANSAKSI WHERE LOKASI_ID='001' AND AKTIF='1' AND PERIODE='".$periode."' AND SPM_ID=A.SPM_ID
                                )RTL_TW1
                            FROM SR01_SPM_MS A
                            WHERE A.LOKASI_ID = '001'
                            AND A.AKTIF = '1'
                            ORDER BY NVL(A.HEADER_SPM_ID, A.SPM_ID), A.KATEGORI DESC, A.URUT ASC, A.JENIS ASC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }
        

        
        
    }
?>