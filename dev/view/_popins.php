<div id="popins" class="popins">
	<div class="popin-overlay doclose"></div>
	<div class="popin popin-vote">
		<div class="popin-title std-title">
			 Voter pour le maillot
		</div>
		<form id="form_to_vote" class="form" method="post">
			<div class="popin-content std-txt">
				<div class="form-maillot-img"></div>
			</div>
			<div class="popin-action">
				<label class="popin-label-partage-vote">Email</label>
				<input type="text" name="form_to_vote_email" id="form_to_vote_email" class="email obligatoire popin-action-email-partage" placeholder="" /> <br>
				<div class="popin-action-checkbox checkbox-optin">
					<input type="checkbox" id="form_to_vote_optin" name="optin01" class="css-checkbox optins_field" value="1"> 
					<label for="form_to_vote_optin" class="css-label-checkbox optins_field"> Recevoir par email l'actualité du coq sportif</label>
				</div>
				<div class="popin-action-checkboxa checkbox-age">
					<input type="checkbox" id="form_to_vote_age" name="opti1" class="css-checkbox optins_field" value="1"> 
					<label for="form_to_vote_age" class="css-label-checkbox optins_field"> Je certifie avoir 16 ans ou plus</label> 
				</div>
				<input type="hidden" name="form_to_vote_maillotID" id="form_to_vote_maillotID" class="maillotID" />
				<input type="hidden" name="form_date_vote_maillot" id="date_vote_maillot" class="voteDATE" value="<?php date('Y-m-d\TH:i:sO') ?>">
				<div class="form-log erreur_log"></div>
				<input type="submit" class="tovote button-vote" value="voter" />
				<div class="information-popin-vote-partage">
					<p>Dans le cadre du vote, votre email sert uniquement de bulletin de participation.<br>
					
					En cochant la case "Recevoir toute l'actualité du coq sportif,vous consentez à recevoir notre actualité et nos offres par voie électronique. Vous pourrez changer vos préférences ou vous désinscrire à tout moment en modifiant vos paramètres sur votre profil et à travers les liens de désinscription.
					</p>	
				</div>
			</div>
		</form>
		<div class="popin-close">
			<a href="javascript:;" class="doclose icon">Fermer</a>
		</div>
	</div>
	<div class="popin popin-share">
		<div class="popin-title std-title">
			Soutenir ce club
		</div>
		<form id="form_to_share" class="form" method="post">
			<div class="popin-content std-txt">
				<div class="form-maillot-img"></div>
			</div>
			<div class="popin-action">
				<input type="hidden" id="share_from" name="share_from" />
				<input type="hidden" name="form_to_share_maillotID" id="form_to_share_maillotID" class="maillotID" />
				<label class="popin-label-partage-vote">Partager par email : </label>
				<input class="email obligatoire popin-action-email-partage" type="text" id="share_to_1" name="share_to_1"  placeholder="Email 1" />
				<input class="email obligatoire popin-action-email-partage" type="text" id="share_to_2" name="share_to_2"  placeholder="Email 2" />
				<input class="email obligatoire popin-action-email-partage" type="text" id="share_to_3" name="share_to_3"  placeholder="Email 3" />
				<div class="form-log erreur_log"></div>
				<div class="bouton-envoyer-partage">
					<input type="submit" class="toshare button-vote" value="Partager" />
				</div>
				<div id="social-media" class="partage-social-media">
					<label class="titre-social-media ">Partager sur les réseaux sociaux:</label>
					<a data-href="https://twitter.com/intent/tweet/?url=<?php echo APP_URL; ?>/maillot-" class="totwitter">
						<img class="logo-social" src="<?php echo APP_DIR; ?>images/svg/twitter.svg" alt="twitter"/>
					</a>
					<a data-href="http://www.facebook.com/share.php?u=<?php echo APP_URL; ?>/maillot-" class="tofacebook">
						<img class="logo-social" src="<?php echo APP_DIR; ?>images/svg/facebook.svg" alt="facebook"/>
					</a>
				</div>
				
			</div>
		</form>
		<div class="popin-close">
			<a href="javascript:;" class="doclose icon">Fermer</a>
		</div>
	</div>
</div>