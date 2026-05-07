<?php
    class Modelworkloadpendaftaran extends CI_Model{

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

        function datatransaksi($periode){
            $query =
                    "
                        SELECT X.KETERANGAN, X.JAM,
                            ROUND(AVG(X.JUMLAH_PASIEN), 0) AS AVG_PASIEN
                        FROM(
                            SELECT 'Pendaftaran Rawat Jalan On Line' KETERANGAN,
                                TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.CREATED_DATE, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                                
                            FROM WEB_CO_REGISTRASI_ONLINE_HD A
                            WHERE TO_CHAR(A.CREATED_DATE, 'YYYY') = '".$periode."'
                            AND   A.POLI_ID<>'UGD01'
                            AND   A.CREATED_BY<>'OTS'
                            GROUP BY TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD'), TO_CHAR(A.CREATED_DATE, 'HH24')

                            UNION

                            SELECT 'Pendaftaran Rawat Jalan On The Spot' KETERANGAN,
                                TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.CREATED_DATE, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                                
                            FROM WEB_CO_REGISTRASI_ONLINE_HD A
                            WHERE TO_CHAR(A.CREATED_DATE, 'YYYY') = '".$periode."'
                            AND   A.POLI_ID<>'UGD01'
                            AND   A.CREATED_BY='OTS'
                            GROUP BY TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD'), TO_CHAR(A.CREATED_DATE, 'HH24')

                            UNION

                            SELECT 'Check In' KETERANGAN,
                                TO_CHAR(A.TGL_HADIR, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.TGL_HADIR, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                                
                            FROM WEB_CO_REGISTRASI_ONLINE_HD A
                            WHERE TO_CHAR(A.TGL_HADIR, 'YYYY') = '".$periode."'
                            AND   A.POLI_ID<>'UGD01'
                            AND   A.CREATED_BY='OTS'
                            GROUP BY TO_CHAR(A.TGL_HADIR, 'YYYY-MM-DD'), TO_CHAR(A.TGL_HADIR, 'HH24')

                            UNION

                            SELECT 'Call Nurse Station' KETERANGAN,
                                TO_CHAR(A.TGL_PANGGIL, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.TGL_PANGGIL, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                                
                            FROM WEB_CO_REGISTRASI_ONLINE_HD A
                            WHERE TO_CHAR(A.TGL_PANGGIL, 'YYYY') = '".$periode."'
                            AND   A.POLI_ID<>'UGD01'
                            AND   A.CREATED_BY='OTS'
                            GROUP BY TO_CHAR(A.TGL_PANGGIL, 'YYYY-MM-DD'), TO_CHAR(A.TGL_PANGGIL, 'HH24')

                            UNION

                            SELECT 'Pendaftaran IGD' KETERANGAN,
                                TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.CREATED_DATE, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                                
                            FROM SR01_PASIEN_IGD A
                            WHERE TO_CHAR(A.CREATED_DATE, 'YYYY') = '".$periode."'
                            GROUP BY TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD'), TO_CHAR(A.CREATED_DATE, 'HH24')

                            UNION

                            SELECT 'Pendaftaran Rawat Inap' KETERANGAN,
                                TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.CREATED_DATE, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                                
                            FROM SR01_KEU_TRANSKMR_IT A
                            WHERE TO_CHAR(A.CREATED_DATE, 'YYYY') = '".$periode."'
                            GROUP BY TO_CHAR(A.CREATED_DATE, 'YYYY-MM-DD'), TO_CHAR(A.CREATED_DATE, 'HH24')

                            UNION

                            SELECT 'Login Pulang' KETERANGAN,
                                TO_CHAR(A.TGL_LOGINPLG, 'YYYY-MM-DD') AS HARI,
                                TO_CHAR(A.TGL_LOGINPLG, 'HH24') AS JAM,
                                COUNT(*) AS JUMLAH_PASIEN
                                
                            FROM SR01_KEU_EPISODE A
                            WHERE TO_CHAR(A.TGL_LOGINPLG, 'YYYY') = '".$periode."'
                            GROUP BY TO_CHAR(A.TGL_LOGINPLG, 'YYYY-MM-DD'), TO_CHAR(A.TGL_LOGINPLG, 'HH24')
                        )X
                        GROUP BY X.KETERANGAN, X.JAM
                        ORDER BY X.KETERANGAN, X.JAM
                    ";

            $recordset = $this->db->query($query);
            $recordset = $recordset->result();
            return $recordset;
        }

    }
?>