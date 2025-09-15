<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Sistem Manajemen Stok Gudang</title>
    <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/sb-admin-2.min.css'); ?>" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang!</h1>
                                    </div>
                                    <?php if ($this->session->flashdata('error')): ?>
                                        <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php echo form_open('auth/login'); ?>
                                    <div class="form-group">
                                        <?php echo form_input('username', '', 'class="form-control form-control-user" placeholder="Username" required'); ?>
                                    </div>
                                    <div class="form-group">
                                        <?php echo form_password('password', '', 'class="form-control form-control-user" placeholder="Password" required'); ?>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>
                                    <?php echo form_submit('submit', 'Login', 'class="btn btn-primary btn-user btn-block"'); ?>
                                    <?php echo form_close(); ?>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Lupa Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/sb-admin-2.min.js'); ?>"></script>
</body>

</html>