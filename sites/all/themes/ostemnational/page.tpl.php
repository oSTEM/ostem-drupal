<?php if(!isset($_GET["ajax"]) && !isset($_POST["ajax"])) { ?>


<header>
	<?php print render($page['header']); ?>
</header>

<div id="menu_wrap">
	<?php print render($page['menubar']); ?>
</div>

<div id="main" role="main">
	
	<?php print render($page['help']); ?>
	
	<?php print render($page['content']); ?>
	
</div>

<footer>
	<?php print render($page['footer']); ?>
	<div id="sponsor_wrap">
		<?php print render($page['sponsors']); ?>
	</div>
</footer>

<?php
} else {
   print render($page['content']);
}
?>