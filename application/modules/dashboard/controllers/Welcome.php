<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
    require 'vendor/autoload.php';
    use Restserver\Libraries\REST_Controller;
    require APPPATH . '/libraries/REST_Controller.php';

	class Welcome extends CI_Controller {

		public function __construct(){
            parent:: __construct();
			rootsystem::system();
        }

		public function index(){
			$this->template->load("template/template-sidebar","v_welcome");
		}

        
	}
?>