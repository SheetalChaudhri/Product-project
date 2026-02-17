<?php
class Product extends CI_Controller {

   public function __construct(){
        parent::__construct();
        $this->load->model(['Product_model', 'Cart_model']);
        $this->load->helper(['url','form']);
        $this->load->library(['session','form_validation']);
    }

    public function index(){ // Admin: List products for management
        if(!$this->session->userdata('admin_id')) redirect('admin/login');
        $data = [];
        $data['current_page'] = 'products';
        $data['page_title'] = 'Products Management';
        $data['products'] = $this->Product_model->get_all();
        $data['content_view'] = 'product/list';
        
        $this->load->view('admin/layout', $data);
    }

    public function add(){ // Admin: Add product form
        if(!$this->session->userdata('admin_id')) redirect('admin/login');
        
        $this->form_validation->set_rules('name','Product Name','required');
        $this->form_validation->set_rules('price','Price','required|numeric');
        
        if($this->form_validation->run()){
            $this->store();
            return;
        }
        
        $data = [];
        $data['current_page'] = 'products';
        $data['page_title'] = 'Add Product';
        $data['content_view'] = 'product/form';
        
        $this->load->view('admin/layout', $data);
    }

    public function store(){ // Admin: Save new product
        if(!$this->session->userdata('admin_id')) redirect('admin/login');
        
        $product_data = [
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price')
        ];

        if($this->Product_model->create($product_data)){
            $this->session->set_flashdata('success', 'Product added successfully');
            redirect('product');
        } else {
            $this->session->set_flashdata('error', 'Failed to add product');
            redirect('product/add');
        }
    }

    public function edit($id){ // Admin: Edit product form
        if(!$this->session->userdata('admin_id')) redirect('admin/login');
        
        $product = $this->Product_model->get($id);
        
        if(!$product){
            $this->session->set_flashdata('error', 'Product not found');
            redirect('product');
        }
        
        $this->form_validation->set_rules('name','Product Name','required');
        $this->form_validation->set_rules('price','Price','required|numeric');
        
        if($this->form_validation->run()){
            $this->update($id);
            return;
        }
        
        $data = [];
        $data['current_page'] = 'products';
        $data['page_title'] = 'Edit Product';
        $data['product'] = $product;
        $data['content_view'] = 'product/form';
        
        $this->load->view('admin/layout', $data);
    }

    public function update($id){ // Admin: Update product
        if(!$this->session->userdata('admin_id')) redirect('admin/login');
        
        $product_data = [
            'name' => $this->input->post('name'),
            'price' => $this->input->post('price')
        ];

        if($this->Product_model->update($id, $product_data)){
            $this->session->set_flashdata('success', 'Product updated successfully');
            redirect('product');
        } else {
            $this->session->set_flashdata('error', 'Failed to update product');
            redirect('product/edit/' . $id);
        }
    }

    public function delete($id){ // Admin: Delete product
        if(!$this->session->userdata('admin_id')) redirect('admin/login');
        
        if($this->Product_model->delete($id)){
            $this->session->set_flashdata('success', 'Product deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete product');
        }
        redirect('product');
    }

    public function browse(){ // Customer: Browse products with cart
        $user_id = 1; // hardcoded
        $products = $this->Product_model->get_all_products();

        // Add cart info to products
        foreach($products as &$p){
            $cart_item = $this->Cart_model->get_cart_item($user_id, $p['id']);
            if($cart_item){
                $p['in_cart'] = true;
                $p['quantity'] = $cart_item->quantity;
            } else {
                $p['in_cart'] = false;
                $p['quantity'] = 1;
            }
        }
        $data['products'] = $products;
        $this->load->view('list_product', $data);
    }

    // GET: Product Listing (CMS) - Legacy method, redirects to browse
    public function list() {
        redirect('product/browse');
    }

    // POST API: Add product to cart
    public function add_to_cart_api() {
        $user_id = 1; // hardcoded
        $product_id = $this->input->post('product_id');
        $quantity = $this->input->post('quantity') ?? 1;

        if(!$product_id){
            echo json_encode(['status'=>false,'message'=>'Product ID required']);
            return;
        }

        $this->Cart_model->add_to_cart($user_id, $product_id, $quantity);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status'=>true,'message'=>'Cart updated']));
    }

    // POST API: Remove product from cart
    public function remove_from_cart_api() {
        $user_id = 1; 
        $product_id = $this->input->post('product_id');

        if(!$product_id){
            echo json_encode(['status'=>false,'message'=>'Product ID required']);
            return;
        }

        $this->Cart_model->remove_from_cart($user_id, $product_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status'=>true,'message'=>'Product removed from cart']));
    }

    // POST API: Update cart item quantity
    public function update_cart_api() {
        $user_id = 1;
        $product_id = $this->input->post('product_id');
        $quantity = $this->input->post('quantity');

        if(!$product_id){
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'Product ID required']));
            return;
        }

        $quantity = intval($quantity);
        if($quantity <= 0){
            // remove item
            $this->Cart_model->remove_from_cart($user_id, $product_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status'=>true,'message'=>'Product removed from cart']));
            return;
        }

        $this->Cart_model->add_to_cart($user_id, $product_id, $quantity);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status'=>true,'message'=>'Cart updated']));
    }

    // GET API: List cart items with totals
    public function cart_items_api() {
        $user_id = 1;
        $items = $this->Cart_model->get_cart_items($user_id);

        $total_amount = 0;
        $total_quantity = 0;
        $formatted = [];

        foreach($items as $it){
            $subtotal = floatval($it['price']) * intval($it['quantity']);
            $total_amount += $subtotal;
            $total_quantity += intval($it['quantity']);
            $formatted[] = [
                'cart_id' => $it['cart_id'],
                'product_id' => $it['product_id'],
                'name' => $it['name'],
                'price' => floatval($it['price']),
                'quantity' => intval($it['quantity']),
                'subtotal' => $subtotal
            ];
        }

        $response = [
            'status' => true,
            'items' => $formatted,
            'total_amount' => $total_amount,
            'total_quantity' => $total_quantity,
            'item_count' => count($formatted)
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // GET: CMS view of Cart Items
    public function cart_list() {
        $user_id = 1;
        $data['cart_items'] = $this->Cart_model->get_cart_items($user_id);
        $this->load->view('cart_list', $data);
    }
}
