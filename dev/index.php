<?php
		require_once('controller/_functions.php');
		set_data();
		$request_data = request_data();

		switch ($tpl['page']) {
			case 'template-article':
				include('view/_template_article.php');
				break;
			
			default: 
				include('view/_template_list.php');
				break;
		}

?>