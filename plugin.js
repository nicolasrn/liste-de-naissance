;(function($) {
	'use-strict';

	$.fn.compteur = function(options) {
		function updateData(event, data) {
			$.ajax({
				url: "/web/services/RestController.php", 
				method: 'POST',
				data: {
					'model': 'articles',
					'idUser': data.idUser,
					'idArticle': data.idArticle,
					'newValue': data.newValue
				},
				success : function(data) {
					console.log(data);
				}, 
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(data, jqXHR, textStatus, errorThrown);
				}
			});
		}

		var defauts = {
			idUser: null
		}; 

		defauts = $.extend(defauts, options); 

		return this.each(function(index, element) {
			var jElement = $(this);
			var jInput = jElement.find('input[type="text"]');
			var jQteReservee = jElement.find('.quantiteReservee');
			var idArticle = jElement.attr('id').replace('compteur-', '');
			var qteSouhaitee = parseInt(jElement.find('.quantiteSouhaitee').text());
			var qteReserveeInitial = parseInt(jQteReservee.text());
			var qteReservee = qteReserveeInitial;
			var qteUtilisateurInitial = parseInt(jInput.val());
			var qteUtilisateur = qteUtilisateurInitial;
			var qteMin = 0;
			
			var jButtonPlus = jElement.find('button.plus').on('click', {labelQteReserve: jQteReservee, inputCompteur: jInput, max: qteSouhaitee}, function(event) {
				qteUtilisateur = parseInt(event.data.inputCompteur.val());
				var isMaxDepasse = ++qteUtilisateur + (qteReserveeInitial - qteUtilisateurInitial) > event.data.max;
				++qteReservee;
				if (isMaxDepasse) {
					--qteUtilisateur;
					--qteReservee;
				}
				event.data.inputCompteur.val(qteUtilisateur);
				event.data.labelQteReserve.text(qteReservee);
				$(event.target).trigger('updateData', {newValue: qteUtilisateur, idArticle: idArticle, idUser: defauts['idUser']});
			});
			var jButtonMoins = jElement.find('button.moins').on('click', {labelQteReserve: jQteReservee, inputCompteur: jInput, min: qteMin}, function(event) {
				qteUtilisateur = parseInt(event.data.inputCompteur.val());
				var isMinDepasse = --qteUtilisateur < event.data.min;
				--qteReservee;
				if (isMinDepasse) {
					qteUtilisateur = event.data.min;
					++qteReservee;
				}
				event.data.inputCompteur.val(qteUtilisateur);
				event.data.labelQteReserve.text(qteReservee);
				$(event.target).trigger('updateData', {newValue: qteUtilisateur, idArticle: idArticle, idUser: defauts['idUser']});
			});

			jButtonMoins.on('updateData', updateData);
			jButtonPlus.on('updateData', updateData);

		});
	}
})(jQuery);