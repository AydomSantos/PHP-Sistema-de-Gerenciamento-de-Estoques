<?php
require_once 'config/database.php';

session_start();

if(isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit();
}

$vErros = [];
$vSuccess = false;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $vUsername = trim($_POST['username']);
    $vEmail = trim($_POST['email']);
    $vPassword = $_POST['password'];
    $vConfirmPassword = $_POST['confirm_password'];
    $vFull_name = trim($_POST['full_name']);

    if(empty($vUsername)){
        $vErros[] = 'Username is required';
    } 

    if(empty($vEmail)){
        $vErros[] = 'Email is required'; 
    }elseif(!filter_var($vEmail, FILTER_VALIDATE_EMAIL)){
        $vErros[] = 'Invalid email format';
    }

    if(empty($vPassword)){
        $vErros[] = 'Password is required';
    } elseif(strlen($vPassword) < 6) {
        $vErros[] = 'Password must be at least 6 characters long';
    }
    
    if($vPassword !== $vConfirmPassword) {
        $vErros[] = 'Passwords do not match';
    }
    
    if(empty($vFull_name)){
        $vErros[] = 'Full name is required';
    }

    if(empty($vErros)){
        try{
            // Check if email already exists
            $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
            $stmt->bindParam(':email', $vEmail);
            $stmt->execute();
            
            if($stmt->fetchColumn() > 0){
                $vErros[] = 'Email already registered. Please use a different email address.';
            }
            
            // Check if username already exists
            $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
            $stmt->bindParam(':username', $vUsername);
            $stmt->execute();
            
            if($stmt->fetchColumn() > 0){
                $vErros[] = 'Username already taken. Please choose a different username.';
            }
        } catch(PDOException $e){
            $vErros[] = 'Database error: ' . $e->getMessage();
        }
    }

    if(empty($vErros)){
        try{
            // Verificar se é o primeiro usuário (será admin automaticamente)
            $stmt = $conn->query('SELECT COUNT(*) FROM users');
            $userCount = $stmt->fetchColumn();
            $isAdmin = ($userCount == 0) ? 1 : (isset($_POST['is_admin']) ? 1 : 0);
            
            $vHashedPassword = password_hash($vPassword, PASSWORD_DEFAULT);
            // Restore the is_admin field in the SQL statement
            // In the registration process, modify the SQL statement to include status
            $stmt = $conn->prepare('INSERT INTO users (username, email, password, full_name, is_admin, status) VALUES (:username, :email, :password, :full_name, :is_admin, :status)');
            $stmt->bindParam(':username', $vUsername);
            $stmt->bindParam(':email', $vEmail);
            $stmt->bindParam(':password', $vHashedPassword);
            $stmt->bindParam(':full_name', $vFull_name);
            $stmt->bindParam(':is_admin', $isAdmin, PDO::PARAM_INT);
            $status = 'pending';
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            $vSuccess = true;
        }catch(PDOException $e){
            $vErros[] = 'Database error: '. $e->getMessage(); 
        } 
    }  
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">User Registration</h3>
                    </div>
                    <div class="card-body">
                        <?php if($vSuccess): ?>
                            <div class="alert alert-success">
                                Registration successful! <a href="login.php">Login</a> to manage your products.
                            </div>
                        <?php else: ?>
                            <?php if(!empty($vErros)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach($vErros as $erro): ?>
                                            <li><?php echo $erro; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($vUsername) ? htmlspecialchars($vUsername) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($vEmail) ? htmlspecialchars($vEmail) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($vFull_name) ? htmlspecialchars($vFull_name) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <small class="text-muted">Password must be at least 6 characters long.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1">
                                        <label class="form-check-label" for="is_admin">Registrar como administrador</label>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                            </form>
                            <div class="mt-3 text-center">
                                Already have an account? <a href="login.php">Login</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


