<!DOCTYPE html>
<!--[if lt IE 7]><html lang="ru" class="lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html lang="ru" class="lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html lang="ru" class="lt-ie9"><![endif]-->
<!--[if gt IE 8]><!-->
<html lang="ru">
<!--<![endif]-->
<head>

    <!-- META -->
    <meta charset="UTF-8">

    <!-- CSRF TOKEN (CO) -->
    <meta name="csrf_token" content="<?= \CloudStore\CloudStore::$app->system->token->generateToken() ?>">

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

    <?= \CloudStore\CloudStore::$app->tool->SEO->getMetaTags($this->controller) ?>

    <title><?php echo $this->controller->getTitle(); ?></title>

    <link rel="shortcut icon" href="<?php echo $this->widget->widgetLogotype->getFavicon(); ?>" type="image/x-icon">

    <!-- STYLESHEETS -->
    <?php echo $this->includeCSS('css/theme.css'); ?>
    <?php echo $this->includeCSS('css/page.css'); ?>

    <!-- BOOTSTRAP -->
    <?php echo $this->includeCSS('libs/bootstrap-5.0.0-beta1/css/bootstrap.min.css'); ?>

    <!-- ANALYTICS -->
    <?php $this->widget->widgetSEO->getAnalytics(); ?>
    <!-- END ANALYTICS -->

    <style id="WidgetTheme" class="theme-adjust">
        <?php $this->widget->widgetTheme->getWidget(); ?>
    </style>
    <script>
        const globalSystemHost = "<?= \CloudStore\CloudStore::$app->router->getHost() ?>";
    </script>

    <!-- WIDGET STATIC FILES -->
    <?= $this->widget->getStyles(); ?>
    <?= $this->widget->getScripts(); ?>
    <!-- END WIDGET STYLES -->
</head>

<!-- BODY -->
<body>

<!-- MMENU PLUGIN -->
<div class="theme__background-color" id="root">

    <!-- HEADER -->
    <header>
        <?php echo $this->includePart('header'); ?>
    </header>

    <!-- MAIN CONTENT -->
    <?php echo $this->view; ?>
    <!-- END MAIN CONTENT -->

</div>

<!-- JAVASCRIPT -->
<?php echo $this->includeJS('libs/bootstrap-5.0.0-beta1/js/bootstrap.bundle.min.js'); ?>

<!-- REACT -->
<script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>

</body>
<!-- END BODY -->

</html>