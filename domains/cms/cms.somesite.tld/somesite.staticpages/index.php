<?php

$views = array(
	'dashboard'	 	 => 'All Static Pages',
	'new_thing' 	 => 'Create a new Page',
);
echo subNav($section_id,$page_id,$views);

switch ($view) {

	case '':
	case 'index':
	case 'dashboard':
	include 'dashboard.php';
	break;
	
	default:
	if (is_numeric($view)) {
		$ModuleID = $view;
		include 'thing.php';
	} else {
		include $view . '.php';
	}
	break;
}

?>