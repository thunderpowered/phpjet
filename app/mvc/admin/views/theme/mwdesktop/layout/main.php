<!DOCTYPE html>
<!--[if lt IE 7]>
<html lang="ru" class="lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]>
<html lang="ru" class="lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]>
<html lang="ru" class="lt-ie9"><![endif]-->
<!--[if gt IE 8]><!-->
<html lang="ru">
<!--<![endif]-->
<head>
    <!-- META -->
    <meta charset="UTF-8">

    <!-- CSRF TOKEN (CO) -->
    <meta name="csrf_token" content="<?= \CloudStore\CloudStore::$app->system->token->generateToken() ?>">

    <meta name="theme-color" content="#000">
    <!-- Color theme for Chrome, Firefox and Opera -->
    <meta name="theme-color" content="#000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#000">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- END META -->

    <title><?php echo $this->controller->getTitle(); ?></title>

    <!-- WEBSITE STYLESHEETS -->
    <?php echo $this->includeCSS('css/theme.css'); ?>
    <?php echo $this->includeCSS('css/desktop.css'); ?>

    <!-- BOOTSTRAP/CSS -->
    <?php echo $this->includeCommonCSS('libs/bootstrap-5.0.0-beta1/css/bootstrap.min.css'); ?>

    <!-- BOOTSTRAP MSG -->
    <?php echo $this->includeCommonCSS('libs/bootstrap-msg-1.0.8/css/bootstrap-msg.css'); ?>

    <script>
        const globalSystemHost = "<?= \CloudStore\CloudStore::$app->router->getHost() ?>";
    </script>

    <!-- FONT AWESOME -->
    <script src="https://kit.fontawesome.com/ad0c58f10d.js" crossorigin="anonymous"></script>
</head>
<!-- BODY -->
<body>

<!-- MAIN CONTENT -->
<div class="p-0 w-100 vh-100 overflow-hidden theme__background-color theme__link-color" id="Desktop">
    <?php echo $this->view; ?>
</div>

<!-- JQUERY -->
<?php echo $this->includeCommonJS('libs/jquery-3.5.1/jquery-3.5.1.min.js') ?>

<!-- BOOTSTRAP/JS -->
<?php echo $this->includeCommonJS('libs/bootstrap-5.0.0-beta1/js/bootstrap.bundle.min.js'); ?>

<!-- BOOTSTRAP MSG -->
<?php echo $this->includeCommonJS('libs/bootstrap-msg-1.0.8/js/bootstrap-msg.js'); ?>

<!-- REACT APPLICATION -->
<?php echo $this->includeJS('react/desktop.js'); ?>

</body>
<!-- END BODY -->
</html>