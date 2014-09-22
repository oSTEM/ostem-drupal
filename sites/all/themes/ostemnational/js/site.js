jQuery(document).ready(function ($) {

	$('#block-system-main-menu .menu li').each(function (i,e) {
		$(e).addClass('item-'+i);
	});

    $('#user-register-form #edit-subscription #edit-plan-id').change(function () {
        var $this = $(this);
        var form = $this.parents('form').first();
        var stripeFields = form.find('#edit-stripe');
        if ($this.val() == "_none") {
            stripeFields.hide();
            form.data('stripeNoToken', true);
        }
        else {
            stripeFields.show();
            form.data('stripeNoToken', false);
        }
    }).change();
});