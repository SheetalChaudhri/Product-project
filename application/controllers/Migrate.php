<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->library(['session']);
        $this->load->helper(['url']);
        $this->load->database();
    }

    // Run the SQL file at application/sql/setup.sql
    // Requires admin login (session 'admin_id')
    public function run(){
        if(!$this->session->userdata('admin_id')){
            show_error('Forbidden: login as admin to run migrations', 403);
            return;
        }

        $sqlFile = APPPATH . 'sql' . DIRECTORY_SEPARATOR . 'setup.sql';
        if(!file_exists($sqlFile)){
            show_error('setup.sql not found', 500);
            return;
        }

        $sql = file_get_contents($sqlFile);
        // Split SQL on semicolons so each statement is executed separately.
        // This avoids multi-statement execution errors for PREPARE/EXECUTE blocks.
        $parts = preg_split('/;\s*/', $sql);
        $errors = [];
        foreach($parts as $stmt){
            $stmt = trim($stmt);
            if(empty($stmt)) continue;
            // Skip lines that are only comments
            if(preg_match('/^\s*--/', $stmt)) continue;
            try{
                $this->db->query($stmt);
            } catch (Exception $e){
                $errors[] = $e->getMessage();
            }
        }

        echo '<h3>Migration run completed</h3>';
        if(empty($errors)){
            echo '<p>No errors.</p>';
        } else {
            echo '<p>Errors:</p><pre>' . htmlspecialchars(implode("\n", $errors)) . '</pre>';
        }
        echo '<p><a href="' . site_url('admin') . '">Back</a></p>';
    }
}
