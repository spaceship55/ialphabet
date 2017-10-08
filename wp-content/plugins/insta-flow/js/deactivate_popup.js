(function ($) {
    var deactivated = false;
    var reason = "not_set";
    var additionalInfo = "";
    var btnVal = 3;

    function sendDataToServer(data) {
        var url = window["FFI_Deactivate"].admin_url
        $.post(url, data, function(data, status){
            console.log(data, status)
        })
    }

    function LA_Deactivator(prefix) {
        $(document).on("click", "[data-slug='insta-flow'] .deactivate a", function () {
            $(".ffi-opacity").show();
            $(".ffi-deactivate-popup").show();
            if ($(this).attr("data-uninstall") == "1") {
                btnVal = 2;
            }
            return false;
        });

        $(document).on("change", "[name='ffi_reasons']", function () {
            reason = $(this).val()
            var $wrap = $(".ffi-additional-details-wrap");
            var $popup = $(".ffi-deactivate-popup");

            $wrap.html("");
            $popup.removeClass("ffi-popup-active1 ffi-popup-active2 ffi-popup-active3");

            if (reason == "plugin_is_hard_to_use_technical_problems") {
                additionalInfo = '<div class="ffi-additional-active"><div><strong>Please describe your issue.</strong></div><br>' +
                    '<textarea name="' + prefix + '_additional_details" rows = "4"></textarea><br>' +
                    '<div>Our support will contact <input type="text" name="' + prefix + '_email" value="' + window["FFI_Deactivate"].email + '"> regarding this.</div>' +
                    '<br><div><button class="button button-primary ffi-submit-ticket" data-val="' + btnVal + '">Submit support ticket</button></div></div>';

                $wrap.append(additionalInfo);
                $popup.addClass("ffi-popup-active1");
            }
            else if (reason == "free_version_limited") {
                additionalInfo = '<div class="ffi-additional-active">' +
                    '<div><strong>We believe our premium version will fit your needs.</strong></div>' +
                    '<div><a href="' + window["FFI_Deactivate"].premium_url + '" target="_blank">Try with 100% money back guarantee.</a></div>';

                $wrap.append(additionalInfo);
                $popup.addClass("ffi-popup-active2");
            }
            else if (reason == "premium_expensive") {
                additionalInfo = '<div class="ffi-additional-active">' +
                    '<div><strong>We have a special offer for you.</strong></div>' +
                    '<div>Submit this form to get the offer to <input type="text" name="' + prefix + '_email" value="' + window["FFI_Deactivate"].email + '"></div>' +
                    '</div>';

                $wrap.append(additionalInfo);
                $popup.addClass("ffi-popup-active3");
            }

            $("#ffi-deactivate").hide();
            $("#ffi-submit-and-deactivate").show();
        });

        $(document).on("keyup", "[name=" + prefix + "_additional_details]", function () {
            if ($(this).val().trim() || jQuery("[name=" + prefix + "_reasons]:checked").length > 0) {
                $("#ffi-deactivate").hide();
                $("#ffi-submit-and-deactivate").show();
            }
            else {
                $("#ffi-deactivate").show();
                $("#ffi-submit-and-deactivate").hide();
            }
        });

        $(document).on("click", ".ffi-deactivate", function () {
            $(".ffi-deactivate-popup-opacity").show();

            var additional = $('[name="ffi_additional_details"]').val()
            var email = $('[name="ffi_email"]').val()
            var data = {
                action: window["FFI_Deactivate"].slug_down + '_deactivate',
                plugin: window["FFI_Deactivate"].slug_down,
                reason: reason,
                email: email ? email : window["FFI_Deactivate"].admin_email,
                version: window["FFI_Deactivate"].version,
                additional_info: additional ? additional : ''
            }

            sendDataToServer(data)

            if ($(this).hasClass("ffi-clicked") == false) {
                $(this).addClass("ffi-clicked");
                $("[name=ffi_submit_and_deactivate]").val(jQuery(this).attr("data-val"));
            }
        });

        $(document).on("click", ".ffi-submit-ticket", function () {
            var message = $('[name="ffi_additional_details"]').val()
            var email = $('[name="ffi_email"]').val()
            var data = {
                action: window["FFI_Deactivate"].slug_down + '_deactivate_ticket',
                plugin: window["FFI_Deactivate"].slug_down,
                reason: reason,
                email: email ? email : window["FFI_Deactivate"].admin_email,
                version: window["FFI_Deactivate"].version,
                message: message ? message : ''
            }

            sendDataToServer(data)

            if ($(this).hasClass("ffi-clicked") == false) {
                $(this).addClass("ffi-clicked");
                $(".ffi-submit-ticket").text('Ticket sent!');
                setTimeout(function () {
                    $(".ffi-submit-ticket").text('Submit support ticket');
                    $('[name="ffi_additional_details"]').val('')
                }, 5000)
            }
        });

        $(document).on("click", ".ffi-cancel, .ffi-opacity", function () {
            $(".ffi-opacity").hide();
            $(".ffi-deactivate-popup").hide();
            return false;
        });
    }

    $(document).ready(function () {
        LA_Deactivator('ffi')
    })
})(jQuery)