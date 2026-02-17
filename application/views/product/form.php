<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?php echo isset($product) ? 'Edit Product' : 'Add New Product'; ?></h5>
    </div>
    <div class="card-body">
        <?php if (validation_errors()): ?>
            <div class="alert alert-danger">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo isset($product) ? base_url('product/update/' . $product['id']) : base_url('product/add'); ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name *</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($product) ? htmlspecialchars($product['name']) : set_value('name'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Price *</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo isset($product) ? $product['price'] : set_value('price'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <?php if (isset($product) && !empty($product['image'])): ?>
                    <small class="form-text text-muted">Current image: <?php echo htmlspecialchars($product['image']); ?></small>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <?php echo isset($product) ? 'Update Product' : 'Add Product'; ?>
                </button>
                <a href="<?php echo base_url('product'); ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
