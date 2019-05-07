<?php
	$itemsTxt = ($items > 1) ? $items." maillots" : $items." maillot";
	$pagination = '';
	$pagination .= '<div class="nb-items navigation-item">'.$itemsTxt.'</div>';
	$pagination .= '<div class="pagination navigation-item" data-itemsperpage="'. ITEMSPERPAGE .'" data-currentpage="'. $currentPage .'"  data-pages="'. $pages .'" data-items="'. $items .'">';
	$pagination .= '	<a href="javascript:;" class="pagination-btn btn-prev prevnext gotoprev"></a>';
	$pagination .= '	<span class="pagination-select-page">';
	$pagination .= '		Page';
	$pagination .= '		<select class="gotopage pagination-select-trigger cursor-tri-pagination">';

	for ($i=1 ; $i <= $pages ; $i++) {
		$current = ($currentPage==$i) ? 'selected=selected' : '';
		$pagination .= '		<option value="'.($i-1).'" '. $current . '>'.$i.'</option>';
	}

	$pagination .= '		</select>';
	$pagination .= '		/'.$pages;
	$pagination .= '	</span>';
	$pagination .= '	<a href="javascript:;" class="pagination-btn btn-next prevnext gotonext"></a>';
	$pagination .= '</div>';
?>