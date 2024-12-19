<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ffe259, #ffa751);
            font-family: Arial, sans-serif;
            perspective: 1000px;
        }

        .sign-in-up {
  background: -webkit-linear-gradient(left, #d4cd00, #A41312);
  margin-top: 1%;
  padding: 3%;

        }
        .background-3d {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, #ffe259, #ffa751);
            transform: rotateX(45deg) rotateY(45deg);
            transform-style: preserve-3d;
        }

        .navbar-brand span {
            white-space: normal;
            word-wrap: break-word;
            text-align: center;
            font-size: 1.3rem;
        }

        .footer {
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .navbar-brand img {
            height: 70px;
            width: auto;
        }

        .register-left img {
            width: 100px;
        }

        .btnRegister {
            background: #d4cd00;
            color: #fff;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .sign-in-up {
                margin-top: 3%;
                padding: 2%;
            }

            .register-left img {
                width: 80px;
            }

            .footer {
                position: static;
            }
        }

        @media (max-width: 576px) {
            .sign-in-up {
                margin-top: 2%;
                padding: 2%;
            }

            .register-left img {
                width: 60px;
            }
        }

        .header {
            padding: 0rem 2rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: #333;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #b63a29;
        }

        .btn-login {
            background-color: #b63a29;
            color: #fff;
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }

    </style>
</head>

<body>
    <div class="background-3d"></div>
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="library.png" alt="Library Logo" class="mr-2">
                    <span class="font-weight-bold text-dark">Gensantos Foundation College, Inc. Library</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="btn btn-login nav-link text-white" href="index.php">HOME</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="container sign-in-up">
        <div class="row">
            <div class="col-md-3 register-left ">
                <img src="icon-gfi-book.png" alt="" />
                <h3>Welcome</h3>
                <p>GFI's Online Library</p>
                <input type="button" id="toggleLogin" value="Register" /><br />
            </div>
            <div class="col-md-9 login-right">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form id="loginForm" class="student" method="POST" action="">
                            <h3 class="login-heading">Login Account</h3>
                            <div class="row login-form">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" name="email" class="form-control" placeholder="Your Email *" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Your Password *" required />
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePasswordVisibility()">
                                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group d-flex justify-content-between">
                                            <label class="form-check-label">
                                                <input type="checkbox" id="rememberMe" name="rememberMe" onclick="handleRememberMe()" />
                                                Remember Me
                                            </label>
                                            <div class="">
                                                <a id="forgot_password" href="#" role="tab" aria-controls="ForgotPassword" aria-selected="false">Forgot Password?</a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="submit" class="btnlogin" name="student_login" value="Login" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
    
</body>

</html>