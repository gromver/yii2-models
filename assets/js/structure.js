yii.multyfield = (function ($) {
    function append(container) {
        $(container).find("> .multyfield-extra-fields > .form-group.hidden:first").removeClass("hidden").addClass("show").find("[name]:input").attr("name", function (i, val) {
            return val.replace(/^__/, "");
        });
    }

    function normalize(container) {
        var $container = $(container);
        $container.find("> .multyfield-empty-text")[$container.find("> div > .form-group:visible")[0] ? "hide" : "show"]();
        if (!$container.find("> .multyfield-extra-fields > .form-group.hidden")[0]) $container.find("> .multyfield-append-btn").fadeOut(300);
    }

    return {
        init: function () {
            $(".multyfield-container").each(function () {
                var $container = $(this);
                $container.on("click", "> .multyfield-append-btn", function (e) {
                    append($container);
                    normalize($container);
                    e.preventDefault();
                });

                $container.on("click", "> div > .form-group .multyfield-close-btn", function (e) {
                    $(this).parents(".form-group:first").hide("slow", function () {
                        $(this).remove();
                        normalize($container);
                    });
                    e.preventDefault();
                });

                normalize($container);
            })
        }
    }
})(jQuery);