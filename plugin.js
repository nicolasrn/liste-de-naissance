;(function($) {
	'use-strict';

	$.fn.addFileToForm = function (options, value) {
		if (value === undefined) {
			var defauts = {
				itemForAppend: $(this).parents('form').find('.list-image'),
				model: '<div class="form-group">' + 
							'<label for="image-{{0}}" class="col-sm-2 control-label">Image {{0}}</label>' +
							'<div class="col-sm-10">' +
								'<input type="file" name="image-{{0}}" id="image-{{0}}">' +
							'</div>' +
						'</div>'
			};
			defauts = $.extend(defauts, options); 

			return $(this).each(function(index, element) {
				var btnAjoutFichier = $(this);
				btnAjoutFichier.data('index', 0);

				btnAjoutFichier.on('click', function (event, item) {
					var index = btnAjoutFichier.data('index');
					var template = defauts['model'].replace(/\{\{0\}\}/g, ++index).replace(/\{\{1\}\}/g, index + 1);
					btnAjoutFichier.data('index', index);
					var elemToInsert = $(template);
					if (item && item !== undefined && item !== null) {
						elemToInsert.find('input').val(item);
					}
					$(defauts['itemForAppend']).append(elemToInsert);
				});
				
				if (defauts['img'] && defauts['img'] !== undefined) {
					var imgToDelete = btnAjoutFichier.parents('form').find('input[name=toDelete]');

					var listeImg = $.map(defauts.img, function(val, key) {
						return '<li data-id="' + val.id + '">' + val.src + '<button type="button" class="glyphicon glyphicon-remove"></button></li>';
					});
					$('<ul>' + listeImg.join('') + '</ul>').insertBefore(btnAjoutFichier);
					btnAjoutFichier.parent().find('.glyphicon.glyphicon-remove').each(function(index, item) {
						$(item).on('click', function(event) {
							var toDelete = $(this).parent().attr('data-id');
							if (imgToDelete.val().indexOf(toDelete) < 0) {
								imgToDelete.val((imgToDelete.val() == "" ? imgToDelete.val() : imgToDelete.val() + ";") + toDelete);
							}
							$(this).parent().remove();
						});
					});
				}
			});
		} else {
			return $(this).each(function(index, element) {
				$(this).data(options, value);
			});
		}
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
				success : function(response) {
					data.feedBack.text(data.feedBack.attr('data-value'));
					data.feedBack.show();
					data.feedBack.fadeOut(1500);
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
			var jFeedBack = jElement.find('.feedBack'); 

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
					return;
				}
				event.data.inputCompteur.val(qteUtilisateur);
				event.data.labelQteReserve.text(qteReservee);
				if (defauts['isEditMode'] === false) { 
					$(event.target).trigger('updateData', {newValue: qteUtilisateur, idArticle: idArticle, idUser: defauts['idUser'], feedBack: jFeedBack});
				}
			});
			var jButtonMoins = jElement.find('button.moins').on('click', {labelQteReserve: jQteReservee, inputCompteur: jInput, min: qteMin}, function(event) {
				qteUtilisateur = parseInt(event.data.inputCompteur.val());
				var isMinDepasse = --qteUtilisateur < event.data.min;
				--qteReservee;
				if (isMinDepasse) {
					qteUtilisateur = event.data.min;
					++qteReservee;
					return;
				}
				event.data.inputCompteur.val(qteUtilisateur);
				event.data.labelQteReserve.text(qteReservee);

				if (defauts['isEditMode'] === false) { 
					$(event.target).trigger('updateData', {newValue: qteUtilisateur, idArticle: idArticle, idUser: defauts['idUser'], feedBack: jFeedBack});
				}
			});

			if (defauts['isEditMode'] === false) { 
				jButtonMoins.on('updateData', updateData);
				jButtonPlus.on('updateData', updateData);
			}
		});
	};

	$.fn.handleMesActions = function(options) {
		defauts = $.extend({}, options);
		var mesActions = $(this);
		var idListe = mesActions.attr('data-on');

		var liste = defauts['liste'].slice(0);
		var callbackRefresch = defauts['callbackRefresch'];
		var context = defauts['context'];
		
		mesActions.find('#mes-articles').on('click', function(event) {
			event.preventDefault();
			liste = liste.sort(function(a, b) {
				var cmp = b.quantiteReserveeUtilisateur - a.quantiteReserveeUtilisateur;
				if (cmp == 0) {
					return b.libelle - a.libelle;
				}
				return cmp;
			});
			callbackRefresch(liste, context);
			$(mesActions.selector).next(idListe).find('input[id^=quantiteReserveeUtilisateur]').each(function(index, qteUtilisateur) {
				var article = $(this).parents('[id^=article-]');
				if (qteUtilisateur.value > 0) {
					article.css({opacity: 1});
				} else {
					article.css({opacity: 0.2});
				}
			});
		});

		mesActions.find('#tous-les-articles').on('click', function(event) {
			event.preventDefault();
			callbackRefresch(liste, context);
			$(mesActions.selector).next(idListe).find('input[id^=quantiteReserveeUtilisateur]').each(function(index, item) {
				$(this).parents('[id^=article-]').css({opacity: 1});
			});
		});
	};
})(jQuery);