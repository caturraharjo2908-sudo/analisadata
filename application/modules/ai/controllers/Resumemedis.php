<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Resumemedis extends CI_Controller {

		public function __construct(){
            parent:: __construct();
			rootsystem::system();
			$this->load->model("Modelresumemedis","md");
        }

		public function index(){
			$this->template->load("template/template-sidebar","v_resumemedis");
		}

		public function listpasien(){
            $dokterid = "DR RU0000000001";
            $result    = $this->md->listpasien($dokterid);
            
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


		public function resumemedis(){
			$episodeid = $this->input->post('episodeid');

			$url = "http://10.12.120.58/rsudpasarminggu/prod/analisadata/index.php/generateresumeai/".$episodeid;

			// init curl
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			// ?? ubah ke POST
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, [
				'episodeid' => $episodeid
			]);

			$response = curl_exec($ch);

			// handle error
			if (curl_errno($ch)) {
				echo json_encode([
					"responCode" => "01",
					"responDesc" => curl_error($ch)
				]);
				curl_close($ch);
				return;
			}

			curl_close($ch);

			// decode
			$result = json_decode($response, true);


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