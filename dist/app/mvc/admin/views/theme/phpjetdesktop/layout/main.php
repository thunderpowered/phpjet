<?php
/**
 * @var View $this
 * @var WidgetMisc $this->widget->widgetMisc
 */

use Jet\App\Engine\Core\View;
use Jet\App\MVC\Admin\Widgets\WidgetMisc; ?>
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
    <?php echo $this->includeCSS('css/desktop.css'); ?>

    <!-- BOOTSTRAP/CSS -->
    <?php echo $this->includeCommonCSS('libs/bootstrap-5.0.0-beta1/css/bootstrap.min.css'); ?>

    <!-- BOOTSTRAP MSG -->
    <?php echo $this->includeCommonCSS('libs/bootstrap-msg-1.0.8/css/bootstrap-msg.css'); ?>

    <!-- DATATABLES -->
    <?php echo $this->includeCommonCSS('libs/datatables/datatables.min.css'); ?>

    <script type="text/javascript" class="globalVariables">
        let globalSystemActions = {}; // todo use store for this
        const globalSystemHost = "<?php echo \Jet\PHPJet::$app->router->getHost(); ?>";
        const globalSystemRootURL = "<?php echo \Jet\PHPJet::$app->router->getURL(); ?>";
        const globalDesktopMisc = JSON.parse('<?php echo json_encode([ // todo and for this too
            'logotype' => $this->widget->widgetMisc->getLogotype(),
            'engineVersion' => \Jet\PHPJet::$app->system->getEngineVersion()
        ])?>');
    </script>

    <!-- FONT AWESOME -->
    <script src="https://kit.fontawesome.com/ad0c58f10d.js" crossorigin="anonymous"></script>
</head>
<!-- BODY -->
<body>
<!-- MESSAGE FOR NON-JS USERS -->
<noscript>JavaScript is disabled. You have to enable it to load this app.</noscript>
<!-- ROOT ELEMENT -->
<div class="p-0 w-100 vh-100 overflow-hidden theme__background-color theme__link-color" id="Desktop"><?php echo $this->view; ?></div>

<!-- DATATABLES -->
<?php echo $this->includeCommonJS('libs/datatables/datatables.min.js'); ?>

<!-- BOOTSTRAP/JS -->
<?php echo $this->includeCommonJS('libs/bootstrap-5.0.0-beta1/js/bootstrap.bundle.min.js'); ?>

<!-- BOOTSTRAP MSG -->
<?php echo $this->includeCommonJS('libs/bootstrap-msg-1.0.8/js/bootstrap-msg.js'); ?>

<!-- NICESCROLL.JS -->
<?php echo $this->includeCommonJS('libs/jquery.nicescroll-3.7.6/jquery.nicescroll.min.js'); ?>

<!-- REACT APPLICATION -->
<?php echo $this->includeJS('js/desktop.js'); ?>

</body>
<!-- END BODY -->
</html>