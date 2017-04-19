<?php
/**
 * Created by PhpStorm.
 * User: BIT TECHNOLOGIES
 * Date: 10/12/2016
 * Time: 7:07 PM
 */
class My404 extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->helper('url');

        $this->output->set_status_header('404');
        $data['content'] = 'error_404'; // View name

        $this->load->view('404/404.php',$data);
    }
}
?>