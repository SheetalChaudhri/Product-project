<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Add New Product</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= site_url('product') ?>" class="btn btn-secondary">Back to Products</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if(validation_errors()): ?>
                <div class="alert alert-warning"><?php echo validation_errors(); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('product/add') ?>" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?= set_value('name') ?>">
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price *</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required value="<?= set_value('price') ?>">
                </div>

                <!-- Description removed -->

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                    <a href="<?= site_url('product') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
