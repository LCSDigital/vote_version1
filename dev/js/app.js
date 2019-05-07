/****************************************************************************************************/
var Vote = {
	init: function() {

		var optin = $('#form_to_vote_optin').is(':checked');

		if(optin) $('.checkbox-age').show();
		else $('.checkbox-age').hide();
		
		$('#form_to_vote_optin').on('change', function() {
			if($(this).is(':checked')) $('.checkbox-age').show();
			else $('.checkbox-age').hide();
		});

		Vote.controls();

	},
	controls: function() {

		$('.tovote').on('click', function(e) {
			
			e.preventDefault();
			
			var form = $('#form_to_vote');
			form.find('.form-log').html('');

			var log = [
				"Veuillez saisir votre email.",
				"L'email n'est pas valide.",
				"Pour recevoir l'actualité du coq sportif, vous devez certifier avoir 16 ans ou plus."
			];

			// Vérifie la validité des informations avant envoi
			var e = 0;
			var email = $('#form_to_vote_email').val();
			var optin = $('#form_to_vote_optin').is(':checked');
			var age = $('#form_to_vote_age').is(':checked');

			if(email=='') {
				e++;
				form.find('.form-log').append('<div>'+log[0]+'</div>');
			}
			if(!(/^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$/i.test(email)) && email!='') {
				e++;
				form.find('.form-log').append('<div>'+log[1]+'</div>');
			}
			if(optin && !age) {
				e++
				form.find('.form-log').append('<div>'+log[2]+'</div>');
			}

			if(e==0) {
				$('#share_from').val(email);
				Vote.submit();
				if(optin) Vote.optin();
			}

		});

		$('body').on('click', '.get_data', function() {

			var item = $(this).parents('.item, .article');
			var maillotID = item.data('item').id;
			var maillotSrc = item.data('item').src;

			var message = 'venez voter pour le maillot de votre club';
			
			$('input.maillotID').val(maillotID);
			$('input.maillotSrc').val(maillotSrc);

			$('.form-maillot-img').html('<img src="'+maillotSrc+'" alt="" />');
		});

	},
	optin: function() {

		var email = $('#form_to_vote_email').val();
		var json = {
            "email": email,
            "country": 'FR',
            "origin_of_contact":"Eshop",
            "language": 'fr',
            "group":{
            	'Rugby': true
            },
            "status":"subscribed"
        };

        var inputs = JSON.stringify(json);
		
		$.ajax({
			url: 'mailchimp/createorupdate',
			type:"POST",
			data: {json:inputs},
			success: function (data) {

				var topage = '^soutien-rugby-amateur$';
				$.ajax({
					url: 'mailchimp/get',
					type:"POST",
					data: {json:JSON.stringify({"email":email})},
					success: function (data) {
						if(JSON.parse(data).merge_fields.TOPPAGE.indexOf(topage)==-1) {
							topage += '|'+JSON.parse(data).merge_fields.TOPPAGE;
							$.ajax({
								url: 'mailchimp/createorupdate',
								type:"POST",
								data: {json:JSON.stringify({"email":email,"toppage":topage})}
							});
						}
					}
				});

			}
		});

	},
	submit: function() {

		var form = $('#form_to_vote');
		var	formData = new FormData(form[0]);

		loader(1);
		$.ajax({
			url: APP_DIR+'controller/_functions.php?a=vote&type=get',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			complete: function(data) {
				// Récupère la réponse
				var response = JSON.parse(data.responseText);

				// Test le code de la réponse (code 4 = vote valider)
				if(response.code == 4) {

					var emailid = response.emailid;
					var email = $('#form_to_vote_email').val();
					var maillotID = $('#form_to_vote_maillotID').val();

					// Sauvegarde le vote
					$.ajax({
						url: 'https://docs.google.com/forms/d/e/1FAIpQLSetY0KKnCj96qPyk95Dcf5b_s_5tLBxbUCNhjIbiyZmprHUsQ/formResponse',
						data: {
							"entry.1543259727": emailid,
							"entry.1164903087": maillotID,
							"entry.879728416": STAGE,
							"entry.60549451": email
						},
						type: 'POST',
						dataType: 'xml',
						statusCode: {
							0 : function() {
								
							}
						}
					});

					// Incrémente le nombre de votes dans la liste
					var nbVotes = parseInt($('#'+maillotID).find('.count-votes').text()) + 1;
					var voteTxt = (nbVotes > 1) ? ' votes' : ' vote';
					$('#'+maillotID).find('.count-votes').text(nbVotes+voteTxt);

				}

				// Affiche le message de la réponse
				form.find('.form-log').text(response.msg);
				loader(0);
			}
		});

	}
}


