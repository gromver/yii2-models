yii.gromMultipleField = (function ($) {
    function append(container) {
        $(container).find("> .grom-field-multiple-extra-fields > .grom-field-multiple-field.hidden:first").removeClass("hidden").addClass("show").find("[name]:input").attr("name", function (i, val) {
            return val.replace(/^__/, "");
        });
    }

    function normalize(container) {
        var $container = $(container);
        $container.find("> .grom-field-multiple-empty-text")[$container.find("> div > .grom-field-multiple-field:visible")[0] ? "hide" : "show"]();
        if (!$container.find("> .grom-field-multiple-extra-fields > .grom-field-multiple-field.hidden")[0]) $container.find("> .grom-field-multiple-append-btn").fadeOut(300);
    }

    return {
        init: function () {
            $(".grom-field-multiple-container").each(function () {
                var $container = $(this);
                $container.on("click", "> .grom-field-multiple-append-btn", function (e) {
                    append($container);
                    normalize($container);
                    e.preventDefault();
                });

                $container.on("click", "> div > .grom-field-multiple-field > .grom-field-multiple-close-btn", function (e) {
                    $(this).parent().hide(300, function () {
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