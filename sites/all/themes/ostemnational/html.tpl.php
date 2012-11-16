<?php if(!isset($_GET["ajax"]) && !isset($_POST["ajax"])) { ?>

<!DOCTYPE html>
<html class="no-js">
	<head>
	
		<title><?php print $head_title; ?></title>
		<?php print $head; ?>
		<?php print $styles; ?>
		<?php print $scripts; ?>
	
	</head>
	
	<body class="<?php print $classes; ?>" <?php print $attributes;?> id="<?php print getBodyID($is_front); ?>">
		
		<?php print $page_top; ?>
		<?php print $page; ?>
		<?php print $page_bottom; ?>
	
	</body>	
</html>

<?php } ?>