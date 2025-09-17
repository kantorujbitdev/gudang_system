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

    <style>
        .login-card {
            border-radius: 20px;
            overflow: hidden;
        }

        .login-logo {
            width: 80px;
            margin-bottom: 15px;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        .btn-primary {
            border-radius: 50px;
        }

        .form-control-user {
            border-radius: 50px;
            padding: 1rem;
        }

        .login-title {
            font-weight: bold;
            color: #4e73df;
        }

        /* Responsive font size untuk judul */
        @media (max-width: 576px) {
            .login-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-8 col-md-9">

                <div class="card shadow-lg my-5 login-card">
                    <div class="card-body p-5">
                        <div class="text-center">
                            <!-- Logo -->
                            <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Logo" class="login-logo">
                            <h1 class="h4 login-title mb-4">Sistem Manajemen Stok Gudang</h1>
                            <p class="text-muted mb-4">Silakan login untuk melanjutkan</p>
                        </div>

                        <!-- Error Message -->
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Form -->
                        <?php echo form_open('auth/login'); ?>
                        <div class="form-group">
                            <label for="username" class="small text-muted">Username</label>
                            <?php echo form_input('username', '', 'id="username" class="form-control form-control-user" placeholder="Masukkan username" required'); ?>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password" class="small text-muted">Password</label>
                            <?php echo form_password('password', '', 'id="password" class="form-control form-control-user" placeholder="Masukkan password" required'); ?>
                        </div>
                        <?php echo form_submit('submit', 'Login', 'class="btn btn-primary btn-user btn-block"'); ?>
                        <?php echo form_close(); ?>
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