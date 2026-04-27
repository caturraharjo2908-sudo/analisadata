<?php
    class Modelkpi extends CI_Model{

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

        function datawaktutunggurajal($periode){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_WAKTU_PELAYANAN_RJ A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.BULAN ASC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }
        
        function dataoperasielektif($periode){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_BATAL_OK A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.BULAN ASC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datakeluarigd($periode){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_KELUAR_IGD A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.BULAN ASC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datarawatjalan($periode){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_WAKTU_LAYANAN_RJ A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.BULAN ASC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datajampulangpasienbln($periode){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_JAM_PULANG_RANAP_BLN A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.BULAN ASC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datajampulangpasienblnruangan($periode){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_JAM_PULANG_RANAP_BLN_RUANGAN A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.SESUDAH_12 DESC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datajampulangharian(){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_JAM_PULANG_RANAP_HARIAN A
                        ORDER BY A.PERIODE ASC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function datajampulangpasienharianruangan(){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_JAM_PULANG_RANAP_HARIAN_RUANGAN A
                        ORDER BY A.SESUDAH_12 DESC

                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

        function registranaptoranap($periode){
            $query =
                    "
                        SELECT A.*
                        FROM MV_KPI_ADMISSION_RANAP A
                        WHERE A.TAHUN='".$periode."'
                        ORDER BY A.BULAN DESC
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }
        
    }
?>