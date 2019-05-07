<?php 
 $url = file_get_contents('https://script.google.com/macros/s/AKfycbz4UBLi0neUzzQCOdg_FZ9FV-L0NCOdmUIcX2JG-QR3h_VvhEQ/exec');
 $json = json_decode($url,true);
$tableau = array();
foreach ($json["user"] as $key => $value) {
	$horodateur = $value["horodateur"];
	$id = $value["id"];
	$maillotID = $value["maillotid"];
	$type = $value["type"];
	$email = $value["email"];
	$contenu =  $id.";".$maillotID.";".$type."<br>";
	array_push($tableau, $contenu);
}
$tableau_vote = array();
foreach ($tableau as $value) {
	// eclate la ligne pour récuperer les données requises
	$ligne = explode(';', $value);
	//Déclaration des variables
	$maillotID = $ligne[1];
	if( array_key_exists($maillotID, $tableau_vote)) {
		$tableau_vote[$maillotID] +=1;
	} else {
		$tableau_vote[$maillotID] =1;
	}
}
	var_dump($tableau_vote);
?>
