<?php
class Cart_model extends CI_Model {

    // Add or update product in cart
    public function add_to_cart($user_id, $product_id, $quantity = 1) {
        $this->db->where(['user_id' => $user_id, 'product_id' => $product_id]);
        $exists = $this->db->get('cart')->row();

        if($exists) {
            $this->db->where('id', $exists->id);
            $this->db->update('cart', ['quantity' => $quantity]);
            return $exists->id;
        } else {
            $this->db->insert('cart', [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
            return $this->db->insert_id();
        }
    }

    // Get all cart items for a user
    public function get_cart_items($user_id) {
        $this->db->select('c.id as cart_id, p.id as product_id, p.name, p.price, c.quantity');
        $this->db->from('cart c');
        $this->db->join('products p', 'c.product_id = p.id');
        $this->db->where('c.user_id', $user_id);
        return $this->db->get()->result_array();
    }

    // Remove product from cart
    public function remove_from_cart($user_id, $product_id) {
        $this->db->where(['user_id' => $user_id, 'product_id' => $product_id]);
        return $this->db->delete('cart');
    }

    // Get single cart item
    public function get_cart_item($user_id, $product_id) {
        $this->db->where(['user_id'=>$user_id,'product_id'=>$product_id]);
        return $this->db->get('cart')->row();
    }
}
