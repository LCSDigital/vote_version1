<?php 
	$tags = availablesTags();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
	<head>
		<meta charset=utf-8 />
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="viewport" content="width=device-width, initial-scale=1"/> 
 		<?php echo $meta; ?>
		<title>Le coq sportif - Plateforme de vote</title>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/reset.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/jquery.mmenu.all.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/commun.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/eshop.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/template_shop_home_page.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/template_responsive.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/bootstrap.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>/templates/lecoqsportif/css/pattern.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo APP_DIR; ?>css/common.css"/>
		<link type="text/css" rel="stylesheet" href="<?php echo APP_DIR; ?>css/app.css?v=<?php echo filemtime("css/app.css");?>"/>
		<script>
			var BASE_URL = '<?php echo BASE_URL; ?>';
			var APP_DIR = '<?php echo APP_DIR; ?>';
			var APP_URL = '<?php echo APP_URL; ?>';
			var STAGE = '<?php echo STAGE; ?>';
		</script>
	</head>
	<body class="<?php echo $tpl['page']; ?>">
	
		<!-- Google Tag Manager  lcs -->
		<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KB3CTG"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-KB3CTG');</script>
		<!-- End Google Tag Manager -->	
		
		<?php echo $tpl['header']; ?>
		<?php include('view/list/_header.php'); ?>


		<div class="navigation">
			
			<!-- champs recherche -->
			<div class="rechercher navigation-item ui-widget">
				<form id="form_to_search" >
						<input type="text" id="search" name="search" class="search ui"  autocomplete="off" value="<?php echo $request_data['search']; ?>" placeholder="Ville / Nom du club" />
						<input type="submit" class="tosearch tosearch-bouton-rechercher button-vote" value="Rechercher">
				</form>
			</div>
			<!-- fin recherche -->	
			<!--Pagination -->
			<?php echo $request_data['pagination']; ?>
			<!--Fin pagination -->

			<!--Tri -->
			<div class="order navigation-item article-navigation-item">
				<input id="orderby" type="hidden" value="<?php echo $request_data['orderby']; ?>" />
				<input id="order" type="hidden" value="<?php echo $request_data['order']; ?>" />
				Trier par 
				<select class="order-trigger orderby cursor-tri-pagination">
					<option value=",DESC" <?php if($request_data['orderby']=='' && $request_data['order']=='DESC') echo 'selected'; ?> >Ancienneté - au +</option>
					<option value=",ASC" <?php if($request_data['orderby']=='' && $request_data['order']=='ASC') echo 'selected'; ?> >Ancienneté + au -</option>
					<option value="nomClub,ASC" <?php if($request_data['orderby']=='nomClub' && $request_data['order']=='ASC') echo 'selected'; ?> >Nom du club A-Z</option>
					<option value="nomClub,DESC" <?php if($request_data['orderby']=='nomClub' && $request_data['order']=='DESC') echo 'selected'; ?> >Nom du club Z-A</option>
					<option value="nbVotes,DESC" <?php if($request_data['orderby']=='nbVotes' && $request_data['order']=='DESC') echo 'selected'; ?> >Nombre de votes + au -</option>
					<option value="nbVotes,ASC" <?php if($request_data['orderby']=='nbVotes' && $request_data['order']=='ASC') echo 'selected'; ?> >Nombre de votes - au +</option>
					<option value="ville,ASC" <?php if($request_data['orderby']=='ville' && $request_data['order']=='ASC') echo 'selected'; ?> >Ville A-Z</option>
					<option value="ville,DESC" <?php if($request_data['orderby']=='ville' && $request_data['order']=='DESC') echo 'selected'; ?> >Ville Z-A</option>
				</select>
			</div>
			<!--Fin Tri -->

		</div>
		<!--Fin En tête -->

		<!--Ensemble des maillots -->
		 <div class="items-container">
			<div class="items">
				<?php if($request_data['list'] == ''){
					echo '';
					 echo  '<div class="erreur_items_vide">
					 			<p>Désolé, ce maillot n\'existe pas</p>
					 			<div class="">
									<a class="lien-vers-maillots button-vote" href="soutien-rugby-amateur?search=">voir tous les maillots</a>
								</div>
					 		</div>';
					 	
				}
				 else
					 {echo $request_data['list'];}
				 ?>		
			</div>
		</div> 
		<div class="navigation">

			<!--Pagination -->
			<?php echo $request_data['pagination']; ?>
			<!--Fin pagination -->
		</div>	
		<?php echo $tpl['footer']; ?>
		<?php include('view/_popins.php'); ?>
		<?php include('view/_loader.php'); ?>

		<script src="<?php echo APP_DIR; ?>js/jquery.min.js" type="text/javascript"></script>
		<script src="<?php echo APP_DIR; ?>js/app.js?v=<?php echo filemtime("js/app.js");?>"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/jquery-ui-1.9.1.custom.min.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/lazysizes.min.js" type="text/javascript" async =""></script>
		<script src="<?php echo BASE_URL; ?>/scripts/jquery.mmenu.min.all.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/jquery.hoverIntent.minified.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/jquery.touchSwipe.min.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/App.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/App_eshop.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/jquery.selectbox-0.1.3.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/menu_responsive.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/qas.js"></script>
		<script src="<?php echo BASE_URL; ?>/scripts/pattern.js"></script>
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  		<script type="text/javascript">
  			$( function() {
				    var availableTags = <?php echo $tags; ?>;
				    $( "#search" ).autocomplete({
				      source: availableTags
				    });
				  } );
  		</script>
	</body>
</html>

    