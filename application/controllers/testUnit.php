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
    private function registerVariable($user, $email, $pass)
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

    public function test_doRegister()
    {
        $test = $this->registerVariable('irfan', 'irfan@gmail.com', 'password');
        $expected_result = TRUE;
        $test_name = 'Testing do register';
        echo $this->unit->run($test,  $expected_result,  $test_name);
    }

    // FUNCTION KETIGA
    private function placeOrdersVariable($id_cust, $id_resto, $table, $orderVarBaru, $orderQtyBaru)
    {
        date_default_timezone_set('Asia/Jakarta');
        $timestamp = date('Y-m-d H:i:s');
        $data_order = array(
            'id_user'        => $id_cust,
            'id_resto'        => $id_resto,
            'total_bayar'    => NULL,
            'no_table'        => $table,
            'created_at'    => $timestamp,
            'status'        => 'WAITING'
        );

        $this->db->insert('order_list', $data_order);

        $orders = $orderVarBaru;
        $quantity = $orderQtyBaru;
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

    public function test_placeOrders()
    {
        $test = $this->placeOrdersVariable('7', '1', '3', ['Katsu Hot Ramen', 'Lemon Tea', 'Crispy Snacks'], [1, 1, 1]);
        $expected_result = TRUE;
        $test_name = 'Testing do Booking';
        echo $this->unit->run($test,  $expected_result,  $test_name);
    }
}
