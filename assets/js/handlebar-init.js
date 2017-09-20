; (function ($) {

  window.Liste = {
    ETAT_ACTIF: 0,
    ETAT_SUPPRIME: 1
  }

  $.fn.liste = function (options) {

    if ($(this).length === 0) {
      return $(this);
    }

    var defauts = $.extend({
      'user': null
    }, options);
    defauts.templateListe = $(this).find('script');
    defauts.urlWebService = $(this).attr('data-url');
    defauts.etat = $(this).attr('data-etat');
    defauts.templateMesActions = $(this).attr('data-mes-actions');

    var mesActionsTemplate = Handlebars.compile($(defauts.templateMesActions).html());
    var listeNaissanceTemplate = Handlebars.compile($(defauts.templateListe).html());

    $(defauts.templateMesActions).parent().html(mesActionsTemplate());
    $(this).prev().on('keyup', '#search', search.bind(this));

    function refresch(listeParDefaut, context) {
      var self = context || null;
      var search = context.prev().find('#search').val();
      var cadeauxFiltres = listeParDefaut;

      if (search !== "") {
        var options = {
          shouldSort: false,
          threshold: 0.5,
          location: 0,
          distance: 100,
          maxPatternLength: 32,
          minMatchCharLength: 1,
          keys: [
            "libelle"
          ]
        };
        var fuse = new Fuse(cadeauxFiltres, options);
        cadeauxFiltres = fuse.search(search);
      }

      context.html(listeNaissanceTemplate({
        cadeaux: cadeauxFiltres,
        nbColonneMax: 12,
        nbColonneAffichees: 3,
        search: search
      }));
      context.parent().find('.compteur').compteur({
        'idUser': defauts.user.id,
        'context': context
      });
      context.parent().find('.carousel').carousel();
    };

    function search() {
      refresch($(this).data('cadeaux'), $(this));
    }

    function getListe(idUser, etat, callbackSuccess, callbackError) {
      $.ajax({
        url: defauts.urlWebService,
        data: {
          'idUser': defauts.user.id
        },
        method: 'GET',
        success: function (data) {
          return callbackSuccess(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          return callbackError(jqXHR, textStatus, errorThrown);
        }
      });
    }

    function renderListe(context, etat) {
      getListe(defauts.user.id, etat, function (data) {
        context.data('cadeaux', data);
        context.html(listeNaissanceTemplate({
          'cadeaux': data,
          'nbColonneMax': 12,
          'nbColonneAffichees': 3
        }));
        context.find('.compteur').compteur({
          'idUser': defauts.user.id,
          'cadeaux': data
        });
        context.find('.carousel').carousel();
        context.prev().find('#mes-actions').handleMesActions({
          'callbackRefresch': refresch,
          'liste': data,
          'context': context
        });
      }, function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR, textStatus, errorThrown);
      });
    }

    return this.each(function () {
      renderListe($(this));
    });
  };

  $.fn.handleMesActions = function (options) {
		var defauts = $.extend({}, options);
		var mesActions = $(this);
		var idListe = mesActions.attr('data-on');

		var liste = defauts['liste'].slice(0);
		var callbackRefresch = defauts['callbackRefresch'];
		var context = defauts['context'];

		mesActions.find('#mes-articles').on('click', function (event) {
			event.preventDefault();
			liste = liste.sort(function (a, b) {
				var cmp = b.quantiteReserveeUtilisateur - a.quantiteReserveeUtilisateur;
				if (cmp == 0) {
					return b.libelle - a.libelle;
				}
				return cmp;
			});
			callbackRefresch(liste, context);
			$(idListe).find('input[id^=quantiteReserveeUtilisateur]').each(function (index, qteUtilisateur) {
				var article = $(this).parents('[id^=article-]');
				if (qteUtilisateur.value > 0) {
					article.css({ opacity: 1 });
				} else {
					article.css({ opacity: 0.2 });
					article.find('.compteur button').prop('disabled', true);
				}
			});
		});

		mesActions.find('#tous-les-articles').on('click', function (event) {
			event.preventDefault();
			callbackRefresch(defauts['liste'], context);
			$(idListe).find('input[id^=quantiteReserveeUtilisateur]').each(function (index, item) {
				var article = $(this).parents('[id^=article-]');
				article.css({ opacity: 1 });
				article.find('.compteur button').prop('disabled', false);
			});
		});
  };

})(jQuery);