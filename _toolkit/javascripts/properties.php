<?php
	require_once('../toolkit.php');
	
	if (isset($_POST['_tk_cmp'])) {
		$component = str_replace('_tk_cmp_', '', trim($_POST['_tk_cmp']));
?>
{
	component: "<?php echo $component; ?>",
	fields: <?php echo $cmp->{$component}->field_attributes(); ?>
}
<?php
	} else {
?>
{}
<?php
	}
?>