/****************************************************************************************************/
var Share = {
	init: function() {

		Share.controls();

	},
	controls: function() {

		$('.toshare').on('click', function(e) {

			e.preventDefault();
			var form = $('#form_to_share');
			form.find('.form-log').html('');

			var log = [
				"Veuillez saisir un email.",
				"L'email {{i}} n'est pas valide.",
				"Veuillez saisir des emails différents."
			];

			var e = 0;
			var email = [];
			form.find('.popin-action-email-partage').each(function(i) {
				email[i] = $(this).val();
				if(email[i]=='' && i==0) {
					e++;
					form.find('.form-log').append('<div>'+log[0]+'</div>');
				}
				if(!(/^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$/i.test(email[i])) && email[i]!='') {
					e++;
					form.find('.form-log').append('<div>'+log[1].replace('{{i}}', i+1) +'</div>');
				}
				i++;
			});

			if((email[0]!='' && email[0]==email[1]) || (email[0]!='' && email[0] == email[2]) || (email[1]!='' && email[1] == email[2])) {
				e++
				form.find('.form-log').append('<div>'+log[2]+'</div>');
			}

			if(e==0) {
				Share.send();
			}

		});

		$('body').on('click', '.get_data', function() {

			var item = $(this).parents('.item, .article');
			var maillotID = item.data('item').id;
			var maillotSrc = item.data('item').src;

			var message = 'venez voter pour le maillot de votre club';
			
			$('input.maillotID').val(maillotID);
			$('input.maillotSrc').val(maillotSrc);
			$('.totwitter').attr('href', $('.totwitter').attr('data-href') + maillotID);
			$('.tofacebook').attr('href', $('.tofacebook').attr('data-href') + maillotID);
			$('.form-maillot-img').html('<img src="'+maillotSrc+'" alt="" />');
		});

	},
	send: function() {

		var form = $('#form_to_share');
		var	formData = new FormData(form[0]);
		loader(1);
		$.ajax({
			url: APP_DIR+'controller/_functions.php?a=share',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			complete: function(data) {
				console.log(data.responseText)
				form.find('.form-log').text('Merci pour votre soutien. Les destinataires recevront un email.');
				loader(0);
			}	
		});

	}
}



/****************************************************************************************************/
function loader(s) { 
	var loader = $('#loader');
	loader.find('.loader-status').text('');
	(s) ? loader.fadeIn() : loader.fadeOut();
}

/****************************************************************************************************/
var List = {
	init: function() {

		List.pagination._init();
		List.ordering._init();
		List.search._init();

	},
	pagination: {
		pages: $('.pagination').data('pages'),
		_init: function() {

			List.pagination._controls();
			List.pagination._update();

		},
		_controls: function() {
		
			$('.gotopage').on('change', function() {
				
				$('.item').addClass('off');

				var pages = List.pagination.pages;
				var itemsperpage = $('.pagination').data('itemsperpage');
				var pageindex = $(this).val();
				var start = pageindex*itemsperpage;
				var end = pageindex*itemsperpage+itemsperpage;

				if($('.item-'+start)[0]) {
				
					$('.item').addClass('off').slice(start, end).removeClass('off');
					
					List.pagination._update();
				}
				else {

					loader(1);

					var search = ($('#search').val()!='') ? '&search='+ $('#search').val() : '';
					var orderby = ($('#orderby').val()!='') ? '&orderby='+ $('#orderby').val() : '';
					var order = ($('#order').val()!='') ? '&order='+ $('#order').val() : '';

					$.ajax({
						url: APP_DIR+'controller/_functions.php?a=requestdata&type=get&start='+start+'&end='+end+search+orderby+order+'#list-content',
						complete: function(data) {

							var items = data.responseText;
							$('.items').append(items);
							List.pagination._update();
							loader(0);
						}		
					});

				}

			});


			$('.prevnext').on('click', function() {

				var pageindex = parseInt($('.gotopage').val());
				var i = ($(this).hasClass('gotoprev')) ? -1 : 1;

				$('.gotopage').val(pageindex + i).trigger('change');

			});

		},
		_update: function() {

			var pageindex = $('.gotopage').val();
			$('.prevnext').removeClass('off');

			if(pageindex==0) $('.gotoprev').addClass('off');
			if(pageindex==List.pagination.pages-1) $('.gotonext').addClass('off');

		}

	},
	ordering: {
		_init: function() {

			List.ordering._controls();

		},
		_controls: function() {
			
			$('.orderby').on('change', function() {
				var ordering = $(this).val().split(',');
				var orderby = (ordering[0]!='') ? '&orderby='+ordering[0]: '';
				var order = (ordering[1]!='') ? '&order='+ordering[1]: '';

				$('#orderby').val(orderby);
				$('#order').val(order);

				var search = ($('#search').val()!='') ? '&search='+ $('#search').val() : '';
				document.location.href = APP_URL + '?' + search + orderby + order + '#list-content';
			});
		
		}
	},
	search: {
		_init: function() {

			if(document.location.href.indexOf('search')!=-1) $(window).scrollTop($('#list-content').offset().top);

			$('.tosearch').on('click', function(e) {
				e.preventDefault();
				var search = $('#search').val();
				$('#form_to_search').submit();
			});

		}
	}
}

/****************************************************************************************************/
 

/****************************************************************************************************/
var Popins = {
	init: function() {

		$('body').on('click', '.loadpopin', function() {
			$('body').data('scrollto', $(window).scrollTop());

			$('html,body').animate({ scrollTop: 0 }, 500);
			var p = $(this).attr('data-popin');
			if(p!='') Popins.open(p);
		});

		$('#popins').on('click', '.doclose', function() {
			Popins.close();
		});

	},
	open: function(p) {
		$('#popins').addClass('active').find('.'+p).addClass('current');
	},
	close: function() {
		$('#popins').removeClass('active').find('.popin').removeClass('current');
		var scrollto = $('body').data('scrollto');
		$('html,body').animate({ scrollTop: scrollto }, 500);
	}
}


/****************************************************************************************************/
$(document).ready(function() {

	List.init();
	Popins.init();
	Vote.init();
	Share.init();

	if($('.ope-slider')[0]) {

		var slider = setInterval(function() {
			var i = $('.ope-slider').find('.current').index();
			var l = $('.banner-bg').length;
			
			$('.ope-slider').find('.current').removeClass('current');

			if(i==l-1) $('.banner-bg').eq(0).addClass('current');
			else $('.banner-bg').eq(i).next().addClass('current');

		}, 1000);

	}
});