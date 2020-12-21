<!-- PAGE SCRIPTS -->
<!-- ASYNC LOADING (NOT REALLY)-->
<script>
    $(document).ready(function () {
        $(".loading").css("opacity", "1");
    });
</script>

<!-- TODO: what is it? -->
<script>
    var sliderArrows = {
        left: "\u003csvg aria-hidden=\"true\" focusable=\"false\" role=\"presentation\" viewBox=\"0 0 32 32\" class=\"icon icon-arrow-left\"\u003e\u003cpath fill=\"#444\" d=\"M24.333 28.205l-1.797 1.684L7.666 16l14.87-13.889 1.797 1.675L11.269 16z\"\/\u003e\u003c\/svg\u003e",
        right: "\u003csvg aria-hidden=\"true\" focusable=\"false\" role=\"presentation\" viewBox=\"0 0 32 32\" class=\"icon icon-arrow-right\"\u003e\u003cpath fill=\"#444\" d=\"M7.667 3.795l1.797-1.684L24.334 16 9.464 29.889l-1.797-1.675L20.731 16z\"\/\u003e\u003c\/svg\u003e",
        up: "\u003csvg aria-hidden=\"true\" focusable=\"false\" role=\"presentation\" viewBox=\"0 0 32 32\" class=\"icon icon-arrow-up\"\u003e\u003cpath fill=\"#444\" d=\"M26.984 23.5l1.516-1.617L16 8.5 3.5 21.883 5.008 23.5 16 11.742z\"\/\u003e\u003c\/svg\u003e",
        down: "\u003csvg aria-hidden=\"true\" focusable=\"false\" role=\"presentation\" viewBox=\"0 0 32 32\" class=\"icon icon-arrow-down\"\u003e\u003cpath fill=\"#444\" d=\"M26.984 8.5l1.516 1.617L16 23.5 3.5 10.117 5.008 8.5 16 20.258z\"\/\u003e\u003c\/svg\u003e"
    }
</script>

<!-- JQUERY UI -->
<?php $this->includeJS("js/jquery-ui.min.js"); ?>

<?php $this->includeJS("js/jquery.touchSwipe.min.js"); ?>

<!-- PAGE SCRIPTS -->
<!-- OLD AND DEPRECATED FILE -->
<!-- TODO: check it and delete -->
<?php $this->includeJS("js/basic.all.js"); ?>
<?php $this->widget->getScripts(); ?>

<!-- NEW THEME -->
<?php $this->includeJS("js/theme.libs.js"); ?>
<?php $this->includeJS("js/theme.min.js"); ?>

<!-- APPLICATION (REACT) -->
<?php $this->includeJS("js/app.js"); ?>

<!-- HEADERS -->
<?php $this->includeJS("js/headers.js"); ?>

<!-- /PAGE SCRIPTS -->