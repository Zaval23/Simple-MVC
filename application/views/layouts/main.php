<?php 
use ItForFree\SimpleMVC\Config;


$User = Config::getObject('core.user.class');

?>
<!DOCTYPE html>
<html>
    <?php include('includes/main/head.php'); ?>
    <body> 
        <div id="container">
            <a href="<?= \ItForFree\SimpleMVC\Router\WebRouter::link("homepage/index") ?>">
                <img id="logo" src="/images/logo.jpg" alt="Widget News" />
            </a>
            <?= $CONTENT_DATA ?>
            <?php include('includes/main/footer.php'); ?>
        </div>
    </body>
</html>

