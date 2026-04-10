<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/REST_Controller.php';
require 'vendor/autoload.php';

class ResumeMedisAI extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("ModelResumeMedisAI","md");
    }

    public function generateresumeai_post($episodeid){
        $body        = [];
        $sourcedata  = [];
        $aigenerated = [];
        $kunjungan   = [];
        $norm        = "";

        $resultkunjungan        = $this->md->kunjungan($episodeid);
        $resultradiologi        = $this->md->radiologi($episodeid);
        $resultlaboratoriumhd   = $this->md->laboratoriumhd($episodeid);
        $resultkeluhanutama     = $this->md->keluhanutama($episodeid);
        $resultgejala           = $this->md->gejala($episodeid);

        $item = preg_match_all('/([a-zA-Z ]+)\s*(?:\(\+\)|\+)/i', $resultgejala->RESULT, $matches);
        $rawhighlight = array_map(function($item){return trim($item);}, $matches[1]);
        $highlight    = implode(', ', array_map(function($item){return ucfirst(strtolower(trim($item))) . ' (+)';}, $matches[1]));

        $textriwayatsekarang = ltrim($resultkeluhanutama->RIWAYATSEKARANG, "\n");

        // 🔥 hapus negatif & tidak relevan
        $textriwayatsekarang = preg_replace('/\b[^,.\n]*\(-\)[^,.\n]*[,.]?\s*/i', '', $textriwayatsekarang);
        $textriwayatsekarang = preg_replace('/\b[^,.\n]*disangkal[^,.\n]*[,.]?\s*/i', '', $textriwayatsekarang);

        // 🔥 khusus "normal" → lebih selektif
        $textriwayatsekarang = preg_replace('/\b(dalam batas normal|bab dan bak normal|normal)\b[,.]?\s*/i', '', $textriwayatsekarang);

        // 🔥 rapikan teks
        // hapus koma berulang
        $textriwayatsekarang = preg_replace('/,+/', ',', $textriwayatsekarang);

        // hapus koma di awal
        $textriwayatsekarang = preg_replace('/^,\s*/', '', $textriwayatsekarang);

        // hapus koma di akhir
        $textriwayatsekarang = preg_replace('/,\s*$/', '', $textriwayatsekarang);

        // trim final
        $textriwayatsekarang = trim($textriwayatsekarang);

        // 🔥 pecah jadi array
        $listRiwayatSekarang = preg_split('/[,\\n]/', $textriwayatsekarang);

        $rawriwayatsekarang = [];

        foreach ($listRiwayatSekarang as $riwayatItem) {
            $riwayatItem = trim($riwayatItem);
            if (!$riwayatItem) continue;

            // 🔥 bersihkan tanda (+)
            $cleanRiwayat = trim(preg_replace('/\(\+\)/', '', $riwayatItem));

            if ($cleanRiwayat) {
                $rawriwayatsekarang[] = strtolower($cleanRiwayat);
            }
        }


        $textriwayatdahulu = ltrim($resultkeluhanutama->RIWAYATDAHULU, "\n");

        // 🔥 pecah per koma / enter
        $listRiwayatDahulu = preg_split('/[,\\n]/', $textriwayatdahulu);

        $rawriwayatdahulu = [];
        $textList = [];

        foreach ($listRiwayatDahulu as $riwayatDahuluItem) {
            $riwayatDahuluItem = trim($riwayatDahuluItem);
            if (!$riwayatDahuluItem) continue;

            // 🔥 hanya ambil yang ada (+) atau +
            if (
                stripos($riwayatDahuluItem, '(+)') === false &&
                stripos($riwayatDahuluItem, '+') === false
            ) continue;

            // 🔥 bersihkan tanda (+)
            $cleanRiwayat = trim(preg_replace('/\(\+\)|\+/', '', $riwayatDahuluItem));

            if ($cleanRiwayat) {
                $rawriwayatdahulu[] = strtolower($cleanRiwayat);
                $textList[] = $cleanRiwayat . ' (+)';
            }
        }

        // 🔥 gabungkan text
        $textriwayatdahulu = implode(', ', $textList);
        


        $textpemeriksaanfisik = $resultkeluhanutama->TEXT_DATA;
        $norm                 = $resultkunjungan->MRPASIEN;
        
        $kunjungan['pasienid']  = $resultkunjungan->PASIEN_ID;
        $kunjungan['episodeid'] = $resultkunjungan->EPISODE_ID;

        $sourcedata['riwayat']['keluhanutama']['raw']  = [];
        $sourcedata['riwayat']['keluhanutama']['text'] = ltrim($resultkeluhanutama->KELUHAN, "\n");
        $sourcedata['riwayat']['gejala']['raw']        = $rawhighlight;
        $sourcedata['riwayat']['gejala']['text']       = $highlight;
        $sourcedata['riwayat']['sekarang']['raw']      = $rawriwayatsekarang;
        $sourcedata['riwayat']['sekarang']['text']     = $textriwayatsekarang;
        $sourcedata['riwayat']['dahulu']['raw']        = $rawriwayatdahulu;
        $sourcedata['riwayat']['dahulu']['text']       = $textriwayatdahulu;

        $sourcedata['diagnosis']['indikasiranap']['raw']         = [];
        $sourcedata['diagnosis']['indikasiranap']['text']        = ltrim($resultkeluhanutama->INDIKASIRANAP, "\n");
        
        $sourcedata['pemeriksaanfisik']['ttv']['raw']            = [];
        $sourcedata['pemeriksaanfisik']['ttv']['text']           = $this->ambilTTV($textpemeriksaanfisik);
        $sourcedata['pemeriksaanfisik']['statuslokalis']['raw']  = [];
        $sourcedata['pemeriksaanfisik']['statuslokalis']['text'] = $this->ambilStatusLokalis($textpemeriksaanfisik);


        if($resultkunjungan->PULANG_ID==="P01" || $resultkunjungan->PULANG_ID===null){
            $sourcedata['kontrolulang']['raw']  = [];
            $sourcedata['kontrolulang']['text'] = "Kontrol ulang ke " . ($resultkunjungan->POLIKLINIK ?? '-');
            $sourcedata['segeradibawa']['raw']  = [];
            $sourcedata['segeradibawa']['text'] = "Dibawa kembali ke fasilitas kesehatan apabila terjadi perburukan kondisi";

            $resultobat = $this->md->obat($episodeid);

            $sourcedata['penunjang']['obat']['perawatan']['raw'] = [];
            $sourcedata['penunjang']['obat']['pulang']['raw']    = [];

            foreach ($resultobat as $a) {
                $item             = [];
                $item['obatid']   = $a['OBAT_ID'];
                $item['namaobat'] = $a['NAMA_OBAT'];

                if ($a['JENIS_RESEP'] == "1") {
                    $sourcedata['penunjang']['obat']['perawatan']['raw'][] = $item;
                } else {
                    $sourcedata['penunjang']['obat']['pulang']['raw'][] = $item;
                }
            }

            $perawatan_text = array_map(function($item) {return "- {$item['namaobat']}";}, $sourcedata['penunjang']['obat']['perawatan']['raw']);
            $pulang_text    = array_map(function($item) {return "- {$item['namaobat']}";}, $sourcedata['penunjang']['obat']['pulang']['raw']);


            $sourcedata['penunjang']['obat']['perawatan']['text'] = implode("\n", $perawatan_text);
            $sourcedata['penunjang']['obat']['pulang']['text'] = implode("\n", $pulang_text);

        }else{
            $sourcedata['kontrolulang']['raw']                    = [];
            $sourcedata['kontrolulang']['text']                   = "-";
            $sourcedata['segeradibawa']['raw']                    = [];
            $sourcedata['segeradibawa']['text']                   = "-";
            $sourcedata['penunjang']['obat']['perawatan']['raw']  = [];
            $sourcedata['penunjang']['obat']['pulang']['raw']     = [];
            $sourcedata['penunjang']['obat']['pulang']['text']    = "-";
            $sourcedata['penunjang']['obat']['perawatan']['text'] = "-";
        }

        if(!empty($resultradiologi)){
            foreach ($resultradiologi as $row) {
                $resultrad[] = [
                    'namapemeriksaan' => $row['NAMAPEMERIKSAAN'],
                    'result'          => $row['RESULT'],
                    'createddate'     => $row['CREATEDDATE']
                ];
            }
            $sourcedata['penunjang']['radiologi']['raw']  = $resultrad;
            $sourcedata['penunjang']['radiologi']['text'] = implode("\n\n", array_map(function($item){return $item['createddate'] . " " . $item['namapemeriksaan'] . "\n" . "Conclusion :\n" . $item['result'];}, $resultrad));

        }else{
            $sourcedata['penunjang']['radiologi']['raw']  = [];
            $sourcedata['penunjang']['radiologi']['text'] = "-";
        }
        
        if(!empty($resultlaboratoriumhd)){
            $resultlab = [];

            foreach ($resultlaboratoriumhd as $i => $row) {

                $resultlab[$i] = [
                    'sampelid'    => $row['SAMPEL_ID'] ?? '',
                    'registerid'  => $row['REGISTRASI_ID'] ?? '',
                    'createddate' => $row['CREATEDDATE'] ?? '',
                    'hasil'       => []
                ];

                $resultlaboratoriumdt = $this->md->laboratoriumdt($row['SAMPEL_ID'], $norm);

                foreach ($resultlaboratoriumdt as $rowdetail) {
                    $resultlab[$i]['hasil'][] = [
                        'namates' => $rowdetail['NAMA_TES'] ?? '',
                        'unit'    => $rowdetail['UNITS'] ?? '',
                        'result'  => $rowdetail['RESULT_VALUE'] ?? '',
                        'flag'    => $rowdetail['RESULT_FLAG'] ?? ''
                    ];
                }
            }

            $sourcedata['penunjang']['laboratorium']['raw'] = $resultlab;
            $sourcedata['penunjang']['laboratorium']['text'] = implode("\n\n", array_map(function($item){

                $hasil = implode("\n", (function($list){

                    $output = [];
                    $isFirstHeader = true;

                    foreach ($list as $h) {

                        $nama   = $h['namates'] ?? '-';
                        $unit   = $h['unit'] ?? '';
                        $result = $h['result'] ?? '';

                        // ambil indent asli
                        preg_match('/^\s*/', $nama, $match);
                        $indent = $match[0];

                        $nama_bersih = trim($nama);

                        // HEADER (tidak ada result)
                        if (empty($result)) {

                            if ($isFirstHeader) {
                                $output[] = $indent . $nama_bersih . " :";
                                $isFirstHeader = false;
                            } else {
                                $output[] = "\n" . $indent . $nama_bersih . " :";
                            }

                            continue;
                        }

                        // DATA
                        $unit_text = !empty($unit) ? " " . $unit : "";
                        $output[] = $indent . $nama_bersih . " : " . $result . $unit_text;
                    }

                    return $output;

                })($item['hasil'] ?? []));

                return ($item['createddate'] ?? '-') .
                    "\nNo Sampel : " . ($item['sampelid'] ?? '-') .
                    "\n===RESULT===\n" . $hasil;

            }, $resultlab));
        }else{
            $sourcedata['penunjang']['laboratorium']['raw'][] = [];
            $sourcedata['penunjang']['laboratorium']['text']  = "-";
        }
        
        $body['status']                = true;
        $body['code']                  = 200;
        $body['message']               = "success";
        $body['transaksi']             = $kunjungan;
        $body['sourcedata'][]          = $sourcedata;
        $body['aigenerated'][]         = $aigenerated;
        $body['metadata']['timestamp'] = date('Y-m-d H:i:s');

        $this->response($body, 200);
    }

    function normalizeText($text) {
        $text = str_replace("\r", "", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text); // rapikan spasi
        return $text;
    }

    function ambilTTV($text, $startList = [], $endList = []) {

        $text = $this->normalizeText($text);

        $startList = !empty($startList) ? $startList : [
            'KU',
            'Kesadaran',
            'GCS',
            'Tekanan darah'
        ];

        $endList = !empty($endList) ? $endList : [
            '20.30',
            'Status Generalis',
            'Kepala',
            'Thorax',
            '.HEMATOLOGI'
        ];

        // START
        $startPos = false;
        foreach ($startList as $start) {
            $pos = stripos($text, $start);
            if ($pos !== false) {
                $startPos = $pos;
                break;
            }
        }

        if ($startPos === false) return '';

        // END
        $endPos = strlen($text);
        foreach ($endList as $end) {
            $pos = stripos($text, $end);
            if ($pos !== false && $pos > $startPos && $pos < $endPos) {
                $endPos = $pos;
            }
        }

        $ttv = substr($text, $startPos, $endPos - $startPos);

        return trim($ttv);
    }

    function ambilStatusLokalis($text, $startList = [], $endList = []) {

        $text = $this->normalizeText($text);

        $startList = !empty($startList) ? $startList : [
            'Status Generalis',
            'Kepala',
            'Mata'
        ];

        $endList = !empty($endList) ? $endList : [
            'HASIL RADIOLOGI',
            'HASIL LABORATORIUM',
            'HEMATOLOGI',
            'KIMIA DARAH',
            'ELEKTROLIT',
            'Pemeriksaan Penunjang',
            'GDS',
            'Lab',
            '.URINALISA',
            'Pemeriksaan',
            'Hasil'
        ];

        // START
        $startPos = false;
        foreach ($startList as $start) {
            $pos = stripos($text, $start);
            if ($pos !== false) {
                $startPos = $pos;
                break;
            }
        }

        if ($startPos === false) return '';

        // END
        $endPos = strlen($text);
        foreach ($endList as $end) {
            $pos = stripos($text, $end);
            if ($pos !== false && $pos > $startPos && $pos < $endPos) {
                $endPos = $pos;
            }
        }

        $status = substr($text, $startPos, $endPos - $startPos);

        return trim($status);
    }

    // function cleanText($text) {
    //     $text = str_replace("\r", "", $text); // hilangkan CR
        
    //     // rapikan newline berlebih di tengah jadi max 1
    //     $text = preg_replace('/(\n\s*){2,}/', "\n", $text);
        
    //     // hapus newline di awal
    //     $text = preg_replace('/^\n+/', '', $text);
        
    //     // hapus newline di akhir
    //     $text = preg_replace('/\n+$/', '', $text);

    //     return trim($text);
    // }

    // function ambilTTV($text, $startList = [], $endList = []) {


    //     // START keyword
    //     $startList = !empty($startList) ? $startList : [
    //         'Kes:',
    //         'Kesadaran:',
    //         'KU:',
    //         'TTV saat di IGD'
    //     ];

    //     // END keyword
    //     $endList = !empty($endList) ? $endList : [
    //         'Mata',
    //         'Status Generalis',
    //         'Kepala'
    //     ];

    //     // cari start
    //     $startPos = false;

    //     foreach ($startList as $start) {
    //         $pos = stripos($text, $start);
    //         if ($pos !== false) {
    //             $startPos = $pos; // ⬅️ mulai dari label, bukan setelahnya
    //             break;
    //         }
    //     }

    //     // fallback
    //     if ($startPos === false) {
    //         $startPos = 0;
    //     }

    //     // cari end
    //     $endPos = strlen($text);
    //     foreach ($endList as $end) {
    //         $pos = stripos($text, $end);
    //         if ($pos !== false && $pos > $startPos && $pos < $endPos) {
    //             $endPos = $pos;
    //         }
    //     }

    //     $ttv = substr($text, $startPos, $endPos - $startPos);

    // }

    // function ambilStatusLokalis($text, $startList = [], $endList = []) {


    //     // START dari Mata (sesuai data real)
    //     $startList = !empty($startList) ? $startList : [
    //         'Mata',
    //         'Status Generalis'
    //     ];

    //     // END → sebelum penunjang / lab
    //     $endList   = !empty($endList)   ? $endList   : [
    //         'Hasil lab',
    //         'Hasil laboratorium',
    //         'Lab',
    //         'HEMATOLOGI',
    //         'KIMIA',
    //         'LAB',
    //         'ELEKTROLIT',
    //         'HEMATOLOGI',
    //         'Pemeriksaan Penunjang'
    //     ];

    //     // START
    //     $startPos = false;
    //     foreach ($startList as $start) {
    //         $pos = stripos($text, $start);
    //         if ($pos !== false) {
    //             $startPos = $pos;
    //             break;
    //         }
    //     }

    //     if ($startPos === false) return '';

    //     // END
    //     $endPos = strlen($text);
    //     foreach ($endList as $end) {
    //         $pos = stripos($text, $end);
    //         if ($pos !== false && $pos > $startPos && $pos < $endPos) {
    //             $endPos = $pos;
    //         }
    //     }

    //     $status = substr($text, $startPos, $endPos - $startPos);

    // }

    // private function getOriginalPosition($originalText, $normalizedText, $normalizedPos) {
    //     // untuk kasus kamu sebenarnya tidak perlu mapping kompleks
    //     // cukup kembalikan posisi asli berdasarkan normalized string length

    //     return strlen(substr($originalText, 0, $normalizedPos));
    // }




    
}