<!-- META TAGS FOR GOOGLE SEARCH CONSOLE AND YANDEX.WEBMASTER -->
<?php if ($codes["yandex_webmaster"]): ?>
<meta name="yandex-verification" content="<?= $codes["yandex_webmaster"]; ?>" />
<?php endif; ?>

<?php if ($codes["google_searchconsole"]): ?>
<meta name="google-site-verification" content="<?= $codes["google_searchconsole"]; ?>" />
<?php endif; ?>

<!-- SCRIPTS FOR YANDEX.METRIKA AND GOOGLE ANALYTICS -->
<?php if ($codes["google_analytics"]): ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $codes["google_analytics"]; ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?= $codes["google_analytics"]; ?>');
</script>
<?php endif; ?>

<?php if ($codes["yandex_metrika"]): ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter<?= $codes["yandex_metrika"]; ?> = new Ya.Metrika2({
                    id:<?= $codes["yandex_metrika"]; ?>,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/tag.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks2");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/<?= $codes["yandex_metrika"]; ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?php endif; ?>