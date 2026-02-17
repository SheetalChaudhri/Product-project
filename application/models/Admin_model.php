<?php
class Admin_model extends CI_Model {
    
    public function get_by_email($email){
        return $this->db->get_where('admins',['email'=>$email])->row_array();
    }
    
    public function create($data){
        return $this->db->insert('admins', $data);
    }
    
    public function count_admins(){
        return $this->db->count_all('admins');
    }
}
