<!DOCTYPE html>
<html>
<head>
    <title>Cart Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>User Cart</h2>
    <a href="<?= base_url('product/list') ?>" class="btn btn-primary mb-3">Back to Products</a>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($cart_items)): ?>
            <?php foreach($cart_items as $item): ?>
                <tr data-id="<?= $item['product_id'] ?>">
                    <td><?= $item['cart_id'] ?></td>
                    <td><?= $item['name'] ?></td>
                    <td>₹<?= $item['price'] ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary btn-sm decrease me-2">-</button>
                            <input type="text" class="form-control text-center quantity" value="<?= $item['quantity'] ?>" style="width:60px;">
                            <button class="btn btn-outline-secondary btn-sm increase ms-2">+</button>
                        </div>
                    </td>
                    <td class="total">₹<?= $item['price'] * $item['quantity'] ?></td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm remove">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No items in cart</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function(){
    function updateCart(row, qty){
        let product_id = row.data('id');
        $.post('<?= base_url("cart/add") ?>',{product_id:product_id,quantity:qty}, function(res){
            let data = JSON.parse(res);
            if(data.status){
                let price = parseFloat(row.find('td:nth-child(3)').text().replace('₹',''));
                row.find('.total').text('₹'+(price*qty));
            }
        });
    }

    $('.increase').click(function(){
        let row = $(this).closest('tr');
        let input = row.find('.quantity');
        input.val(parseInt(input.val())+1);
        updateCart(row, input.val());
    });

    $('.decrease').click(function(){
        let row = $(this).closest('tr');
        let input = row.find('.quantity');
        let val = parseInt(input.val());
        if(val>1){ input.val(val-1); updateCart(row, input.val()); }
    });

    $('.quantity').change(function(){
        let row = $(this).closest('tr');
        let val = parseInt($(this).val());
        if(val<1) $(this).val(1);
        updateCart(row, $(this).val());
    });

    $('.remove').click(function(){
        let row = $(this).closest('tr');
        let product_id = row.data('id');
        $.post('<?= base_url("cart/remove") ?>',{product_id:product_id}, function(res){
            let data = JSON.parse(res);
            if(data.status) row.remove();
        });
    });
});
</script>
</body>
</html>
