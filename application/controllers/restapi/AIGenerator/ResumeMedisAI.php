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

        $resultkunjungan        = $this->md->kunjungan($episodeid);
        $resultradiologi        = $this->md->radiologi($episodeid);
        $resultlaboratoriumhd   = $this->md->laboratoriumhd($episodeid);
        $resultkeluhanutama     = $this->md->keluhanutama($episodeid);
        $resultgejala           = $this->md->gejala($episodeid);
        $resultpemeriksaanfisik = $this->md->pemeriksaanfisik($episodeid);

        $item = preg_match_all('/([a-zA-Z ]+)\s*(?:\(\+\)|\+)/i', $resultgejala->RESULT, $matches);
        $rawhighlight = array_map(function($item){return trim($item);}, $matches[1]);
        $highlight    = implode(', ', array_map(function($item){return ucfirst(strtolower(trim($item))) . ' (+)';}, $matches[1]));

        $textpemeriksaanfisik = $resultpemeriksaanfisik->TEXT_DATA;
        
        $kunjungan['pasienid']  = $resultkunjungan->PASIEN_ID;
        $kunjungan['episodeid'] = $resultkunjungan->EPISODE_ID;

        $sourcedata['riwayat']['keluhanutama']['raw']            = [];
        $sourcedata['riwayat']['keluhanutama']['text']           = ltrim($resultkeluhanutama->KELUHAN, "\n");
        $sourcedata['diagnosis']['indikasiranap']['raw']         = [];
        $sourcedata['diagnosis']['indikasiranap']['text']        = ltrim($resultkeluhanutama->INDIKASIRANAP, "\n");
        $sourcedata['riwayat']['gejala']['raw']                  = $rawhighlight;
        $sourcedata['riwayat']['gejala']['text']                 = $highlight;
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
            foreach ($resultlaboratoriumhd as $row) {
                $resultlab['order']['sampelid']    = $row['SAMPEL_ID'];
                $resultlab['order']['registerid']  = $row['REGISTRASI_ID'];
                $resultlab['order']['createddate'] = $row['CREATEDDATE'];

                $sourcedata['penunjang']['laboratorium']['raw'][]  = $resultlab;
            }
        }else{

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

    function cleanText($text) {
        $text = str_replace("\r", "", $text); // hilangkan CR
        $text = preg_replace('/(\n\s*){2,}/', "\n", $text); // rapikan newline
        return trim($text);
    }

    function ambilTTV($text, $startList = [], $endList = []) {

        $text = $this->cleanText($text);

        // START keyword
        $startList = !empty($startList) ? $startList : [
            'Kes:',
            'Kesadaran:',
            'KU:',
            'TTV saat di IGD'
        ];

        // END keyword
        $endList = !empty($endList) ? $endList : [
            'Mata',
            'Status Generalis',
            'Kepala'
        ];

        // cari start
        $startPos = false;

        foreach ($startList as $start) {
            $pos = stripos($text, $start);
            if ($pos !== false) {
                $startPos = $pos; // ⬅️ mulai dari label, bukan setelahnya
                break;
            }
        }

        // fallback
        if ($startPos === false) {
            $startPos = 0;
        }

        // cari end
        $endPos = strlen($text);
        foreach ($endList as $end) {
            $pos = stripos($text, $end);
            if ($pos !== false && $pos > $startPos && $pos < $endPos) {
                $endPos = $pos;
            }
        }

        $ttv = substr($text, $startPos, $endPos - $startPos);

        return $this->cleanText($ttv);
    }

    function ambilStatusLokalis($text, $startList = [], $endList = []) {

        $text = $this->cleanText($text);

        // START dari Mata (sesuai data real)
        $startList = !empty($startList) ? $startList : [
            'Mata',
            'Status Generalis'
        ];

        // END → sebelum penunjang / lab
        $endList   = !empty($endList)   ? $endList   : [
            'Hasil lab',
            'Hasil laboratorium',
            'Lab',
            'HEMATOLOGI',
            'KIMIA',
            'LAB',
            'ELEKTROLIT',
            'HEMATOLOGI',
            'Pemeriksaan Penunjang'
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

        return $this->cleanText($status);
    }

    private function getOriginalPosition($originalText, $normalizedText, $normalizedPos) {
        // untuk kasus kamu sebenarnya tidak perlu mapping kompleks
        // cukup kembalikan posisi asli berdasarkan normalized string length

        return strlen(substr($originalText, 0, $normalizedPos));
    }




    
}