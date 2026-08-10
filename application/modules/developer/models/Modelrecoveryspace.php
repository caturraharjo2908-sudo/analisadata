<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modelrecoveryspace extends CI_Model {

    public function get_fra_usage() {
        $sql = "SELECT * FROM v\$flash_recovery_area_usage";
        return $this->db->query($sql)->result_array();
    }

    public function get_top_sql() {
        $sql = "SELECT u.username, ash.machine, ash.sql_id, COUNT(*) as total_samples, ash.session_state, ash.event, sq.sql_text 
                FROM v\$active_session_history ash 
                LEFT JOIN v\$sql sq ON ash.sql_id = sq.sql_id 
                LEFT JOIN dba_users u ON ash.user_id = u.user_id 
                WHERE ash.sample_time > SYSDATE - (2/24) AND ash.sql_id IS NOT NULL 
                GROUP BY u.username, ash.machine, ash.sql_id, ash.session_state, ash.event, sq.sql_text 
                ORDER BY total_samples DESC FETCH FIRST 10 ROWS ONLY";
        return $this->db->query($sql)->result_array();
    }

    public function get_db_status() {
        $sql = "SELECT name, database_role, open_mode, protection_mode FROM v\$database";
        return $this->db->query($sql)->row_array();
    }

    public function get_dataguard_status() {
        $sql = "SELECT (SELECT max(sequence#) FROM v\$archived_log WHERE applied='YES') AS LOG_APPLIED, 
                       (SELECT max(sequence#) FROM v\$archived_log) AS LOG_RECEIVED FROM dual";
        return $this->db->query($sql)->row_array();
    }

    public function get_tablespace_usage() {
        $sql = "SELECT 
                a.tablespace_name,
                ROUND((a.bytes_alloc - NVL(b.bytes_free, 0)) / 1024 / 1024, 2) AS used_mb,
                ROUND(a.bytes_alloc / 1024 / 1024, 2) AS allocated_mb,
                ROUND(a.maxbytes / 1024 / 1024, 2) AS max_mb,
                ROUND(((a.bytes_alloc - NVL(b.bytes_free, 0)) / a.maxbytes) * 100, 2) AS percent_used
            FROM 
                (SELECT tablespace_name, SUM(bytes) bytes_alloc, SUM(DECODE(maxbytes, 0, bytes, maxbytes)) maxbytes 
                 FROM dba_data_files GROUP BY tablespace_name) a,
                (SELECT tablespace_name, SUM(bytes) bytes_free 
                 FROM dba_free_space GROUP BY tablespace_name) b
            WHERE 
                a.tablespace_name = b.tablespace_name (+)
            ORDER BY percent_used DESC";
        return $this->db->query($sql)->result_array();
    }

    public function get_resource_limit() {
        $sql = "SELECT resource_name, current_utilization, max_utilization, limit_value 
                FROM v\$resource_limit WHERE resource_name IN ('processes', 'sessions')";
        return $this->db->query($sql)->result_array();
    }

    public function get_blocking_sessions() {
        $sql = "SELECT s1.username || '@' || s1.machine AS blocking_user, s1.sid AS blocking_sid, 
                       s2.username || '@' || s2.machine AS waiting_user, s2.sid AS waiting_sid, s2.seconds_in_wait 
                FROM v\$lock l1, v\$session s1, v\$lock l2, v\$session s2 
                WHERE s1.sid = l1.sid AND s2.sid = l2.sid AND l1.block = 1 AND l2.request > 0 
                AND l1.id1 = l2.id1 AND l1.id2 = l2.id2";
        return $this->db->query($sql)->result_array();
    }

    public function get_rman_backup_status() {
        $sql = "SELECT input_type AS backup_type, status, TO_CHAR(start_time, 'DD-MON-YYYY HH24:MI:SS') AS start_time, 
                       TO_CHAR(end_time, 'DD-MON-YYYY HH24:MI:SS') AS end_time, time_taken_display AS time_taken 
                FROM v\$rman_backup_job_details WHERE start_time > SYSDATE - 7 
                ORDER BY start_time DESC FETCH FIRST 5 ROWS ONLY";
        return $this->db->query($sql)->result_array();
    }

    public function get_AWR() {
        $sql = "SELECT 
            TO_CHAR(sn.end_interval_time, 'YYYY-MM-DD HH24:MI') AS waktu,
            sm.metric_name AS metrik,
            ROUND(sm.average, 2) AS nilai_rata_rata,
            ROUND(sm.maxval, 2) AS nilai_maksimal
            FROM 
                dba_hist_sysmetric_summary sm
            JOIN 
                dba_hist_snapshot sn 
                ON sm.snap_id = sn.snap_id 
                AND sm.instance_number = sn.instance_number
            WHERE 
                sm.metric_name IN (
                    'Host CPU Utilization (%)', 
                    'I/O Requests per Second',  
                    'Average Active Sessions',
                    'User Commits Per Sec'      -- << Metrik Commit ditambahkan di sini
                )
                AND sn.end_interval_time >= SYSDATE - 7 
            ORDER BY 
                sn.end_interval_time ASC, 
                sm.metric_name";

        return $this->db->query($sql)->result_array();
    }

    public function get_instance_uptime() {
    $sql = "SELECT instance_name, host_name, version, status, 
                   TO_CHAR(startup_time, 'DD-MON-YYYY HH24:MI:SS') AS startup_time,
                   TRUNC(SYSDATE - startup_time) AS uptime_days 
            FROM v\$instance";
    return $this->db->query($sql)->row_array();
    }
}
