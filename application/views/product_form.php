<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>
    <h2>Add Product</h2>
    <form action="<?= base_url('product/create') ?>" method="post" enctype="multipart/form-data">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Price:</label><br>
        <input type="number" name="price" step="0.01" required><br><br>

        <label>Images:</label><br>
        <input type="file" name="images[]" multiple><br><br>

        <button type="submit">Add Product</button>
    </form>

    <hr>
    <h2>All Products</h2>
    <div id="products"></div>

    <script>
        async function loadProducts() {
            const res = await fetch('<?= base_url('product/get_all') ?>');
            const data = await res.json();
            let html = '';
            data.forEach(product => {
                html += `<h3>${product.name} - ₹${product.price}</h3>`;
                product.images.forEach(img => {
                    html += `<img src="<?= base_url() ?>${img}" width="100"> `;
                });
                html += '<hr>';
            });
            document.getElementById('products').innerHTML = html;
        }
        loadProducts();
    </script>
</body>
</html>
