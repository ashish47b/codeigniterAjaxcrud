<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Load database library
        $this->load->database();
    }

    // Method to get all items
    public function getItems() {
        $query = $this->db->get('items');
        return $query->result_array();
    }

    // Method to create a new item
    public function createItem($data) {
        return $this->db->insert('items', $data);
    }

    // Method to update an existing item
    public function updateItem($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('items', $data);
    }

    // Method to delete an item
    public function deleteItem($id) {
        $this->db->where('id', $id);
        return $this->db->delete('items');
    }
    public function singleItem($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('items');
        return $query->result_array();
    }
}