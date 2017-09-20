; (function ($) {
	'use-strict';

	$.fn.addFileToForm = function (options, value) {
		if (value === undefined) {
			var defauts = {
				itemForAppend: $(this).parents('form').find('.list-image'),
				inputCompteur: '#nbImage',
				model: '<div class="form-group">' +
				'<label for="image-{{0}}" class="col-sm-2 control-label">Image {{0}}</label>' +
				'<div class="col-sm-10">' +
				'<input type="file" name="image-{{0}}" id="image-{{0}}">' +
				'</div>' +
				'</div>'
			};
			var defauts = $.extend(defauts, options);

			return $(this).each(function (index, element) {
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
					$(defauts.inputCompteur).val(btnAjoutFichier.data('index'));
				});

				var imgToDelete = btnAjoutFichier.parents('form').find('input[name=toDelete]');
				btnAjoutFichier.parents('form').find('.glyphicon.glyphicon-remove').each(function (index, item) {
					$(item).on('click', function (event) {
						var toDelete = $(this).parent().attr('data-id');
						if (imgToDelete.val().indexOf(toDelete) < 0) {
							imgToDelete.val((imgToDelete.val() == "" ? imgToDelete.val() : imgToDelete.val() + ";") + toDelete);
						}
						$(this).parent().remove();
					});
				});
			});
		} else {
			return $(this).each(function (index, element) {
				$(this).data(options, value);
			});
		}
	};

	$.fn.compteur = function (options) {
		function updateData(event, data) {
			$.ajax({
				url: "/index.php/compteur",
				method: 'POST',
				data: {
					'idPersonne': data.idUser,
					'idArticle': data.idArticle,
					'newValue': data.newValue
				},
				success: function (response) {
					if (data.cadeaux && data.cadeaux != null) {
						var articlesCorrespondant = $.grep(data.cadeaux, function (elementOfArray, index) {
							return elementOfArray.id === data.idArticle;
						});
						$.each(articlesCorrespondant, function (index, item) {
							item.quantiteReserveeUtilisateur = data.newValue;
						});
					}
					data.feedBack.text(data.feedBack.attr('data-value'));
					data.feedBack.show();
					data.feedBack.fadeOut(1500);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(jqXHR, textStatus, errorThrown);
				}
			});
		}

		var defauts = {
			'idUser': null,
			'isEditMode': false,
			'cadeaux': null
		};

		var defauts = $.extend(defauts, options);

		return this.each(function (index, element) {
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

			var jButtonPlus = jElement.find('button.plus').on('click', { 'labelQteReserve': jQteReservee, 'inputCompteur': jInput, 'max': qteSouhaitee }, function (event) {
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
					$(event.target).trigger('updateData', { 'newValue': qteUtilisateur, 'idArticle': idArticle, 'idUser': defauts['idUser'], 'feedBack': jFeedBack, 'cadeaux': defauts['cadeaux'] });
				}
			});
			var jButtonMoins = jElement.find('button.moins').on('click', { 'labelQteReserve': jQteReservee, 'inputCompteur': jInput, 'min': qteMin }, function (event) {
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
					$(event.target).trigger('updateData', { 'newValue': qteUtilisateur, 'idArticle': idArticle, 'idUser': defauts['idUser'], 'feedBack': jFeedBack, 'cadeaux': defauts['cadeaux'] });
				}
			});

			if (defauts['isEditMode'] === false) {
				jButtonMoins.on('updateData', updateData);
				jButtonPlus.on('updateData', updateData);
			}
		});
	};

	$.fn.connexionRapide = function (options) {
		var defauts = $.extend({}, options);

		return $(this).each(function () {
			if (Cookies.get('utilisateur')) {
				var utilisateur = JSON.parse(Cookies.get('utilisateur'));
				var contenu = $('<h1 class="navbar-right navbar-brand nomargin bienvenue">').append('<br><small><a href="' + $(this).attr('data-deconnexion') + '">deconnexion</a></small></h1>');
				contenu.prepend($(this).attr('data-message').replace('{{login}}', utilisateur.login));
				$(this).append(contenu);
			}
		});
	};

	$.fn.showMessage = function (options) {
		var defauts = $.extend({}, options);

		return $(this).each(function () {
			var condition = $(this).attr('data-show-message');
			if (!condition) {
				return;
			}
			var message = $(this).attr('data-message');
			var niveau = $(this).attr('data-niveau');
			if (message.trim() !== "") {
				$(this).text(message);
				if (niveau.trim() != "") {
					$(this).addClass(niveau);
				}
			}
		});
	};

	$.fn.menu = function (options) {
		var defauts = $.extend({}, options);

		return $(this).each(function () {
			var elem = $(this);
			$.ajax(defauts['url'])
				.success(function (data) {
					elem.html(data);
					$('[data-plugin=navigationHistorique]').navigationHistorique();
				}).error(function () {
					console.log(arguments);
				});
		});
	}

	$.fn.navigationHistorique = function (options) {
		var defauts = $.extend({}, options);
		return $(this).each(function () {
			$(this).on('click', function (event) {
				event.preventDefault();
				history[$(this).attr('href').replace('#', '')]();
			});
		});
	};
})(jQuery);