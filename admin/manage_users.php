<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

$auth = new Auth($conn);
$auth->requireAdmin();

$message = '';
$error = '';

// Processar ações do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    
    if (isset($_POST['toggle_admin'])) {
        try {
            $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $current_status = $stmt->fetchColumn();
            
            $new_status = $current_status ? 0 : 1;
            $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            
            $message = "Permissões de administrador atualizadas com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao atualizar permissões: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_user'])) {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
            $stmt->execute([$user_id, $_SESSION['user_id']]);
            $message = "Usuário removido com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao remover usuário: " . $e->getMessage();
        }
    }
}

// Buscar todos os usuários
try {
    $stmt = $conn->prepare("SELECT id, username, email, full_name, is_admin, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao carregar usuários: " . $e->getMessage();
}

// Include header
include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h2>Gerenciamento de Usuários</h2>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (empty($users)): ?>
                <div class="alert alert-info">Nenhum usuário encontrado.</div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Email</th>
                            <th>Nome Completo</th>
                            <th>Admin</th>
                            <th>Data de Registro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge bg-success">Sim</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Não</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="toggle_admin" class="btn btn-primary btn-sm">
                                                <?php echo $user['is_admin'] ? 'Remover Admin' : 'Tornar Admin'; ?>
                                            </button>
                                        </form>
                                        <form method="post" class="d-inline ms-1" onsubmit="return confirm('Tem certeza que deseja remover este usuário?');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Remover</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="../index.php" class="btn btn-secondary">Voltar ao Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>