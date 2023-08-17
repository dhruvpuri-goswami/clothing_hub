<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login | Clothing Hub</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-info">
                    <div class="card-header text-center text-white" style="background-color: #00494d;">
                        <h3><i class="fas fa-tshirt"></i> Clothing Hub</h3>
                        <p>Enter the world of fashion</p>
                    </div>
                    <div class="card-body">
                        <form action="backend_login.php" method="post">
                            <div class="form-group">
                                <label for="username"><i class="fas fa-user"></i> Username:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="fas fa-lock"></i> Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-block text-white"
                                style="background-color: #00494d;">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        New to Clothing Hub? <a href="register.php" class="text-info" style="color: #00494d;">Register
                            now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>

</html>