

$(document).ready(function () {

    if ($('#bundle-expert-container #widgets-json-container').html() !== '[]') {
        BundleExpertMainInit.init();
    }
    BundleExpertMainInit.init_cart_kit_edit_button();

    setInterval(BundleExpertMainInit.init_cart_kit_edit_button, 1000);

});

