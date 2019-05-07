<html>
<head>
	<meta charset="utf-8">
	<style type="text/css">
		.formulaire_connexion {
			padding-top: 22px;
		}
		.bouton_connexion{
			
		}

		.input_connexion {
		}
	</style>
</head>
<body>
<?php
		$login = $_POST['login'];
		$mdp = $_POST['mdp'];
		
				if(hash('md5', $login)=='55aebc0599e53f7fcc1c44578bf6eb60' && hash('md5', $mdp)=='edf8e477ea3dbb72c9d8a64c4ffa5f25') {
				include('_functions.php');
				$file = '../data/data.json';
				$backup = file('../data/backup.csv', FILE_SKIP_EMPTY_LINES);
				unset($backup[0]);
				$tableau_vote = array();
				foreach($backup as $value) {
					// eclate la ligne pour récuperer les données requises
					$ligne = explode(';', $value);
					//Déclaration des variables
					$maillotID = $ligne[2];
					if( array_key_exists($maillotID, $tableau_vote)) {
						$tableau_vote[$maillotID] +=1;
					} else {
						$tableau_vote[$maillotID] =1;
					}
				}
				
				//récuperation de data.json
				$data = file_get_contents($file);
				
				$arr_items = json_decode($data, true);
				
				foreach ($tableau_vote as $key=>$value) {
					echo $key.';<br/>';
					$arr_items[$key]['nbVotes']['élégant'] = $value;
				}
				
				//Ecriture dans data.json
				$json = json_encode($arr_items, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
				set_file_data($file, $json, 'w');

				echo 'Success';
			
			}
			else {
?>
	<form method="post" action="backup.php" class="formulaire_connexion">
		<label>Login: </label> <input type="text" name="login" id="login" class= "input_connexion">
		<label>mots de passe: </label> <input type="password" name="mdp" id="mdp" class="input_connexion">
		<input type="submit" name="connexion" class="bouton_connexion">
	</form>
<?php	
			}		
?>	
</body>
</html>
