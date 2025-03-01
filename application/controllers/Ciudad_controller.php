<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Ciudad_controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Ciudad_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('upload');
        $this->load->library('session');
    }

    public function index() {
        $data['cities'] = $this->Ciudad_model->get_city_names();
        $this->load->view('ciudad_view', $data);
    }

    public function upload() {
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = 2048;
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('csv_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        } else {
            $file_data = $this->upload->data();
            $csv_data = array_map(function($line) {
                // Usar explode para separar por espacios/tabs
                return preg_split('/\s+/', trim($line)); 
            }, file($file_data['full_path']));

            $cities = [];
            foreach ($csv_data as $row) {
                if (count($row) >= 5) {
                    $cities[] = [
                        'id_externo' => $row[0],
                        'codigo_departamento' => $row[1],
                        'name' => mb_convert_encoding($row[2], 'UTF-8', 'auto'),
                        'main_code' => $row[3],
                        'additional_code' => $row[4]
                    ];
                }
            }

            if (!empty($cities)) {
                $this->Ciudad_model->insert_cities($cities);
                $this->session->set_flashdata('success', '¡Archivo procesado!');
            }
        }
        redirect('ciudad_controller');
    }

    public function get_city() {
        $name = $this->input->get('name', TRUE);
        $data = $this->Ciudad_model->get_city_details($name);
        echo json_encode($data);
    }
}