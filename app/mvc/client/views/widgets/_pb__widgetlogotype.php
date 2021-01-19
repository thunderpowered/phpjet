<?php
/**
 * @var string $logotype
 */
?>
<div class="p-3 header__logotype-wrapper">
    <div style="background-image: url('<?php echo $logotype; ?>')" class="header__logotype-image"></div>
    <a title="<?php echo \Jet\App\Engine\Config\Config::$config['site_name']; ?>" href="<?php echo \Jet\PHPJet::$app->router->getHost(); ?>" class="header__logotype-link"></a>
</div>