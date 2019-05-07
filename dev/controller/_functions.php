<?php
	session_start();

	error_reporting(E_ALL & ~E_NOTICE);

	/*  CONF ******************************************************************************************/

	$tpl = [];
	$tpl['page'] = 'template-list';
	
	if($_SERVER['SERVER_NAME']!='localhost') {

		/* Protocole */
		$http = (strpos($_SERVER['SERVER_NAME'],'dev2')!==false) ? 'http://' : 'https://'; 
		define(HTTP, $http);

		/* PROD */
		
		// Défini le dossier contenant l'app
		define(APP_DIR, '/vote/');
		define(BASE_URL, HTTP . $_SERVER['HTTP_HOST'] .'/fr-fr/');
		define(APP_URL, BASE_URL . 'soutien-rugby-amateur');
		
		
		// Header et footer
		$tpl['header'] = file_get_contents(BASE_URL.'index.php?controller=main&id_page=185');
		$tpl['footer'] = file_get_contents(BASE_URL.'index.php?controller=main&id_page=186');

	}
	else {
		
		/* DEV */
		
		define(APP_DIR, '');
		define(BASE_URL, 'https://www.lecoqsportif.com');
		define(APP_URL, '');
		$tpl['header'] = file_get_contents(BASE_URL.'/fr-fr/index.php?controller=main&id_page=185');
		$tpl['footer'] = file_get_contents(BASE_URL.'/fr-fr/index.php?controller=main&id_page=186');
	
	}

	// Tableau pour les images
	$img = array(
		"maillot" => "images/maillots/",
		"logo" => "images/logos/",
		"extenMaillot" => "_maillot.jpg",
		"extenLogo" => "_logo.png"
	);

	// Défini la phase marketing de vote
	$stage = array("élégant","authentique","créatif");	
	define('STAGE', $stage[0]);

	// Défini le nombre de maillot par page
	define('ITEMSPERPAGE', 32);

	// Initialise les Meta balises pour Opengrah
	$meta = '';




	// Footer

	/*  ACTIONS ******************************************************************************************/
	if(isset($_GET['a'])) {
		switch($_GET['a']) {
			
			case 'requestdata':
				request_data();
				break;

			case 'vote': 
				vote();
				break;

			case 'share':
				share();
				break;

			case 'article':
				set_meta_data();
				$tpl['page'] = 'template-article';
				break;

		}
	}

	/**********************************************************************************************************
	Ecris des données dans un fichier
	**********************************************************************************************************/
	function set_file_data($file, $data, $mode) {

		// Test si le fichier exite
		if (file_exists($file)) {
			
			if ($open = fopen($file, $mode)) {  

				// verrouille le fichier
				flock($open, 2);
				
				// écrit
				fwrite($open, $data);
				
				//on deverouille
				flock($open, 3);
				
				// ferme le fichier
				fclose($open);

			}
		
		}
		// Le fichier n'existe pas, il est créé à la volée
		else {
			
			$open= fopen($file, 'w');
			fwrite($open, $data);
			fclose($open);
		
		}   
	}


	/**********************************************************************************************************
	Récupère les maillots extrait du csv et formalise les données dans un fichier
	**********************************************************************************************************/
	function set_data() {
		
		global $img, $stage;
		
		/*  READ DATA.JSON */

		// récupération des données existantes pour comparaison avec le csv afin de ne mettre à jour que les nouveaux maillots
		$file = "data/data.json";
		$data = file_get_contents($file);
		$arr_items = json_decode($data, true);

		// compte le nombre de maillot dans le json
		$data_items_length =  count($arr_items);

		// créé un tableau vide si le fichier est vide
		if($data_items_length==0) $arr_items = [];


		/*  READ REPORT.CSV */

		// récuperation du fichier à partir du lien 
		$csv = file('https://ffr-subli.lablcs.com/report/status-3/report.csv', FILE_SKIP_EMPTY_LINES);

		// supprime la ligne de titre
		unset($csv[0]);

		// compte le nombre de maillots dans le csv
		$csv_items_length = count($csv);

		/*  UPDATE DATA.JSON FROM REPORT.CSV */

		// Test si il y a de nouveaux maillots
		if($csv_items_length > $data_items_length) {

			// Ajout des nouvelles données
			foreach($csv as $value) {
				// eclate la ligne pour récuperer les données requises
				$item = explode(';', $value);

				$newItems = 0;
				$maillotID = $item[0];
				$imageMaillot = $img['maillot'].$item[0].$img['extenMaillot'];
				$imageLogo = $img['logo'].$item[0].$img['extenLogo'];
				$intitule = str_replace('"', '', $item[2]);
				$split_intitule = explode(' - ', $intitule);
				$ville = strtolower($split_intitule[0]);
				$nomClub = (count($split_intitule)==2) ? strtolower($split_intitule[1]) : strtolower($split_intitule[0]);

				// Ajoute une entrée seulement si le maillot n'est pas existant dans la liste des données afin de ne pas réinitialiser les votes
				if(strpos($data, $maillotID)===false) {
					
					$newItems++;
					$arr_items[$maillotID] = array(
						"maillotID" => $maillotID,
						"imageMaillot" => $imageMaillot,
						"imageLogo" => $imageLogo,
						"intitule" => $intitule,
						"nomClub" => $nomClub,
						"ville" => $ville,
						"nbVotes" => array(
							$stage[0] => 0,
							$stage[1] => 0,
							$stage[2] => 0
						)
					);
				}
			}

			if($newItems>0) {
				$json = json_encode($arr_items, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
				set_file_data($file, $json, 'w');
			}
		}
	}


	/**********************************************************************************************************
		Récupère les maillots correspondants à une requête
		- filtre l'affichage en fonction de la page demandée
		- recherche sur mot clé
		- Tri les données
	**********************************************************************************************************/
	function request_data() {
		
		// Défini si c'est une requete ajax (type=='get')
		$type = (isset($_GET['type'])) ? htmlentities($_GET['type']) : '';
		$dir = ($type=='get') ? '../' : '';

		// Défini les index limites de recherche
		$start = (isset($_GET['start'])) ? htmlentities($_GET['start']) : 0;
		$end = (isset($_GET['end'])) ? htmlentities($_GET['end']) : ITEMSPERPAGE;

		// Défini le terme de la recherche
		if(isset($_GET['search'])) $search = htmlentities($_GET['search']);
		else if(isset($_POST['search'])) $search = htmlentities($_POST['search']);
		else $search = '';
		

		// Défini l'ordre d'affichage (nbVotes | nomClub | ville)
		$orderby = (isset($_GET['orderby']) && $_GET['orderby']!='') ? htmlentities($_GET['orderby']): '';
		if(isset($_GET['order']) && $_GET['order']!='') $order = htmlentities($_GET['order']);
		else {
			// Ordre par défaut en fonction du paramètre de tri
			switch($orderby) {
				case 'nbVotes': $order = 'DESC'; break;
				case 'nomClub': $order = 'ASC'; break;
				case 'ville': 	$order = 'ASC'; break;
				default:		$order = 'DESC';
			}

		}


		/*  READ DATA.JSON */
		$file = $dir."data/data.json";
		$data = file_get_contents($file);
		$arr_items = json_decode($data, true);
		$compteur = count($arr_items);


		/* CHECK BLACKLIST */
		$blacklist = file($dir.'data/blacklist.csv', FILE_SKIP_EMPTY_LINES);
		unset($blacklist[0]);

		foreach($blacklist as $value) {

			//echo $value."<br>";
			$ligne = explode(';', $value);
			$autorisation = trim($ligne[1]);
			$maillotID = trim($ligne[0]);

			if($autorisation == 'NON') {
				// Extrait du tableau les maillots pour lesquelles l'autorisation n'est pas accordée
				unset($arr_items[$maillotID]);
			}
		
		}


		/*  FILTER DATA TO MATCH SEARCH PARAMETER */
		if($search!='') {

			// Défini sur quel critère la recherche va se faire
			$subject = (preg_match('/(.+)-(.+)-(.{7})-(.{8})/', $search)) ? 'maillotID' : 'intitule';

			foreach ($arr_items as $key=>$item) {
				
				$maillotID = $item['maillotID'];
				$nomClub = $item[$subject];

				// Extrait du tableau toute entrée qui ne possède pas l'élément recherché dans le nom du club
				if(stripos($nomClub, $search)===false) unset($arr_items[$key]);
				
			}

		}

		/* ORDER DATA BY PARAMETER */
		if($orderby!='') {
			
			// Créé un tableau pour trier les éléments dans l'ordre défini
			$arr_orderby = [];
			foreach ($arr_items as $key=>$item) {
				
				$maillotID = $item['maillotID'];
				if(preg_match('/nomClub|ville|nbVotes/',$orderby)) {
					$arr_orderby[$maillotID] = ($orderby=='nbVotes') ? $item[$orderby][STAGE] : $item[$orderby];
				}
			
			}

			// Tri les éléments
			if($order=='DESC') arsort($arr_orderby);
			else asort($arr_orderby);

		}
		// Si aucun parametre de tri n'est défini, affichage par défaut des maillots les plus récents aux plus anciens
		else {
			if($order=='DESC') $arr_items = array_reverse($arr_items);
		}


		/*  SET DATA INTO VAR  */
		$list = '';
		$count = 0;

		// affiche les éléments en fonction du tri demandé
		if(preg_match('/nomClub|ville|nbVotes/',$orderby)) {
			foreach ($arr_orderby as $key => $value) {
				$maillotID = $key;
				$nomClub = $arr_items[$maillotID]['nomClub'];
				$ville = $arr_items[$maillotID]['ville'];
				$imageMaillot = $arr_items[$maillotID]['imageMaillot'];
				$imageLogo = $arr_items[$maillotID]['imageLogo'];
				$nbVotes = $arr_items[$maillotID]['nbVotes'][STAGE];
				
				$html = insert_requested_data($search,$start,$end,$count,$maillotID,$nomClub,$ville,$imageMaillot,$imageLogo,$nbVotes);
				$list .= $html;

				$count++;
			}
		}
		// affiche les éléments sans tri
		else {
			foreach ($arr_items as $key=>$item) {

				$maillotID = $item['maillotID'];
				$nomClub = $item['nomClub'];
				$ville = $item['ville'];
				$imageMaillot = $item['imageMaillot'];
				$imageLogo = $item['imageLogo'];
				$nbVotes = $item['nbVotes'][STAGE];

				$html = insert_requested_data($search,$start,$end,$count,$maillotID,$nomClub,$ville,$imageMaillot,$imageLogo,$nbVotes);
				$list .= $html;

				$count++;
				
			}
		}


		// Retourne le contenu de la liste comme réponse si requete ajax
		if($type=='get') echo $list;


		/*  SET PAGINATION */
		
		// Défini la page courante
		$currentPage = (isset($_GET['p']) && is_int(intval($_GET['p']))) ? $_GET['p'] : 1;
		
		// Défini le nombre de maillots
		$items =  count($arr_items);

		if($items > 0) {
				
			$pages = ceil($items/ITEMSPERPAGE);
			$next = $currentPage+1;
			$previous = $currentPage -1;
			$start = ($currentPage-1) *ITEMSPERPAGE;
			$end = $currentPage *ITEMSPERPAGE;


			$prevState = ($currentPage == 1) ? 'off' : 'on';
			$nextState = ($currentPage == $pages) ? 'off' : 'on';


			include($dir.'view/list/_pagination.php');

		}
		else {
			$pagination = '';
		}

		return array(
			'data' => $arr_items,
			'pagination' => $pagination,
			'list' => $list,
			'search' => $search,
			'orderby' => $orderby,
			'order' => $order,
		);
	}

	function insert_requested_data($search,$start,$end,$count,$maillotID,$nomClub,$ville,$imageMaillot,$imageLogo,$nbVotes) {

		global $img;

		$dir = (isset($_GET['type']) && $_GET['type']=='get') ? '../' : '';

		// Met en valeur le terme recherché en cas de recherche
		$nomClubTxt = ($search!='') ? str_replace($search, '<span style="background:yellow;">'.$search.'</span>', $nomClub) : $nomClub;
		$villeTxt = ($search!='') ? str_replace($search, '<span style="background:yellow;">'.$search.'</span>', $ville) : $ville;

		if($count>= $start && $count < $end) {

			if($_GET['a']=='article') $tpl['list'] = file_get_contents($dir.'view/article/_article.php');
			else $tpl['list'] = file_get_contents($dir.'view/list/_list.php');
			$tpl['list'] = str_replace('{{BACKLINK}}', BASE_URL.'soutien-rugby-amateur?search=', $tpl['list']);
			$tpl['list'] = str_replace('{{APPDIR}}', APP_DIR, $tpl['list']);
			$tpl['list'] = str_replace('{{INDEX}}', $count, $tpl['list']);
			$tpl['list'] = str_replace('{{MAILLOTID}}', $maillotID, $tpl['list']);
			$tpl['list'] = str_replace('{{NOMCLUB}}', $nomClubTxt, $tpl['list']);
			$tpl['list'] = str_replace('{{VILLE}}', $villeTxt, $tpl['list']);
			$tpl['list'] = str_replace('{{IMGMAILLOTFACE}}', APP_DIR . $imageMaillot, $tpl['list']);
			$tpl['list'] = str_replace('{{IMGMAILLOTDOS}}', str_replace($img['maillot'], $img['maillot'].'dos-', APP_DIR . $imageMaillot), $tpl['list']);
			$tpl['list'] = str_replace('{{IMGLOGO}}', APP_DIR . $imageLogo, $tpl['list']);
			$tpl['list'] = str_replace('{{NBVOTES}}', $nbVotes, $tpl['list']);
			
			if($_SERVER['SERVER_NAME']=='localhost') $tpl['list'] = str_replace('{{URL}}', '?a=article&search='. $maillotID, $tpl['list']);
			else $tpl['list'] = str_replace('{{URL}}', APP_URL . '-maillot-'. $maillotID, $tpl['list']);
		}

		return $tpl['list'];

	}

	/**********************************************************************************************************
		fonction de vote de maillot
		- Ajoute un bulletin de participation dans votes.json
		- Met à jour le nombre de vote d'un maillot dans data.json
	**********************************************************************************************************/
	function vote() {

		$id = hash('md5', $_POST['form_to_vote_email']);

		// Tableau des messages de validation
		$log = array(
			0 => "{\"code\": 0, \"msg\": \"Veuillez saisir votre email.\"}",
			1 => "{\"code\": 1, \"msg\": \"L'email n'est pas valide.\"}",
			2 => "{\"code\": 2, \"msg\": \"Vous avez déjà voté pour ce maillot.\"}",
			3 => "{\"code\": 3, \"msg\": \"Vous avez atteint la limite de votes.\"}",
			4 => "{\"code\": 4, \"msg\": \"Merci pour votre soutien.\",\"emailid\":\"". $id ."\"}"
		);

		$votesPerUser = 5;


		/*  FORM SUBMIT */

		if(!empty($_POST)) {

			$entries = (isset($_SESSION['entries'])) ? $_SESSION['entries'] : '0';

			$email =  htmlspecialchars($_POST['form_to_vote_email']);
			$maillotID =  htmlspecialchars($_POST['form_to_vote_maillotID']);

			if(!filter_var( $email, FILTER_VALIDATE_EMAIL)){
				echo $log[1];
				exit();
			}

			// Récupère les données du vote
			$newVote = array(
				"id"=>$id,
				"maillotID"=> $maillotID,
				"dateVote"=> date('d-m-Y, H:i:s'),
				"type"=> STAGE
			);


			// Récupère les votes existants

			$file = "../data/votes.json";

			if (file_exists($file)) {

				$alreadyVoted = false;
				$countMail = 0;
				$votes = json_decode(file_get_contents($file),true);
				if (count($votes)==0) {
					$votes = [];
				}
				else {

					// Création de variable pour savoir si id a déjà voté pour le maillotID et si id à voter plus de fois que votesPerUser
					foreach ($votes as $vote) {

						$id_from_json = $vote['id'];
						$maillotID_from_json = $vote['maillotID'];

						//on compare le mail saisit et celui dans notre tableau
						if($id_from_json == $id && $maillotID_from_json == $maillotID) $alreadyVoted = true;

						//on compare aussi l'identifiant du maillot avec celui dans le fichier
						if($id_from_json == $id) $countMail++;
					}

				}

				// l'id a déjà voté pour ce maillot
				if($alreadyVoted) {
					echo $log[2];
				}
				else {
					// la session a atteint la limite de vote
					if($countMail >= $votesPerUser || $entries >= $votesPerUser) {
						echo $log[3];
					}
					// Le vote rempli toute les conditions, il est sauvegardé
					else {

						// Stocke en session le nombre de vote pour éviter qu'une même session puisse voter plusieurs fois en utilisant des mails différents
						if(isset($_SESSION['id'])) $_SESSION['entries'] += 1;
						else $_SESSION['entries'] = 0;

						$_SESSION['id'] = $id;


						// Ajoute le vote dans le fichier votes.json
						array_push($votes, $newVote);
						$data = json_encode($votes,  JSON_PRETTY_PRINT);
						set_file_data($file, $data, 'w');


						// Met à jour le total de vote du maillot correspondant dans data.json
						$file = '../data/data.json';
						if (file_exists($file)) {
							
							$arr_items = json_decode(file_get_contents($file), true);
							$match = false;
							$index = 0;
							foreach ($arr_items as $key => $item) {
								if($item['maillotID'] ==  $maillotID) {
									$match = true;
									$index = $key;
									$nbVotes = $item['nbVotes'][STAGE] + 1;
								}
							}

							if($match) {
								$arr_items[$index]['nbVotes'][STAGE] = $nbVotes;
								$data = json_encode($arr_items, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
								set_file_data($file, $data, 'w');
							}

						}

						echo $log[4];
					}
				}

			}
		}
		else {
			echo $log[0];
		}
	}

	/**********************************************************************************************************
	Défini les données meta pour Open Graph
	**********************************************************************************************************/
	function set_meta_data() {

		global $meta;

		$article = request_data();
		$maillotID = $_GET['search'];
		
		$meta .= '<meta property="og:title" content="Programme de soutien du rugby amateur par le coq sportif et la FFR" />';
		$meta .= '<meta property="og:type" content="article" />';
		$meta .='<meta property="og:description" content="Dans le cadre du programme, chaque club de rubgy a pu créer son propre maillot. Soutenez le club '. $article['data'][0]['nomClub'] .' et ses couleurs en votant pour désigner la meilleure création." />';
		$meta .='<meta property="og:url" content="'. HTTP . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']. '" />';
		$meta .='<meta property="og:image" content="'. HTTP . $_SERVER['HTTP_HOST'] . APP_DIR . $article['data'][$maillotID]['imageMaillot'] .'" />';

		return $meta;
	}


	/**********************************************************************************************************
	Fonction de Partage
	**********************************************************************************************************/
	function share() {
		$to = [];
		if($_POST['share_to_1']!='') array_push($to, htmlentities($_POST['share_to_1']));
		if($_POST['share_to_2']!='') array_push($to, htmlentities($_POST['share_to_2']));
		if($_POST['share_to_3']!='') array_push($to, htmlentities($_POST['share_to_3']));


		$from = ($_POST['share_from']!='') ? $_POST['share_from'] : 'noreply@lecoqsportif.com';
		$sharer = ($_POST['share_from']!='') ? $from : 'une personne';
		$subject = 'Ensemble, soutenons ce club de rugby !';
		
		$maillotID = htmlentities($_POST['form_to_share_maillotID']);
		$_GET['search'] = $maillotID;
		$_GET['type'] = 'get';
		$maillot = request_data();
		$nbVotes = $maillot['data'][$maillotID]['nbVotes'][STAGE];
		$voteTxt = ($nbVotes > 1) ? ' personnes ont voté pour ce maillot' : ' personne a voté pour ce maillot';
		$votes = ($nbVotes > 0) ? '<br/>' . $nbVotes . $voteTxt . '<br/>' : '';

		$tpl['email'] = file_get_contents('../view/_email_share.php');
		$tpl['email'] = str_replace('{{IMGMAILLOT}}', HTTP . $_SERVER['HTTP_HOST'] . APP_DIR . $maillot['data'][$maillotID]['imageMaillot'], $tpl['email']);
		$tpl['email'] = str_replace('{{IMGLOGO}}', HTTP . $_SERVER['HTTP_HOST'] . APP_DIR . $maillot['data'][$maillotID]['imageLogo'], $tpl['email']);
		$tpl['email'] = str_replace('{{URL}}', HTTP . $_SERVER['HTTP_HOST'] . '/fr-fr/soutien-rugby-amateur/maillot-'.$maillotID, $tpl['email']);
		$tpl['email'] = str_replace('{{NOMCLUB}}', $maillot['data'][$maillotID]['nomClub'], $tpl['email']);
		$tpl['email'] = str_replace('{{VILLE}}', $maillot['data'][$maillotID]['ville'], $tpl['email']);
		$tpl['email'] = str_replace('{{VOTES}}', $votes, $tpl['email']);
		$tpl['email'] = str_replace('{{FROM}}', $sharer, $tpl['email']);

		send_mail($to, $from, $subject, $tpl['email']);
	}

	/**********************************************************************************************************
	Envoi de mail
	**********************************************************************************************************/
	function send_mail($to,$from,$subject,$tpl) {

		foreach ($to as $email) {

			if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8\r\n";
				$headers .='Content-Transfer-Encoding: 8bit'."\r\n";
				$headers .= 'To: '.$to."\r\n";
				$headers .= 'From: '.$from."\r\n";
				$headers .= 'Reply-To: <'.$from.'>'. "\r\n";

				// Envoi
				mail($email, $subject, $tpl, $headers);
			}
		}

	}

	/**********************************************************************************************************
	Tags pour autocompletion
	**********************************************************************************************************/

	function availablesTags() {
		$file = 'data/data.json';
		$data = file_get_contents($file);
		$arr_items = json_decode($data, true);
	
		$available = array();
		foreach ($arr_items as $key=>$value) {
			$intitule = $arr_items[$key]['intitule'];
			array_push($available, $intitule);
		}

		return json_encode($available);
	}
?>