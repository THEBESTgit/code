<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ciudad_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Conexión explícita a la BD
    }

    // Inserta ciudades en lote
    public function insert_cities($data) {
        return $this->db->insert_batch('cities', $data);
    }

    // Obtiene nombres de ciudades para el select
    public function get_city_names() {
        return $this->db->select('name')
                      ->order_by('name', 'ASC')
                      ->get('cities')
                      ->result_array();
    }

    // Obtiene detalles completos de una ciudad
    public function get_city_details($name) {
        return $this->db->get_where('cities', ['name' => $name])
                      ->row_array();
    }
}