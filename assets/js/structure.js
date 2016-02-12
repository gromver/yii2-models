yii.gromMultipleField = (function ($) {
    function append(container) {
        $(container).find("> .grom-field-multiple-field_extra:first").removeClass("grom-field-multiple-field_extra").find("[name]:input").attr("name", function (i, val) {
            return val.replace(/^__/, "");
        });
    }

    function normalize(container) {
        var $container = $(container);
        $container.find("> .grom-field-multiple-empty-text")[$container.find("> .grom-field-multiple-field:not(.grom-field-multiple-field_extra)")[0] ? "hide" : "show"]();
        if (!$container.find("> .grom-field-multiple-field_extra")[0]) $container.find("> .grom-field-multiple-append-btn").fadeOut(300);
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

                $container.on("click", "> .grom-field-multiple-field > .grom-field-multiple-close-btn", function (e) {
                    $(this).parent().hide(300, function () {
                        $(this).remove();
                        normalize($container);
                    });
                    e.preventDefault();
                });

                $container.on("click", "> .grom-field-multiple-field > .grom-field-multiple-up-btn", function (e) {
                    var row = $(this).parent();
                    row.insertBefore(row.prev());

                    e.preventDefault();
                });

                $container.on("click", "> .grom-field-multiple-field > .grom-field-multiple-down-btn", function (e) {
                    var row = $(this).parent();
                    row.insertAfter(row.next(':visible'));

                    e.preventDefault();
                });

                normalize($container);
            })
        }
    }
})(jQuery);