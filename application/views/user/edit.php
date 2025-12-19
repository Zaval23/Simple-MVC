<?php 
use ItForFree\SimpleMVC\Config;

$Url = Config::getObject('core.router.class');
$User = Config::getObject('core.user.class');
?>

<?php include('includes/admin-users-nav.php'); ?>


<h2><?= $editAdminusersTitle ?>
    <span>
        <?= $User->returnIfAllowed("admin/adminusers/delete", 
            "<a href=" . $Url::link("admin/adminusers/delete&id=" . $_GET['id']) 
            . ">[Удалить]</a>");?>
    </span>
</h2>

<form id="editUser" method="post" action="<?= $Url::link("admin/adminusers/edit&id=" . $_GET['id'])?>">
    <div class="form-group">
        <label for="login">Введите имя пользователя</label>
        <input type="text" class="form-control" name="login" id="login" placeholder="логин пользователя" value="<?= $viewAdminusers->login ?>">
    </div>
    <div class="form-group">
        <label for="pass">Введите пароль (оставьте пустым, чтобы не менять)</label>
        <input type="text" class="form-control" name="pass" id="pass" placeholder="новый пароль (оставьте пустым для сохранения текущего)" value="">
    </div>
    <div class="form-group">
        <label for="role">Права доступа</label>
        <select name="role" id="role" class="form-control">
            <option value="admin" <?= ($viewAdminusers->role === 'admin') ? 'selected' : '' ?>>Администратор</option>
            <option value="auth_user" <?= ($viewAdminusers->role === 'auth_user') ? 'selected' : '' ?>>Зарегистрированный пользователь</option>
        </select>
    </div>
    <div class="form-group">
        <label for="email">Введите e-mail</label>
        <input type="text" class="form-control" name="email" id="email" placeholder="email" value="<?= $viewAdminusers->email ?>">
    </div>
    
    <input type="hidden" name="id" value="<?= $_GET['id']; ?>">
    <input type="submit" class="btn btn-primary" name="saveChanges" value="Сохранить">
    <input type="submit" class="btn" name="cancel" value="Назад">
</form>
