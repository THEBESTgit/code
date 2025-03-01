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
        $config = [
            'upload_path' => './uploads/',
            'allowed_types' => 'csv',
            'max_size' => 2048,
            'file_ext_tolower' => TRUE
        ];
        
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('csv_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        } else {
            $file_data = $this->upload->data();
            $csv_content = file_get_contents($file_data['full_path']);
            $csv_content = mb_convert_encoding($csv_content, 'UTF-8', 'auto');
            $csv_data = array_map('str_getcsv', explode("\n", $csv_content));

            $cities = [];
            foreach ($csv_data as $row) {
                if (count($row) >= 5 && !empty($row[0])) {
                    $cities[] = [
                        'id_externo' => trim($row[0]),
                        'codigo_departamento' => trim($row[1]),
                        'name' => trim($row[2]),
                        'main_code' => trim($row[3]),
                        'additional_code' => trim($row[4])
                    ];
                }
            }

            if (!empty($cities)) {
                try {
                    $this->Ciudad_model->insert_cities($cities);
                    $this->session->set_flashdata('success', '¡Archivo subido y procesado!');
                } catch (Exception $e) {
                    $this->session->set_flashdata('error', 'Error en base de datos: ' . $e->getMessage());
                }
            }
        }
        redirect('ciudad_controller');
    }

    public function get_city() {
        // Limpiar buffer de salida
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $name = $this->input->get('name', TRUE);
            $data = $this->Ciudad_model->get_city_details($name);
            
            if ($data) {
                die(json_encode([
                    'status' => 'success',
                    'data' => [
                        'departamento' => $data['codigo_departamento'],
                        'nombre' => $data['name'],
                        'codigo_principal' => $data['main_code'],
                        'codigo_adicional' => $data['additional_code']
                    ]
                ]));
            } else {
                die(json_encode(['status' => 'error', 'message' => 'Ciudad no encontrada']));
            }
        } catch (Exception $e) {
            die(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
}