<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	function testReturn()
    {
        $s = '';
        for ($i=0; $i<100000; $i++)
            $s .= "HELLO MY DEAR FRIEND $i cool awesome ";

        return $s;
    }

    function testEcho()
    {
        for ($i=0; $i<100000; $i++)
            echo "HELLO MY DEAR FRIEND $i cool awesome ";
    }

	public function index()
	{
        $this->output->enable_profiler(TRUE);

        //echo $this->testReturn();
        $this->testEcho();

		$this->load->view('welcome_message');
	}

	public function index2()
	{
        $this->output->enable_profiler(TRUE);

		echo APPPATH;
		//modules::load('pages/home')->index();
		modules::load('pages/Home')->index();
	}
}
