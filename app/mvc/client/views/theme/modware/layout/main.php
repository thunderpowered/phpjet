<?php
/**
 * @var \Jet\App\Engine\Core\View $this
 */
?>
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
    <meta name="csrf_token" content="<?= \Jet\PHPJet::$app->system->token->generateToken() ?>">

    <!-- TODO don't forget to set it from widget-theme -->
    <meta name="theme-color" content="#000">
    <!-- Color theme for Chrome, Firefox and Opera -->
    <meta name="theme-color" content="#000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#000">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- END META -->

    <?= \Jet\PHPJet::$app->tool->SEO->getMetaTags($this->controller) ?>

    <title><?php echo $this->controller->getTitle(); ?></title>

    <link rel="shortcut icon" href="<?php echo $this->widget->widgetLogotype->getFavicon(); ?>" type="image/x-icon">

    <!-- WEBSITE STYLESHEETS -->
    <?php echo $this->includeCSS('css/theme.css'); ?>
    <?php echo $this->includeCSS('css/page.css'); ?>

    <!-- BOOTSTRAP/CSS -->
    <?php echo $this->includeCSS('libs/bootstrap-5.0.0-beta1/css/bootstrap.min.css'); ?>

    <!-- ANALYTICS -->
    <?php $this->widget->widgetSEO->getAnalytics(); ?>
    <!-- END ANALYTICS -->

    <style id="WidgetTheme" class="theme-adjust">
        <?php $this->widget->widgetTheme->getWidget(); ?>
    </style>
    <script>
        const globalSystemHost = "<?= \Jet\PHPJet::$app->router->getHost() ?>";
    </script>

    <!-- WIDGET STATIC FILES -->
    <?= $this->widget->getStyles(); ?>
    <?= $this->widget->getScripts(); ?>
    <!-- END WIDGET STYLES -->

    <!-- FONT AWESOME -->
    <script src="https://kit.fontawesome.com/ad0c58f10d.js" crossorigin="anonymous"></script>
</head>

<!-- BODY -->
<body>

<!-- ROOT DIV -->
<div class="theme__background-color" id="root">

    <!-- HEADER -->
    <header>
        <?php echo $this->includePart('header'); ?>
    </header>

    <!-- MAIN CONTENT -->
    <?php echo $this->view; ?>

</div>
<!-- END ROOT DIV -->

<!-- OLD GOOD JQUERY, ACTUALLY USELESS FOR THIS PROJECT -->
<!-- THE ONLY ONE PLUGIN USING JQUERY IS NICESCROLL -->
<!-- TODO: FIND WORKAROUND -->
<?php echo $this->includeJS('libs/jquery-3.5.1/jquery-3.5.1.min.js') ?>

<!-- BOOTSTRAP/JS -->
<?php echo $this->includeJS('libs/bootstrap-5.0.0-beta1/js/bootstrap.bundle.min.js'); ?>

<!-- NICESCROLL.JS -->
<?php echo $this->includeJS('libs/jquery.nicescroll-3.7.6/jquery.nicescroll.min.js'); ?>

<!-- REACT APPLICATION -->
<?php echo $this->includeJS('react/dist/js/app.js'); ?>

<!-- JQUERY PLUGIN ACTIVATION -->
<?php echo $this->includeJS('js/plugins.js', false, true); ?>

</body>
<!-- END BODY -->
</html>