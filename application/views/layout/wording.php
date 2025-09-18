<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= $error; ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success; ?></div>
<?php endif; ?>