<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Dompdf\Dompdf;

class Welcome extends CI_Controller {

    public function index(){
        redirect('auth/sign');
    }

    function tes(){
        $data['title'] = 'Laporan';
        $data['nama'] = 'Testing';

        // Ambil HTML dari view
        $html = $this->load->view('v_tespdf', $data, true);

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('laporan.pdf', ['Attachment' => false]);
    }
}
