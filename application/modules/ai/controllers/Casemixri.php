<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Casemixri extends CI_Controller {

		public function __construct(){
            parent:: __construct();
			rootsystem::system();
			$this->load->model("Modelcasemixri","md");
        }

		public function index(){
			$this->template->load("template/template-sidebar","v_casemixri");
		}

        public function casemixri(){
            $startdate = $this->input->post("startDate");
			$endate    = $this->input->post("endDate");
			$result = $this->md->casemixri($startdate,$endate);
            
			if(!empty($result)){
				$json["responCode"]   = "00";
				$json["responHead"]   = "success";
				$json["responDesc"]   = "Data Di Temukan";
				$json['responResult'] = $result;
            }else{
                $json["responCode"] = "01";
                $json["responHead"] = "info";
                $json["responDesc"] = "Data Tidak Di Temukan";
            }

            echo json_encode($json);
        }

		

	}
?>