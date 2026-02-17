<!DOCTYPE html>
<html>
<head>
    <title>Product Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Product Listing</h2>
    <a href="<?= base_url('cart/list') ?>" class="btn btn-primary mb-3">View Cart</a>

    <div class="row">
        <?php foreach($products as $p): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <?php if(!empty($p['images'])): ?>
                    <img src="<?= base_url($p['images'][0]) ?>" class="card-img-top" style="height:200px;object-fit:cover;">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?= $p['name'] ?></h5>
                    <p class="card-text">₹<?= $p['price'] ?></p>

                    <?php if($p['in_cart']): ?>
                    <div class="d-flex align-items-center mb-2">
                        <button class="btn btn-outline-secondary btn-sm decrease me-2" data-id="<?= $p['id'] ?>">-</button>
                        <input type="text" class="form-control text-center quantity" value="<?= $p['quantity'] ?>" data-id="<?= $p['id'] ?>" style="width:60px;">
                        <button class="btn btn-outline-secondary btn-sm increase ms-2" data-id="<?= $p['id'] ?>">+</button>
                        <button class="btn btn-danger btn-sm ms-2 remove" data-id="<?= $p['id'] ?>">Remove</button>
                    </div>
                    <?php else: ?>
                    <button class="btn btn-primary add-to-cart" data-id="<?= $p['id'] ?>">Add to Cart</button>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
$(document).ready(function(){
    function updateCart(id, qty, cb){
        $.post('<?= base_url("cart/add") ?>', {product_id:id, quantity:qty}, function(res){
            let data = (typeof res === 'string')? JSON.parse(res) : res;
            if(data.status){ if(cb) cb(null, data); } 
            else { if(cb) cb(data.message); else alert(data.message); }
        }).fail(function(){ if(cb) cb('Request failed'); else alert('Request failed'); });
    }

    function removeFromCart(id, cb){
        $.post('<?= base_url("cart/remove") ?>', {product_id:id}, function(res){
            let data = (typeof res === 'string')? JSON.parse(res) : res;
            if(data.status){ if(cb) cb(null, data); } else { if(cb) cb(data.message); else alert(data.message); }
        }).fail(function(){ if(cb) cb('Request failed'); else alert('Request failed'); });
    }

    // Delegated handlers so dynamic UI updates keep working
    $(document).on('click', '.add-to-cart', function(){
        let id = $(this).data('id');
        let btn = $(this);
        updateCart(id, 1, function(err){
            if(err) return alert(err);
            // replace button with in-cart controls
            let controls = `\n                <div class="d-flex align-items-center mb-2">\n                    <button class="btn btn-outline-secondary btn-sm decrease me-2" data-id="${id}">-</button>\n                    <input type="text" class="form-control text-center quantity" value="1" data-id="${id}" style="width:60px;">\n                    <button class="btn btn-outline-secondary btn-sm increase ms-2" data-id="${id}">+</button>\n                    <button class="btn btn-danger btn-sm ms-2 remove" data-id="${id}">Remove</button>\n                </div>`;
            btn.replaceWith(controls);
        });
    });

    $(document).on('click', '.increase', function(){
        let id = $(this).data('id');
        let input = $('.quantity[data-id="'+id+'"]');
        let newVal = parseInt(input.val())+1;
        input.val(newVal);
        updateCart(id, newVal);
    });

    $(document).on('click', '.decrease', function(){
        let id = $(this).data('id');
        let input = $('.quantity[data-id="'+id+'"]');
        let val = parseInt(input.val());
        if(val>1){ input.val(val-1); updateCart(id, input.val()); }
    });

    $(document).on('click', '.remove', function(){
        let id = $(this).data('id');
        let container = $(this).closest('.card-body');
        removeFromCart(id, function(err){
            if(err) return alert(err);
            // show Add to Cart button again
            let addBtn = `<button class="btn btn-primary add-to-cart" data-id="${id}">Add to Cart</button>`;
            container.find('.d-flex').remove();
            container.append(addBtn);
        });
    });
});
</script>
</body>
</html>
