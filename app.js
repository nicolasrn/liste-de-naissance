;(function($) {
	"use-strict";

	Handlebars.registerHelper('eq', function (a, b, options) {
		return a === b ? options.fn(this) : options.inverse(this);
	});

	Handlebars.registerHelper('ifReservationPossible', function(a, b, options) {
		if(a - b > 0) {
			return options.fn(this);
		}
		return options.inverse(this);
	});

	Handlebars.registerHelper("math", function(lvalue, operator, rvalue, options) {
		var lvalue = parseFloat(lvalue);
		var rvalue = parseFloat(rvalue);

		return {
			"+": lvalue + rvalue,
			"-": lvalue - rvalue,
			"*": lvalue * rvalue,
			"/": lvalue / rvalue,
			"%": lvalue % rvalue
		}[operator];
	});

	Handlebars.registerHelper("ifmod", function(index, modulo, resultat, delta, options) {
		var index = parseFloat(index);
		var modulo = parseFloat(modulo);
		var resultat = parseFloat(resultat) + parseFloat(delta);

		var calcul = index % modulo;

		var resultatComparaison = calcul == resultat;

		return resultatComparaison ? options.fn(this) : options.inverse(this);
	});

	Handlebars.registerHelper('eachWithParent', function(context, parentId, options) {
		if (!options) {
			throw new Exception('Must pass iterator to #eachWithParent');
		}

		var fn = options.fn, inverse = options.inverse;
		var i = 0, ret = "", data;

		if (options.data) {
			data = Handlebars.createFrame(options.data);
		}

		if(context && typeof context === 'object') {
			if (Handlebars.Utils.isArray(context)) {
				for(var j = context.length; i<j; i++) {
					if (data) {
						data.index = i;
						data.first = (i === 0);
						data.last  = (i === (context.length-1));
						data.parentId = parentId;
					}
					ret = ret + fn(context[i], { data: data });
				}
			} else  {
				for (var property in context) {
					if (context.hasOwnProperty(property)) {
						if (data) {
							data.index = i++;
							data.indexProperty = property;
							data.first = !data.first ? property : data.first;
							data.parentId = parentId;
						}
						ret = ret + fn(context[property], { data: data });
					}
				}
			}
		}

		if(i === 0){
			ret = inverse(this);
		}

		return ret;
	});

	var util = {
		uuid: function () {
			/*jshint bitwise:false */
			var i, random;
			var uuid = '';

			for (i = 0; i < 32; i++) {
				random = Math.random() * 16 | 0;
				if (i === 8 || i === 12 || i === 16 || i === 20) {
					uuid += '-';
				}
				uuid += (i === 12 ? 4 : (i === 16 ? (random & 3 | 8) : random)).toString(16);
			}

			return uuid;
		}
	};

	var App = {
		init: function () {
			this.formLoginTemplate = Handlebars.compile($('#login-template').html());
			this.bienvenueTemplate = Handlebars.compile($('#bienvenue-template').html());
			this.listeNaissanceTemplate = Handlebars.compile($('#app-template').html());
			this.enregistrementTemplate = Handlebars.compile($('#enregistrement-template').html());
			this.ajoutArticleTemplate = Handlebars.compile($('#ajoutArticle-template').html());
			this.detailReservationArticleListeDeNaissance = Handlebars.compile($('#detailReservation').html());
			this.detailPersonnes = Handlebars.compile($('#detailPersonnes').html());
			this.bindEvents();

			var self = this;

			this.router = new Router({
				'/': function () {
					self.renderHome();
				}.bind(this),
				'/liste-de-naissance': function () {
					self.renderListeDeNaissance();
				}.bind(this),
				'/liste-de-naissance/edit/:id': function(id) {
					self.renderEdit(id);
				}.bind(this),
				'/liste-de-naissance/edit': function() {
					self.renderEdit(null);
				}.bind(this),
				'/liste-de-naissance/detail': function() {
					self.renderDetailReservationArticleListeDeNaissance();
				}.bind(this),
				'/enregistrement': function() {
					self.renderEnregistrement();
				}.bind(this),
				'/personnes': function() {
					self.renderPersonnes();
				}.bind(this),
				'/deconnexion': function() {
					self.deconnexion();
				}.bind(this)
			});
		},
		deconnexion: function() {
			Cookies.remove('ldn-user');
			this.renderHome();
			this.router.setRoute('/');
		}, 
		isLogged: function() {
			var cookie = Cookies.get('ldn-user');
			this.user = cookie && cookie !== undefined ? JSON.parse(cookie) : null;
			return this.user != null;
		},
		authentificate: function(event) {
			event.preventDefault();
			self = this;
			$.ajax({
				url: "/web/services/RestController.php?model=login", 
				method: 'POST',
				data : {
					'login': event.target.login.value,
					'password': event.target.password.value
				},
				success : function(data) {
					self.user = data;
					if (data && data != "null") {
						self.router.setRoute('/liste-de-naissance');
						Cookies.set('ldn-user', self.user);
						$.get('/web/menuAdmin.php', null, function(data) {
							$('#menuAdmin').html(data);
						});
					}
				},
				error: function(jqXHR, textStatus, errorThrown ) {
					self.renderLogin({
						erreurAuthentification: true
					});
					$('[data-toggle="login-erreur"]').popover();
					self.renderBienvenue();
				}
			});
		},
		getListe: function(idUser, callbackSuccess, callbackError) {
			$.ajax({
				url: "/web/services/RestController.php", 
				data: {
					model: 'articles',
					action: 'getAll',
					idUser: idUser
				},
				method: 'GET',
				success : function(data) {
					return callbackSuccess(data);
				}, 
				error: function(jqXHR, textStatus, errorThrown) {
					return callbackError(jqXHR, textStatus, errorThrown);
				}
			});
		},
		enregistrement: function(event) {
			event.preventDefault();
			var self = this;
			var form = event.target;
			var login = form.login.value;
			var password = form.password.value;
			var confirmationPassword = form.password2.value;
			var isErreur = false;
			
			$(form).find('.has-error').removeClass('has-error');
			$(form).find('span.help-block').empty();

			if (password == '' || confirmationPassword == '') {
				if (password == '') {
					$(form.password).parents('div.form-group').addClass('has-error');
					$(form.password).parent().find('span.help-block').html('les deux mots de passe ne peuvent être vide');
				} 
				if (confirmationPassword == '') {
					$(form.password2).parents('div.form-group').addClass('has-error');
					$(form.password2).parent().find('span.help-block').html('les deux mots de passe ne peuvent être vide');
				}
				isErreur = true;
			} else if (password !== confirmationPassword) {
				$(form.password).parents('div.form-group').addClass('has-error');
				$(form.password2).parents('div.form-group').addClass('has-error');
				$(form.password).parent().find('span.help-block').html('les deux mots de passe sont différents');
				$(form.password2).parent().find('span.help-block').html('les deux mots de passe sont différents');
				isErreur = true;
			}

			if (login === '') {
				var loginField = $(form).find('input[name=login]');
				loginField.parents('.form-group').addClass('has-error');
				loginField.parent().find('span.help-block').html('le login ne peut être vide');
				isErreur = true;
			}

			if (!isErreur) {
				$.ajax({
					url: "/web/services/RestController.php?model=login&action=enregistrement", 
					method: 'POST',
					data : {
						'login': login,
						'password': password,
						'password2': confirmationPassword
					},
					success : function(data) {
						self.router.setRoute('/');
					},
					error: function(jqXHR, textStatus, errorThrown ) {
						var loginField = $(form).find('input[name=login]');
						loginField.parents('.form-group').addClass('has-error');
						loginField.parent().find('span.help-block').html(jqXHR.responseText);
					}
				});
			}
		},
		ajoutArticle: function(event) {
			event.preventDefault();

			var quantiteSouhaitee = event.target.quantiteSouhaitee.value;
			var libelle = event.target.libelle.value;
			var erreur = false;

			var spanHelp = $(event.target).find('#resultatAjoutArticle span.help-block');
			var spanHelpParent = $(event.target).find('#resultatAjoutArticle');
			spanHelp.empty();
			spanHelpParent.removeClass('has-error');
			spanHelpParent.removeClass('has-success');

			if (libelle == '') {
				spanHelp.html(spanHelp.html() + " le libellé ne doit pas être vide"); 
				spanHelpParent.addClass('has-error');
				erreur = true;
			}
			if (quantiteSouhaitee == 0) {
				spanHelp.html(spanHelp.html() + "<br/>attention la quantite souhaitée est nulle");
				spanHelpParent.addClass('has-error');
				erreur = true;
			}

			if (!erreur) {
				$.ajax({
					url: "/web/services/RestController.php",
					data: new FormData(event.target),
					method: 'POST',
					cache: false,
					processData: false,
					contentType: false,
					success: function (data) {
						if (event.target.action == 'addArticle') {
							var message = function(messageList) {
								if (messageList) {
									var messages = $.map(messageList, function(valeur, index) {
										return '<li>' + valeur + '</li>';
									});
									return '<ul>' + messages.join('') + '</ul>';
								}
								return "";
							}(data['message']);
							$('#resultatAjoutArticle span.help-block').html("l'article a bien été inséré : " + message);
							$('#resultatAjoutArticle').addClass('has-success');
							event.target.reset();
						} else {
							console.log('update');
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						var res = JSON.parse(jqXHR.responseText)['message'];

						$('#resultatAjoutArticle span.help-block').html(res);
						$('#resultatAjoutArticle').addClass('has-error');
					}
				});
			}
		},
		recupererDroitAdmin: function(callbackSuccess, callbackError) {
			$.ajax('/web/services/RestController.php', {
				method: 'GET',
				data : {
					model: 'login',
					action: 'recupererDroitAdmin'
				}
			}).success(function(data) {
				callbackSuccess();
			}).error(function() {
				callbackError();
			});
		},
		bindEvents: function () {
			$('#app').on('submit', 'form#form-authentification', this.authentificate.bind(this));
			$('#app').on('submit', 'form#enregistrement', this.enregistrement.bind(this));
			$('#app').on('submit', 'form#ajoutArticle', this.ajoutArticle.bind(this));
			$('#app').on('reset', 'form#ajoutArticle', function(event) {
				event.target.reset();
				$('.list-image').empty();
				$('#app').find('#ajouterImage').addFileToForm('index', 0);
			}.bind(this));
		},
		renderHome: function() {
			var self = this;
			if (!self.isLogged()) {
				self.renderLogin();
				self.renderBienvenue();
			} else {
				self.renderListeDeNaissance();
			}
		},
		renderPersonnes: function() {
			var self = this;
			if (this.isLogged()) {
				this.renderLogin({
					login: self.user['user']['login']
				});
				this.recupererDroitAdmin(function() {
					$.ajax("/web/services/RestController.php", {
						data: {
							model: 'articles',
							action: 'articleReserve'
						},
						method: 'GET'
					}).success(function(data) {
						$.ajax("/web/services/RestController.php", {
							data: {
								model: 'login',
								action: 'personnes'
							},
							method: 'GET'
						}).success(function(data) {
							$('#app').html(self.detailPersonnes(data));
						}).error(function(jqXHR, textStatus, errorThrown) {
							console.log(arguments);
						});
						$('#app').html(self.detailPersonnes(data));
					}).error(function(jqXHR, textStatus, errorThrown) {
						console.log(arguments);
					});
				}, function() {
					self.router.setRoute('/');
				});
			} else {
				this.router.setRoute('/');
			}
		},
		renderListeDeNaissance: function () {
			var self = this;
			if (self.isLogged()) {
				self.renderLogin({
					login: self.user['user']['login']
				});
				self.getListe(self.user['user']['id'], function(data) {
					self.cadeaux = data;
					$('#app').html(self.listeNaissanceTemplate({
						cadeaux: self.cadeaux,
						nbColonneMax: 12,
						nbColonneAffichees:3
					}));
					$('#app').find('.compteur').compteur({idUser: self.user['user']['id']});
					$('#app').find('.carousel').carousel();
				}, function(jqXHR, textStatus, errorThrown) {
					console.log(jqXHR, textStatus, errorThrown);
				});
			} else {
				self.router.setRoute('/');
			}
		},
		renderDetailReservationArticleListeDeNaissance: function() {
			var self = this;
			if (this.isLogged()) {
				this.renderLogin({
					login: self.user['user']['login']
				});
				this.recupererDroitAdmin(function() {
					$.ajax("/web/services/RestController.php", {
						data: {
							model: 'articles',
							action: 'articleReserve'
						},
						method: 'GET'
					}).success(function(data) {
						$('#app').html(self.detailReservationArticleListeDeNaissance(data));
						$('table.table').DataTable();
					}).error(function(jqXHR, textStatus, errorThrown) {
						console.log(arguments);
					});
				}, function() {
					self.router.setRoute('/');
				});
			} else {
				this.router.setRoute('/');
			}
		},
		renderEdit: function(id) {
			var self = this;
			if (self.isLogged()) {
				this.recupererDroitAdmin(function() {
					self.renderLogin({
						login: self.user['user']['login']
					});
					self.renderFormArticle(id);
				}, function() {
					self.router.setRoute('/');
				});
			} else {
				this.router.setRoute('/');
			}
		},
		renderFormArticle: function(id) {
			var self = this;
			if (id && id !== undefined && id != null) {
				$.ajax({
					url: "/web/services/RestController.php", 
					data: {
						model: 'articles',
						action: 'get',
						id: id
					},
					method: 'GET',
					success : function(data) {
						$('#app').html(self.ajoutArticleTemplate(data));
						$('#app').find('.compteur').compteur({idUser: self.user['user']['id'], isEditMode: true});
						$('#app').find('#ajouterImage').addFileToForm(data);
					}, 
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(jqXHR, textStatus, errorThrown);
					}
				});
			} else {
				$('#app').html(this.ajoutArticleTemplate({quantiteSouhaitee: 0, creation: true}));
				$('#app').find('.compteur').compteur({idUser: this.user['user']['id'], isEditMode: true});
				$('#app').find('#ajouterImage').addFileToForm();
			}
		},
		renderEnregistrement: function () {
			$('#app').html(this.enregistrementTemplate());
		},
		renderLogin: function (options) {
			$('#navbar-content').html(this.formLoginTemplate(options));
		},
		renderBienvenue: function () {
			$('#app').html(this.bienvenueTemplate());
		}
	};

	App.init();
	App.router.init('/');
	
	$.get('/web/menuAdmin.php', null, function(data) {
		$('#menuAdmin').html(data);
	});
})(jQuery);
