<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Manajemen Stok Gudang</title>
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/sb-admin-2.min.css'); ?>" rel="stylesheet">

    <style>
        .login-card {
            border-radius: 10px;
            /* lebih kotak, nggak terlalu lonjong */
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
            border-radius: 8px;
            /* tombol agak kotak */
        }

        .form-control-user {
            border-radius: 8px;
            /* input agak kotak */
            padding: 0.9rem;
        }

        .login-title {
            font-weight: bold;
            color: #4e73df;
        }

        /* Responsive font size untuk judul */
        @media (max-width: 576px) {
            .login-title {
                font-size: 1rem;
            }

            .login-text {
                font-size: 0.85rem
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
                            <h1 class="h4 login-title mb-4">Manajemen Stok Gudang</h1>
                            <p class="login-text mb-4">Silakan login untuk melanjutkan</p>
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

    <script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/sb-admin-2.min.js'); ?>"></script>



    <script>
        // Auto hide alerts
        setTimeout(function () {
            $(".alert").fadeOut('slow');
        }, 2000);
    </script>
</body>

</html>