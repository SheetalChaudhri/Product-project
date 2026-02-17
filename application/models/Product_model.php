<?php
class Product_model extends CI_Model {

    public function get_all(){ 
        return $this->db->get('products')->result_array(); 
    }
    
    public function get($id){ 
        return $this->db->get_where('products',['id'=>$id])->row_array(); 
    }
    
    public function create($data){ 
        return $this->db->insert('products', $data);
    }
    
    public function add_product($data){ 
        $this->db->insert('products',$data); 
    }
    
    public function update($id, $data){ 
        $this->db->where('id',$id)->update('products',$data);
        return $this->db->affected_rows() > 0;
    }
    
    public function delete($id){ 
        $this->db->where('id',$id)->delete('products');
        return $this->db->affected_rows() > 0;
    }
    
    public function count_all(){
        return $this->db->count_all('products');
    }

    public function insert_product($data, $images) {
        $this->db->insert('products', $data);
        $product_id = $this->db->insert_id();

        foreach ($images as $img) {
            $this->db->insert('product_images', [
                'product_id' => $product_id,
                'image_path' => $img
            ]);
        }

        return $product_id;
    }

    public function get_all_products() {
        $this->db->select('p.id, p.name, p.price, pi.image_path');
        $this->db->from('products p');
        $this->db->join('product_images pi', 'p.id = pi.product_id', 'left');
        $query = $this->db->get();
        $result = $query->result_array();

        $products = [];
        foreach ($result as $row) {
            $pid = $row['id'];
            if (!isset($products[$pid])) {
                $products[$pid] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'images' => []
                ];
            }
            if($row['image_path']) {
                $products[$pid]['images'][] = $row['image_path'];
            }
        }

        return array_values($products);
    }
}
