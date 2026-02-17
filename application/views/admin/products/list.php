<!DOCTYPE html>
<html>
<head>
    <title>Products - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Products Management</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= site_url('product/add') ?>" class="btn btn-primary">Add Product</a>
            <a href="<?= site_url('admin/logout') ?>" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <?php if(empty($products)): ?>
        <div class="alert alert-info">No products found. <a href="<?= site_url('product/add') ?>">Add one now</a></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= $product['name'] ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td>
                            <a href="<?= site_url('product/edit/' . $product['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?= site_url('product/delete/' . $product['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
