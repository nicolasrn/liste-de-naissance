;(function($) {
	'use-strict';

	$.fn.addFileToForm = function (options) {

		var defauts = {
			itemForAppend: $(this).parents('form').find('.list-image'),
			model: `<div class="form-group">
						<label for="image-{{0}}" class="col-sm-2 control-label">Image {{0}}</label>
						<div class="col-sm-10">
							<input type="file" name="image-{{0}}" id="image-{{0}}">
						</div>
					</div>`
		}; 

		defauts = $.extend(defauts, options); 

		return $(this).each(function(index, element) {
			var index = 0;
			var btnAjoutFichier = $(this);

			btnAjoutFichier.on('click', function (event) {
				var template = defauts['model'].replace(/\{\{0\}\}/g, ++index).replace(/\{\{1\}\}/g, index + 1);
				$(defauts['itemForAppend']).append($(template));
			});
		});
	};

	$.fn.compteur = function(options) {
		function updateData(event, data) {
			$.ajax({
				url: "/web/services/RestController.php", 
				method: 'POST',
				data: {
					'model': 'articles',
					'action': 'updateReservation',
					'idUser': data.idUser,
					'idArticle': data.idArticle,
					'newValue': data.newValue
				},
				success : function(data) {
					console.log(data);
				}, 
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR, textStatus, errorThrown);
				}
			});
		}

		var defauts = {
			idUser: null,
			isEditMode: false
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
				if (defauts['isEditMode'] === false) { 
					$(event.target).trigger('updateData', {newValue: qteUtilisateur, idArticle: idArticle, idUser: defauts['idUser']});
				}
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

				if (defauts['isEditMode'] === false) { 
					$(event.target).trigger('updateData', {newValue: qteUtilisateur, idArticle: idArticle, idUser: defauts['idUser']});
				}
			});

			if (defauts['isEditMode'] === false) { 
				jButtonMoins.on('updateData', updateData);
				jButtonPlus.on('updateData', updateData);
			}
		});
	};
})(jQuery);