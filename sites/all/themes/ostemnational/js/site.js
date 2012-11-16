jQuery(document).ready(function () {
	jQuery('#block-system-main-menu .menu li').each(function (i,e) {
		jQuery(e).addClass('item-'+i);
	});
});