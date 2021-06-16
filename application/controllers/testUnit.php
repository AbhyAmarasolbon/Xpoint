<?php

class TestUnit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('unit_test');
    }

    // FUNCTION PERTAMA
    private function loginVariable($user, $pass)
    {
        // $username = $this->input->post('username');
        // $password = md5($this->input->post('password'));
        $username = $user;
        $password = md5($pass);

        $query = $this->db->where('username', $username)
            ->where('password', $password)
            ->get('user');

        if ($query->num_rows() > 0) {
            $data = array(
                'username'    => $username,
                'logged_in'    => TRUE
            );
            // $this->session->set_userdata($data);

            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function test_doLogin()
    {
        $test = $this->loginVariable('philip', 'password');
        $expected_result = TRUE;
        $test_name = 'Testing do login';
        echo $this->unit->run($test,  $expected_result,  $test_name);
    }

    // FUNCTION KEDUA
    public function registerVariable($user, $email, $pass)
    {
        $data = array(
            'username'         => $user,
            'email'     => $email,
            'password'         => md5($pass),
        );
        $this->db->insert('user', $data);

        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function test_doRegister($orders, $quantity, $totalbayar)
    {
        $test = $this->registerVariable();
        $expected_result = TRUE;
        $test_name = 'Testing do register';
        echo $this->unit->run($test,  $expected_result,  $test_name);
    }

    // FUNCTION KETIGA
    public function placeorder_variable()
    {
        date_default_timezone_set('Asia/Jakarta');
        $timestamp = date('Y-m-d H:i:s');
        $data_order = array(
            'id_user'        => $this->input->post('id_cust'),
            'id_resto'        => $this->input->post('id_resto'),
            'total_bayar'    => NULL,
            'no_table'        => $this->input->post('table'),
            'created_at'    => $timestamp,
            'status'        => 'WAITING'
        );

        $this->db->insert('order_list', $data_order);

        $orders = $_POST['orders'];
        $quantity = $_POST['quantity'];
        $totalbayar = 0;

        foreach ($orders as $index => $orderlist) {
            $query = $this->db->select("MAX(id_order) AS idOrder")
                ->from('order_list')
                ->get();
            $order = $query->row()->idOrder;

            $menu = $this->db->select('id_menu')
                ->where('menu', $orders[$index])
                ->get('menu')
                ->result_array()[0]['id_menu'];

            $data_details = array(
                'id_order'    => $order,
                'id_menu'     => $menu,
                'quantity'    => $quantity[$index]
            );

            $this->db->insert('order_details', $data_details);

            $total = $this->db->select('*')
                ->where('id_order', $order)
                ->join('menu', 'menu.id_menu = order_details.id_menu')
                ->get('order_details')
                ->row();
            $temp = $total->harga * $total->quantity;
            $totalbayar = $totalbayar + $temp;
        }

        $data = array(
            'total_bayar' => $totalbayar,
        );

        $this->db->where('id_order', $order)
            ->update('order_list', $data);

        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function test_doRegister()
    {
        $test = $this->registerVariable('abi', 'abi@gmail.com', 'password');
        $expected_result = TRUE;
        $test_name = 'Testing do register';
        echo $this->unit->run($test,  $expected_result,  $test_name);
    }
}
