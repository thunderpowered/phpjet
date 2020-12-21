$(document).ready(function () {
    if ($.cookie("mateil-jquery-message") !== "viewed") {

        $(".message__background").css("display", "block");
        $(".message__background").css("opacity", "1");
        $(".message__body").css("display", "block");
        $(".message__body").css("opacity", "1");

    }


    $(".js-message__close").click(function () {

        $.cookie("mateil-jquery-message", "viewed", {
            expires: 30
        });

        $(".message__background").css("display", "none");
        $(".message__background").css("opacity", "0");
        $(".message__body").css("display", "none");
        $(".message__body").css("opacity", "0");

    });
});