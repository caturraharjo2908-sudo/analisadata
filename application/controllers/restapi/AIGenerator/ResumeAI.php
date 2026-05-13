<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/REST_Controller.php';
require 'vendor/autoload.php';

if(!function_exists('color')){
    function color($name = null){
        $colors = [
            'reset'          => "\033[0m",
            'black'          => "\033[30m",
            'red'            => "\033[31m",
            'green'          => "\033[32m",
            'yellow'         => "\033[33m",
            'blue'           => "\033[34m",
            'magenta'        => "\033[35m",
            'cyan'           => "\033[36m",
            'white'          => "\033[37m",
            'gray'           => "\033[90m",
            'light_red'      => "\033[91m",
            'light_green'    => "\033[92m",
            'light_yellow'   => "\033[93m",
            'light_blue'     => "\033[94m",
            'light_magenta'  => "\033[95m",
            'light_cyan'     => "\033[96m",
            'light_white'    => "\033[97m",
        ];

        return $colors[$name] ?? $colors['reset'];
    }
}

class ResumeAI extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("ModelResumeAI","md");
    }

    public function generateresumeai_post($episodeid){
        $body       = [];
        $sourcedata = [];
        $dataresume = [];

        $resultkunjungan      = $this->md->kunjungan($episodeid);
        $resultkeluhanutama   = $this->md->keluhanutama($episodeid);
        $resultobat           = $this->md->obat($episodeid);
        $resultradiologi      = $this->md->radiologi($episodeid);
        $resultlaboratoriumhd = $this->md->laboratoriumhd($episodeid);
        $resultdiagnosa       = $this->md->diagnosa($episodeid);

        if (empty($resultkeluhanutama)) {

            $body['status']  = false;
            $body['code']    = 404;
            $body['message'] = "Source Data Tidak Tersedia";
            $body['metadata']['timestamp'] = date('Y-m-d H:i:s');

            return $this->response($body, 404);
        }

        $sourcedata['riwayat']['keluhanutama']           = $this->keluhanutama($resultkeluhanutama);
        $sourcedata['riwayat']['gejala']                 = $this->gejala($resultkeluhanutama);
        $sourcedata['riwayat']['sekarang']               = $this->sekarang($resultkeluhanutama);
        $sourcedata['riwayat']['dahulu']                 = $this->dahulu($resultkeluhanutama);
        $sourcedata['pemeriksaanfisik']['statuslokalis'] = $this->statuslokalis($resultkeluhanutama);
        $sourcedata['pemeriksaanfisik']['ttv']           = $this->ttv($resultkeluhanutama);
        $sourcedata['diagnosis']['indikasiranap']        = $this->indikasiranap($resultkeluhanutama);
        $sourcedata['diagnosis']['icd10']                = $this->icd10($resultdiagnosa);
        $sourcedata['kontrolulang']                      = $this->kontrolulang($resultkunjungan);
        $sourcedata['segeradibawa']                      = $this->segaradibawa($resultkunjungan);
        $sourcedata['penunjang']['obat']['perawatan']    = $this->obatperawat($resultobat);
        $sourcedata['penunjang']['obat']['pulang']       = $this->obatpulang($resultobat);
        $sourcedata['penunjang']['radiologi']            = $this->radiologi($resultradiologi);
        $sourcedata['penunjang']['laboratorium']         = $this->laboratorium($resultkunjungan,$resultlaboratoriumhd);
        

        $body['status']                = true;
        $body['code']                  = 200;
        $body['message']               = "success";
        $body['sourcedata'][]          = $sourcedata;
        $body['transaksi']             = $this->kunjungan($resultkunjungan);
        $body['metadata']['timestamp'] = date('Y-m-d H:i:s');

        $dataresume['PASIEN_ID']         = $body['transaksi']['pasienid'];
        $dataresume['EPISODE_ID']        = $body['transaksi']['episodeid'];
        $dataresume['DOKTER_ID']         = $body['transaksi']['dokterid'];
        $dataresume['RUANG_ID']          = $body['transaksi']['ruangid'];
        $dataresume['CREATED_BY']        = $body['transaksi']['dokterid'];
        $dataresume['KONDISI']           = $body['transaksi']['pulang'];
        $dataresume['KONDISI_PULANG_ID'] = $body['transaksi']['pulangid'];
        $dataresume['KELUHAN']           = $body['sourcedata'][0]['riwayat']['keluhanutama']['text'];
        $dataresume['GEJALA']            = $body['sourcedata'][0]['riwayat']['gejala']['text'];
        $dataresume['RIWAYATPS']         = $body['sourcedata'][0]['riwayat']['sekarang']['text'];
        $dataresume['RIWAYATPD']         = $body['sourcedata'][0]['riwayat']['dahulu']['text'];
        $dataresume['STATUS']            = $body['sourcedata'][0]['pemeriksaanfisik']['statuslokalis']['text'];
        $dataresume['VITAL']             = $body['sourcedata'][0]['pemeriksaanfisik']['ttv']['text'];
        $dataresume['INDIKASI']          = $body['sourcedata'][0]['diagnosis']['indikasiranap']['text'];
        $dataresume['LAINNYA']           = $body['sourcedata'][0]['penunjang']['radiologi']['text'];
        $dataresume['OBATP']             = $body['sourcedata'][0]['penunjang']['obat']['pulang']['text'];
        $dataresume['KONTROL']           = $body['sourcedata'][0]['kontrolulang']['text'];
        $dataresume['INTRUKSI']          = $body['sourcedata'][0]['segeradibawa']['text'];

        $this->md->insertresume($dataresume);

        $this->response($body, 200);
    }

    public function kunjungan($result){
        $body = [];

        $body['pasienid']      = $result->PASIEN_ID;
        $body['episodeid']     = $result->EPISODE_ID;
        $body['statusepisode'] = $result->STATUS_EPISODE;
        $body['ruangid']       = $result->RUANGRWT_ID;
        $body['mrpasien']      = $result->MRPASIEN;
        $body['namapasien']    = $result->NAMAPSIEN;
        $body['pulangid']      = $result->PULANG_ID;
        $body['pulang']        = $result->CARAPULANG;
        $body['tglkeluar']     = $result->TGLKELUAR;
        $body['dokterid']      = $result->DOKTER_ID;
        $body['namadokter']    = $result->NAMADOKTER;

        return $body;
    }

    public function icd10($result){
        $body = [];
        foreach ($result as $a) {
            $item             = [];
            $item['icd10_id']   = $a['ICD10'];
            $item['icd10_desc'] = $a['DIAGNOSA'];

            $body['raw'][] = $item;
        }

        return $body;
    }

    public function obatperawat($result){
        $body = [];
        foreach ($result as $a) {
            $item             = [];
            $item['obatid']   = $a['OBAT_ID'];
            $item['namaobat'] = $a['NAMA_OBAT'];

            if ($a['JENIS_RESEP'] == "1") {
                $body['raw'][] = $item;
            }
        }

        $body['text'] = implode("\n",array_map(function($item) {return "- {$item['namaobat']}";}, $body['raw']));
        $body['len']  = mb_strlen($body['text']);

        return $body;
    }

    public function obatpulang($result){
        $body = [];

        if(!empty($result)){
            $body['raw'] = [];

            foreach ($result as $a) {
                $item             = [];
                $item['obatid']   = $a['OBAT_ID'];
                $item['namaobat'] = $a['NAMA_OBAT'];

                if ($a['JENIS_RESEP'] != "1") {
                    $body['raw'][] = $item;
                }
            }

            $body['text'] = !empty($body['raw']) ? implode("\n", array_map(function($item) { return "- {$item['namaobat']}"; }, $body['raw'])) : "";
            $body['len']  = mb_strlen($body['text']);
        }else{
            $body['raw']  = [];
            $body['text'] = "";
            $body['len']  = 0;
        }
        
        return $body;
    }

    public function radiologi($result){
        $body = [];
        
        if(!empty($result)){
            foreach ($result as $a) {
                $item             = [];
                $item['namapemeriksaan'] = $a['NAMAPEMERIKSAAN'];
                $item['result']          = $a['RESULT'];
                $item['createddate']     = $a['CREATEDDATE'];

                $body['raw'][] = $item;
            }

            $body['text'] = implode("\n\n", array_map(function($item){return trim($item['createddate']) . " " . $item['namapemeriksaan'] . "\n" ."Conclusion:\n" . $item['result'];}, $body['raw']));
            $body['len']  = mb_strlen($body['text']);
        }else{
            $body['raw']  = [];
            $body['text'] = "";
            $body['len']  = 0;
        }

        return $body;
    }
    
    public function laboratorium($kunjungan, $result){

        $body = [];
        $norm = $kunjungan->MRPASIEN;

        foreach ($result as $a) {

            $item = [];
            $item['sampelid']    = $a['SAMPEL_ID'];
            $item['registerid']  = $a['REGISTRASI_ID'];
            $item['createddate'] = $a['CREATEDDATE'];
            $item['hasil']       = [];

            $resultlaboratoriumdt = $this->md->laboratoriumdt($a['SAMPEL_ID'], $norm);

            foreach ($resultlaboratoriumdt as $rowdetail) {
                $item['hasil'][] = [
                    'namates' => $rowdetail['NAMA_TES'] ?? '',
                    'unit'    => $rowdetail['UNITS'] ?? '',
                    'result'  => $rowdetail['RESULT_VALUE'] ?? '',
                    'flag'    => $rowdetail['RESULT_FLAG'] ?? ''
                ];
            }

            $body['raw'][] = $item;
        }

        $body['raw'] = $body['raw'];

        $body['text'] = implode("\n\n", array_map(function($item){
            $header = $item['createddate'] . " Sampel: " . $item['sampelid'];
            $detail = "";

            foreach ($item['hasil'] as $h) {
                $detail .= "- {$h['namates']} : {$h['result']} {$h['unit']} {$h['flag']}\n";
            }

            return $header . "\n" . trim($detail);
        }, $body['raw']));

        $body['len'] = mb_strlen($body['text']);

        return $body;
    }

    public function gejala($result){
        $text   = $result->S2;

        $parsed   = $this->extractPositiveClinicalFlags($text);
        $joined   = implode(", ", $parsed['raw']);
        $symptoms = $this->extractSymptomsPerItem($joined);

        return [
            "raw"  => $symptoms['raw'],
            "text" => $symptoms['text'],
            "len"  => mb_strlen($symptoms['text'])
        ];
    }

    public function sekarang($result){
        $body = [];

        $text = trim($result->S2);

        $lines = preg_split('/\n+/', $text);

        $cleaned = [];

        foreach ($lines as $line) {

            $lower = strtolower(trim($line));

            // =========================
            // 🔥 SKIP SEMUA RIWAYAT BLOCK
            // =========================
            if (
                strpos($lower, 'riwayat sosial') !== false ||
                strpos($lower, 'riwayat nikah') !== false ||
                strpos($lower, 'riwayat obstetri') !== false ||
                strpos($lower, 'riwayat kb') !== false ||
                strpos($lower, 'riwayat menstruasi') !== false ||
                strpos($lower, 'rpd') !== false ||
                strpos($lower, 'rpk') !== false ||
                strpos($lower, 'rpo') !== false
            ) {
                continue;
            }

            // =========================
            // 🔥 SKIP DETAIL SOSIAL & ADMIN
            // =========================
            if (
                preg_match('/\b(irt|suami|istri|karyawan|pekerjaan|pendidikan|sma|smk|stm|merokok|minum alkohol|alkohol)\b/i', $lower)
            ) {
                continue;
            }

            // =========================
            // 🔥 SKIP OBSTETRI DETAIL (1. 2. 3. 4. Hamil ini)
            // =========================
            if (preg_match('/^\d+\./', $lower)) {
                continue;
            }

            if (preg_match('/\b(hamil ini|abortus|spontan|bbl|aterm|dikuret)\b/i', $lower)) {
                continue;
            }

            // =========================
            // KEEP ONLY CURRENT CONDITION
            // =========================
            $cleaned[] = $line;
        }

        $text = implode("\n", $cleaned);

        $body['text'] = $text;
        $body['len']  = mb_strlen($text);

        return $body;
    }

    public function dahulu($result){
        $text = $result->S3;

        $parsed = $this->extractRiwayatClinical($text);

        return [
            "baseon"  => $text,
            "raw"  => $parsed['raw'],
            "text" => $parsed['text'],
            "len"  => mb_strlen($parsed['text'])
        ];
    }

    public function keluhanutama($result){
        $body = [];

        $text = trim($result->S);

        $body['text'] = $text;
        $body['len']  = mb_strlen($text);

        return $body;
    }

    public function statuslokalis($result){
        $text = $result->O;

        $parsed = $this->extractStatusLokalis($text);

        return [
            "raw"  => $parsed['raw'],
            "text" => $parsed['text'],
            "len"  => mb_strlen($parsed['text'])
        ];
    }

    public function ttv($result){
        $text = $result->O;

        $parsed = $this->extractTtv($text);

        return [
            "raw"  => $parsed['raw'],
            "text" => $parsed['text'],
            "len"  => mb_strlen($parsed['text'])
        ];
    }

    public function indikasiranap($result){
        $body = [];

        $text = trim($result->A); // hapus spasi + \n di awal & akhir

        $body['text'] = $text;
        $body['len'] = mb_strlen($text);

        return $body;
    }

    public function kontrolulang($result){
        $body = [];

        if($result->PULANG_ID === "P01"){
            $text = "Kontrol ulang ke ".$result->POLIKLINIK;
        }else{
            $text = "-";
        }

        $body['text'] = $text;
        $body['len']  = mb_strlen($text);

        return $body;
    }

    public function segaradibawa($result){
        $body = [];

        if($result->PULANG_ID === "P01"){
            $text = "Dibawa kembali ke fasilitas kesehatan apabila terjadi perburukan kondisi";
        }else{
            $text = "-";
        }

        $body['text'] = $text;
        $body['len']  = mb_strlen($text);

        return $body;
    }

    public function extractPositiveClinicalFlags($text){

        $raw = [];

        $positiveMarkers = [
            'keluhan',
            'mengeluh',
            'dirasakan',
            'terdapat',
            'ada',
            'mengalami',
            'tampak',
            'disertai',
            'positif'
        ];

        $negativeMarkers = [
            'tidak',
            'tidak ada',
            'tidak terdapat',
            'tidak disertai',
            'tidak mengalami',
            'disangkal',
            'menyangkal',
            'tanpa',
            'belum ada',
            'negatif'
        ];

        // 🔥 split ringan (jangan hancurkan struktur)
        $sentences = preg_split('/(?<=[\.\!\?])\s+|\n+/', trim($text));

        foreach ($sentences as $sentence){

            $sentence = trim($sentence);
            if ($sentence === '') continue;

            $lower = strtolower($sentence);

            $positive = false;
            $negative = false;

            // =========================
            // SYMBOL DETECTION
            // =========================
            if (preg_match('/\(\+\)|\+/', $lower)) $positive = true;
            // if (preg_match('/\(-\)|-/', $lower)) $negative = true;
            if (preg_match('/\(-\)/', $lower)) $negative = true;

            // =========================
            // KEYWORD DETECTION
            // =========================
            foreach ($negativeMarkers as $neg){
                if (strpos($lower, $neg) !== false) $negative = true;
            }

            foreach ($positiveMarkers as $pos){
                if (strpos($lower, $pos) !== false) $positive = true;
            }

            // =========================
            // DECISION
            // =========================
            if ($negative && !$positive) continue;

            if ($positive){
                $clean = $this->removeNegativeSegments($sentence);

                if ($clean !== ''){
                    $raw[] = $clean;
                }
            }
        }

        return [
            "raw"  => array_values(array_unique($raw)),
            "text" => implode("\n", $raw) // 🔥 JANGAN pakai titik
        ];
    }

    public function removeNegativeSegments($sentence){

        // hapus "xxx (-)"
        $sentence = preg_replace('/[^,\.]+?\(-\)/i', '', $sentence);

        // hapus "xxx-"
        $sentence = preg_replace('/\b\w+-(?=\s|,|\.|$)/i', '', $sentence);

        // rapikan koma
        $sentence = preg_replace('/\s*,\s*/', ', ', $sentence);
        $sentence = preg_replace('/(,\s*)+/', ', ', $sentence);

        // hapus koma di awal/akhir
        $sentence = trim($sentence, " ,.");

        // rapikan spasi
        $sentence = preg_replace('/\s{2,}/', ' ', $sentence);

        return $sentence;
    }

    public function extractSymptomsPerItem($text){

        $items = [];

        // =========================
        // NORMALISASI
        // =========================
        $text = strtolower($text);

        // 🔥 hapus bullet
        $text = preg_replace('/^\s*\*\s*/m', '', $text);

        // newline → koma
        $text = preg_replace('/\n+/', ',', $text);

        // titik → koma
        $text = preg_replace('/\.\s*/', ',', $text);

        $text = preg_replace('/\s*-\s*/', ',', $text);

        // "dan" → koma
        $text = preg_replace('/\s+dan\s+/i', ',', $text);

        // =========================
        // SPLIT
        // =========================
        $parts = preg_split('/,/', $text);

        foreach ($parts as $part){

            $part = trim($part);
            if ($part === '') continue;

            // =========================
            // CLEAN PREFIX (IMPORTANT)
            // =========================
            $part = preg_replace('/^pasien.*?keluhan/i', '', $part);
            $part = preg_replace('/^terdapat/i', '', $part);
            $part = preg_replace('/^keluhan( lainnya)? seperti/i', '', $part);
            $part = preg_replace('/^disertai/i', '', $part);
            $part = preg_replace('/^bab\s*/', '', $part);
            $part = preg_replace('/^bak\s*/', '', $part);

            // hapus sisa *
            $part = str_replace('*', '', $part);

            $part = trim($part);
            if ($part === '') continue;

            // =========================
            // SKIP NEGATIVE
            // =========================
            if (preg_match('/\(-\)|-\s*$/', $part)) continue;

            // =========================
            // POSITIVE DETECTION (DIKETATKAN)
            // =========================
            $isPositive = false;

            // simbol +
            if (preg_match('/\(\+\)|\+$/', $part)) {
                $isPositive = true;
            }

            // keyword klinis WAJIB (bukan bebas)
            if (preg_match('/\b(mual|muntah|nyeri|demam|lemas|batuk|sesak|keringat|mata merah|diare|encer)\b/i', $part)){
                $isPositive = true;
            }

            if (!$isPositive) continue;

            // =========================
            // FINAL CLEAN
            // =========================
            $clean = preg_replace('/\(\+\)|\+$/', '', $part);
            $clean = preg_replace('/pasien.*$/', '', $clean);

            // 🔥 hapus sisa kata tidak penting
            $clean = preg_replace('/^dengan\s*/', '', $clean);
            $clean = preg_replace('/^yang\s*/', '', $clean);

            $clean = trim($clean);

            if ($clean !== ''){
                $items[] = $clean;
            }
        }

        // =========================
        // UNIQUE
        // =========================
        $items = array_values(array_unique($items));

        // =========================
        // FORMAT
        // =========================
        $formatted = [];

        foreach ($items as $item){
            $formatted[] = ucfirst($item) . ' (+)';
        }

        return [
            "raw"  => $items,
            "text" => implode(', ', $formatted)
        ];
    }

    public function extractTtv($text){
        $lines = preg_split('/\n+/', $text);

        $raw = [];

        // =========================
        // MASTER KEYWORDS
        // =========================
        $ttvKeywords = [
            'ku',
            'kes',
            'kesadaran',
            'gcs',
            'tekanan darah',
            'frekuensi',
            'nadi',
            'napas',
            'respirasi',
            'suhu',
            'saturasi',
            'bb',
            'tb',
            'ews'
        ];

        $stopKeywords = [
            'Kepala',
            'mata',
            'hidung',
            'mulut',
            'leher',
            'jantung',
            'paru',
            'abdomen',
            'integumen',
            'extremitas',
            'hematologi',
            'kimia darah',
            'elektrolit',
            'imuno',
            'fungsi hati',
            'fungsi ginjal'
        ];

        // =========================
        // HELPER
        // =========================
        $matchKeyword = function ($line, $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($line, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        };

        // =========================
        // PARSING
        // =========================
        foreach ($lines as $line) {

            $line = trim($line);
            if ($line === '') continue;

            $lower = strtolower($line);

            // 🔥 STOP kalau masuk pemeriksaan fisik / lab
            if (
                preg_match('/^(' . implode('|', $stopKeywords) . ')\s*:/i', $line) ||
                preg_match('/^\./', $line) // .HEMATOLOGI dll
            ) {
                break; // ⛔ langsung berhenti total
            }

            // =========================
            // AMBIL TTV
            // =========================
            if (
                $matchKeyword($lower, $ttvKeywords) ||
                preg_match('/(mmhg|bpm|celcius|%|kg|cm|kali\/mnt)/i', $line)
            ) {
                $raw[] = $line;
            }
        }

        // hapus duplikat
        $raw = array_values(array_unique($raw));

        $finalText = implode("\n", $raw);

        return [
            "raw"  => $raw,
            "text" => $finalText,
            "len"  => mb_strlen($finalText)
        ];
    }

    public function extractStatusLokalis($text){
        $lines = preg_split('/\n+/', $text);

        $raw   = [];
        $clean = [];

        $allowed = false;
        $currentOrgan = null;

        // =========================
        // CONFIG
        // =========================
        $startKeywords = [
            'status lokalis',
            'status generalis'
        ];

        $organKeywords = [
            'kepala','mata','hidung','mulut','leher',
            'jantung','paru','abdomen','integumen',
            'ekstremitas','extremitas','thorax','telinga','tht'
        ];

        $stopKeywords = [
            'hematologi','kimia darah','elektrolit','imuno',
            'serologi','fungsi hati','fungsi ginjal',
            'diabetes','lab','gds','pemeriksaan',
            'diagnosa','dx','tx','terapi'
        ];

        // helper
        $matchKeyword = function ($line, $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($line, $keyword) !== false) return true;
            }
            return false;
        };

        foreach ($lines as $line) {

            $line = trim($line);
            if ($line === '') continue;

            $lower = strtolower($line);

            // =========================
            // STOP
            // =========================
            if ($matchKeyword($lower, $stopKeywords)) {
                $allowed = false;
                $currentOrgan = null;
                continue;
            }

            // =========================
            // START (HEADER)
            // =========================
            if ($matchKeyword($lower, $startKeywords)) {
                $allowed = true;
                continue;
            }

            // =========================
            // 🔥 AUTO START JIKA KETEMU ORGAN
            // =========================
            if (preg_match('/^(' . implode('|', $organKeywords) . ')\s*:/i', $line)) {
                $allowed = true;
            }

            if (!$allowed) continue;

            // =========================
            // ORGAN BARU
            // =========================
            if (preg_match('/^(' . implode('|', $organKeywords) . ')\s*:/i', $line, $match)) {
                $currentOrgan = ucfirst(strtolower($match[1]));

                $raw[] = $line;
                $clean[] = $line;
                continue;
            }

            // =========================
            // SUB ORGAN (Mata:, THT:, dll)
            // =========================
            if (preg_match('/^[A-Za-z ]+\s*:/', $line) && $currentOrgan !== null) {
                $raw[] = $line;
                $clean[] = $line;
                continue;
            }

            // =========================
            // LANJUTAN
            // =========================
            if ($currentOrgan !== null) {
                $raw[] = $line;
                $clean[] = $line;
            }
        }

        $finalText = implode("\n", $clean);

        return [
            "raw"  => $raw,
            "text" => $finalText,
            "len"  => mb_strlen($finalText)
        ];
    }

    public function extractRiwayatClinical($text, $config = []){
        // =========================
        // DEFAULT CONFIG
        // =========================
        $defaults = [

            "startKeywords" => [
                'rw', 'riw', 'riwayat', 'h/o', 'history',
                'ckd', 'dm', 'hipertensi',
                'dx', 'tx'
            ],

            "stopKeywords" => [
                'poli', 'waktu', 'usg'
            ],

            "negativeKeywords" => [
                'disangkal',
                'tidak diketahui'
            ],

            "negativeSymbols" => [
                '(-)', '-/-'
            ],

            "headers" => [
                'dx' => 'Dx:',
                'tx' => 'Tx:'  
            ]
        ];

        $cfg = array_merge($defaults, $config);

        $lines = preg_split('/\n+/', $text);

        $raw = [];
        $formatted = [];

        $inHistoryBlock = false;

        $skipSocialBlock = false;
        $skipPernikahan  = false;

        // =========================
        // 🔥 OBSTETRI STATE
        // =========================
        $obstetriMode = false;
        $currentObstetri = null;
        $obstetriList = [];

        $seenHeaders = array_fill_keys(array_keys($cfg['headers']), false);

        foreach ($lines as $line) {

            $line = trim($line);
            if ($line === '') continue;

            $lower = strtolower($line);

            // =========================
            // 🔥 SKIP RIWAYAT PERNIKAHAN FULL BLOCK
            // =========================
            if (strpos($lower, 'riwayat pernikahan') !== false) {
                $skipPernikahan = true;
                continue;
            }

            if ($skipPernikahan) {
                if (preg_match('/^riwayat\s+/i', $lower) && strpos($lower, 'riwayat pernikahan') === false) {
                    $skipPernikahan = false;
                } else {
                    continue;
                }
            }

            // =========================
            // 🔥 SKIP RPK TOTAL
            // =========================
            if (strpos($lower, 'rpk') !== false) {
                continue;
            }

            // =========================
            // 🔥 RPD CLEAN (FIRST SENTENCE ONLY)
            // =========================
            if (strpos($lower, 'rpd') !== false) {
                $parts = preg_split('/\.\s*/', $line);
                $line = $parts[0];
            }

            // =========================
            // 🔥 SKIP RIWAYAT SOSIAL BLOCK
            // =========================
            if (strpos($lower, 'riwayat sosial') !== false) {
                $skipSocialBlock = true;
                continue;
            }

            if ($skipSocialBlock) {
                continue;
            }

            // =========================
            // START DETECTION
            // =========================
            foreach ($cfg['startKeywords'] as $kw) {
                if (strpos($lower, $kw) !== false) {
                    $inHistoryBlock = true;
                    break;
                }
            }

            if (strpos($line, '+') !== false || strpos($line, '?') !== false) {
                $inHistoryBlock = true;
            }

            // =========================
            // STOP DETECTION
            // =========================
            foreach ($cfg['stopKeywords'] as $kw) {
                if (strpos($lower, $kw) !== false) {
                    $inHistoryBlock = false;
                    break;
                }
            }

            if (!$inHistoryBlock) continue;

            // =========================
            // SPLIT PER ITEM
            // =========================
            $line = preg_replace('/\s*,\s*/', ' | ', $line);
            $chunks = explode(' | ', $line);

            foreach ($chunks as $chunk) {

                $chunk = trim($chunk);
                $chunk = preg_replace('/\x{00A0}/u', '', $chunk);
                $chunk = preg_replace('/\s+/', ' ', $chunk);

                if ($chunk === '') continue;

                $lowerChunk = strtolower($chunk);

                // =========================
                // 🔥 EXCLUDE SOCIAL DETAIL
                // =========================
                if (preg_match('/\b(pasien|suami|karyawan|pendidikan|sma|stm|bekerja)\b/i', $lowerChunk)) {
                    continue;
                }

                // =========================
                // 🔥 EXCLUDE LIFESTYLE
                // =========================
                if (preg_match('/\b(merokok|minum alkohol|alkohol)\b/i', $lowerChunk)) {
                    continue;
                }

                // =========================
                // 🔥 OBSTETRI DETECTOR
                // =========================
                if (preg_match('/riwayat obstetri/i', $chunk)) {

                    $obstetriMode = true;
                    $currentObstetri = $chunk;

                    $raw[] = $chunk;
                    $formatted[] = $chunk;

                    continue;
                }

                // =========================
                // 🔥 OBSTETRI GROUPING FIX
                // =========================
                if ($obstetriMode) {

                    if (preg_match('/^\d+\./', $chunk)) {

                        if ($currentObstetri !== null) {
                            $obstetriList[] = $currentObstetri;
                        }

                        $currentObstetri = $chunk;
                        continue;
                    }

                    if ($currentObstetri !== null) {
                        $currentObstetri .= ', ' . $chunk;
                    }

                    continue;
                }

                // =========================
                // NEGATIVE FILTER
                // =========================
                $isNegative = false;

                foreach ($cfg['negativeKeywords'] as $neg) {
                    if (strpos($lowerChunk, $neg) !== false) {
                        $isNegative = true;
                        break;
                    }
                }

                foreach ($cfg['negativeSymbols'] as $sym) {
                    if (strpos($chunk, $sym) !== false) {
                        $isNegative = true;
                        break;
                    }
                }

                if (preg_match('/-\s*$/', $chunk)) {
                    $isNegative = true;
                }

                if ($isNegative) continue;

                // =========================
                // HEADER HANDLING
                // =========================
                foreach ($cfg['headers'] as $key => $label) {

                    if (preg_match('/^' . preg_quote($key, '/') . '\s*:/i', $chunk)) {

                        if ($seenHeaders[$key]) continue;

                        $seenHeaders[$key] = true;

                        $raw[] = $label;
                        $formatted[] = $label;

                        continue 2;
                    }
                }

                // =========================
                // FINAL CLEAN
                // =========================
                $chunk = trim($chunk);
                

                if ($chunk === '') continue;

                $raw[] = $chunk;
                $formatted[] = $chunk;
            }
        }

        // =========================
        // FLUSH OBSTETRI LAST ITEM
        // =========================
        if ($currentObstetri !== null) {
            $obstetriList[] = $currentObstetri;
        }

        // =========================
        // MERGE OBSTETRI RESULT
        // =========================
        if (!empty($obstetriList)) {
            $raw = array_merge($raw, $obstetriList);
            $formatted = array_merge($formatted, $obstetriList);
        }

        // =========================
        // FINAL OUTPUT
        // =========================
        $raw = array_values(array_unique($raw));
        $formatted = array_values(array_unique($formatted));

        return [
            "raw"   => $raw,
            "text"  => implode("\n", $formatted),
            "count" => count($raw)
        ];
    }

    public function generateresume_POST(){
        headerlogresume();

        $resultlistrresume = $this->md->listrresume();

        if(empty($resultlistrresume)){
            echo color('red')."Data Tidak Ditemukan";
            return;
        }

        foreach($resultlistrresume as $a){
            $statusColor = "red";
            $statusMsg   = "";

            $episodeid = $a->EPISODE_ID;

            // =========================
            // CALL API
            // =========================
            $url = "http://10.12.120.58/rsudpasarminggu/prod/analisadata/index.php/generateresumeai/".$episodeid;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['episodeid' => $episodeid]);
            $response = curl_exec($ch);

            // =========================
            // ERROR CURL
            // =========================
            if (curl_errno($ch)) {

                $statusMsg   = "CURL ERROR: ".curl_error($ch);
                $statusColor = "red";

                curl_close($ch);

                echo formatlog($a->PASIEN_ID,$a->EPISODE_ID,$a->TGLKELUAR,$a->DOKTER_ID,$statusMsg,'cyan','cyan','cyan','cyan',$statusColor);
                continue;
            }

            curl_close($ch);

            // =========================
            // DECODE JSON
            // =========================
            $result = json_decode($response, true);

            if (!$result) {
                $statusMsg   = "INVALID JSON";
                $statusColor = "red";
            } else {

                // =========================
                // HANDLE RESPONSE BARU
                // =========================
                if (isset($result['status']) && $result['status'] === true && $result['code'] == 200) {

                    $dataresume  = [];
                    
                    $labText   = $result['sourcedata'][0]['penunjang']['laboratorium']['text'] ?? '';
                    $chunkSize = 4000;
                    $chunks    = str_split($labText, $chunkSize);

                    foreach ($chunks as $i => $chunk) {
                        if ($i >= 3) break;

                        if ($i == 0) {
                            $dataresume['LAB'] = $chunk;
                        } else {
                            $dataresume['LAB'.($i+1)] = $chunk;
                        }
                    }

                    $dataresume['PASIEN_ID']         = $result['transaksi']['pasienid'];
                    $dataresume['EPISODE_ID']        = $result['transaksi']['episodeid'];
                    $dataresume['DOKTER_ID']         = $result['transaksi']['dokterid'];
                    $dataresume['RUANG_ID']          = $result['transaksi']['ruangid'];
                    $dataresume['CREATED_BY']        = $result['transaksi']['dokterid'];
                    $dataresume['KONDISI']           = $result['transaksi']['pulang'];
                    $dataresume['KONDISI_PULANG_ID'] = $result['transaksi']['pulangid'];
                    $dataresume['KELUHAN']           = $result['sourcedata'][0]['riwayat']['keluhanutama']['text'];
                    $dataresume['GEJALA']            = $result['sourcedata'][0]['riwayat']['gejala']['text'];
                    $dataresume['RIWAYATPS']         = $result['sourcedata'][0]['riwayat']['sekarang']['text'];
                    $dataresume['RIWAYATPD']         = $result['sourcedata'][0]['riwayat']['dahulu']['text'];
                    $dataresume['STATUS']            = $result['sourcedata'][0]['pemeriksaanfisik']['statuslokalis']['text'];
                    $dataresume['VITAL']             = $result['sourcedata'][0]['pemeriksaanfisik']['ttv']['text'];
                    $dataresume['INDIKASI']          = $result['sourcedata'][0]['diagnosis']['indikasiranap']['text'];
                    $dataresume['LAINNYA']           = $result['sourcedata'][0]['penunjang']['radiologi']['text'];
                    $dataresume['OBATP']             = $result['sourcedata'][0]['penunjang']['obat']['pulang']['text'];
                    $dataresume['KONTROL']           = $result['sourcedata'][0]['kontrolulang']['text'];
                    $dataresume['INTRUKSI']         = $result['sourcedata'][0]['segeradibawa']['text'];


                    if($this->md->insertresume($dataresume)){
                        $statusMsg   = "Success";
                        $statusColor = "green";

                        echo formatlog($a->PASIEN_ID,$a->EPISODE_ID,$a->TGLKELUAR,$a->DOKTER_ID,$statusMsg,'cyan','cyan','cyan','cyan',$statusColor);
                    }else{
                        $statusMsg   = "Failed Save Resume";
                        $statusColor = "green";

                        echo formatlog($a->PASIEN_ID,$a->EPISODE_ID,$a->TGLKELUAR,$a->DOKTER_ID,$statusMsg,'cyan','cyan','cyan','cyan',$statusColor);
                    }
                } else {

                    $msg  = $result['message'] ?? 'FAILED';
                    $code = $result['code'] ?? 'UNKNOWN';

                    $statusMsg   = "FAILED ($code): ".$msg;
                    $statusColor = "red";

                    echo formatlog($a->PASIEN_ID,$a->EPISODE_ID,$a->TGLKELUAR,$a->DOKTER_ID,$statusMsg,'cyan','cyan','cyan','cyan',$statusColor);
                }
            }
            
        }

        
    }
    
}