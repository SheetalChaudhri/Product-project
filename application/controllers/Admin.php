<?php
class Admin extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library(['session','form_validation']);
        $this->load->helper(['url','form']);
        $this->load->model('Admin_model');
    }

    public function login(){
        if($this->input->post()){
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $admin = $this->Admin_model->get_by_email($email);

            if($admin && password_verify($password, $admin['password'])){
                $this->session->set_userdata([
                    'admin_id' => $admin['id'],
                    'admin_name' => $admin['name']
                ]);
                redirect('product');
            } else {
                $data['error'] = "Invalid Email or Password";
            }
        }
        $this->load->view('admin/login', isset($data)?$data:[]);
    }

    public function create_admin(){
        // Check if admin already exists
        $existing_admin = $this->Admin_model->count_admins();
        
        if($this->input->post()){
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT)
            ];
            
            if($this->Admin_model->create($data)){
                $this->session->set_flashdata('success', 'Admin account created. Please login.');
                redirect('admin/login');
            } else {
                $data['error'] = 'Failed to create admin account';
            }
        }
        
        // Only allow creation if no admins exist
        if($existing_admin == 0){
            $this->load->view('admin/create', isset($data)?$data:[]);
        } else {
            redirect('admin/login');
        }
    }

    public function logout(){
        $this->session->unset_userdata(['admin_id','admin_name']);
        redirect('admin/login');
    }
}
