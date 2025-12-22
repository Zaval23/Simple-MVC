<?php 
use ItForFree\SimpleMVC\Config;


$User = Config::getObject('core.user.class');

?>
<!DOCTYPE html>
<html>
    <?php include('includes/admin-main/head.php'); ?>
    <body> 
        <?php include('includes/admin-main/nav.php'); ?>
        <div class="container">
            <?= $CONTENT_DATA ?>
        </div>
    </body>
</html>

