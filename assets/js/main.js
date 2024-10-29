function appIDChange(val) {

    jQuery('#aklamatorSingleWidgetID option:first-child').val('');
    jQuery('#aklamatorPageWidgetID option:first-child').val('');

}

function myFunction(widget_id) {

    var myWindow = window.open(akla_vars.site_url+'show/widget/'+widget_id);

    myWindow.focus();

}


jQuery(document).ready(function () {

    jQuery('#aklamator_pr_save').on('click', function (event) {
        var aklapraplicationID = jQuery('#aklamatorApplicationID');
        if (aklapraplicationID.val() == "")
        {
            alert("Paste your Aklamator Application ID");
            aklapraplicationID.focus();
            event.preventDefault();
        }
    });




    jQuery('#aklamatorApplicationID').on('input', function ()
    {
        jQuery('#aklamator_error').css('display', 'none');
    });

    jQuery('#aklamator_login_button').click(function () {
        var akla_login_window = window.open(signup_url,'_blank');
        var aklamator_interval = setInterval(function() {
            var aklamator_hash = akla_login_window.location.hash;
            var aklamator_api_id = "";
            if (akla_login_window.location.href.indexOf('aklamator_wordpress_api_id') !== -1) {

                aklamator_api_id = aklamator_hash.substring(28);
                jQuery("#aklamatorApplicationID").val(aklamator_api_id);
                akla_login_window.close();
                clearInterval(aklamator_interval);
                jQuery('#aklamator_error').css('display', 'none');
            }
        }, 1000);

    });
    
    jQuery("#aklamatorSingleWidgetID").change(function () {

        if (jQuery(this).val() == 'none') {
            jQuery('#preview_single').attr('disabled', true);
        } else {
            jQuery('#preview_single').removeAttr('disabled');
        }

        jQuery(this).find("option").each(function () {
//
            if (this.selected) {
                jQuery(this).attr('selected', true);

            } else {
                jQuery(this).removeAttr('selected');

            }
        });

    });


    jQuery("#aklamatorPageWidgetID").change(function () {

        if (jQuery(this).val() == 'none') {

            jQuery('#preview_page').attr('disabled', true);
        } else {
            jQuery('#preview_page').removeAttr('disabled');
        }

        jQuery(this).find("option").each(function () {
//
            if (this.selected) {
                jQuery(this).attr('selected', true);
            } else {
                jQuery(this).removeAttr('selected');

            }
        });

    });


    if (jQuery('table').hasClass('dynamicTable')) {
        jQuery('.dynamicTable').dataTable({
            "iDisplayLength": 10,
            "sPaginationType": "full_numbers",
            "bJQueryUI": false,
            "bAutoWidth": false

        });
    }
});