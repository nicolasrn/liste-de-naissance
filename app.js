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
			this.bindEvents();

			var self = this;

			this.router = new Router({
				'/': function (filter) {
					self.renderHome();
				}.bind(this),
				'/liste-de-naissance': function (filter) {
					self.renderListeDeNaissance();
				}.bind(this),
				'/enregistrement': function(filter) {
					self.renderEnregistrement();
				}.bind(this)
			});
		},
		isLogged: function(callbackSuccess, callbackError) {
			$.ajax({
				url: "/web/services/RestController.php?model=login&action=isAuthentificate", 
				method: 'GET',
				success : function(data) {
					return callbackSuccess(data);
				}, 
				error: function() {
					return callbackError();
				}
			});
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
			
			if (password !== confirmationPassword) {
				$(form.password).parents('div.form-group').addClass('has-error');
				$(form.password2).parents('div.form-group').addClass('has-error');
			} else {
				$.ajax({
					url: "/web/services/RestController.php?model=login&action=enregistrement", 
					method: 'POST',
					data : {
						'login': login,
						'password': password
					},
					success : function(data) {
						self.router.setRoute('/');
					},
					error: function(jqXHR, textStatus, errorThrown ) {
						console.log(jqXHR, textStatus, errorThrown);
					}
				});
			}
		},
		bindEvents: function () {
			$('#navbar').on('submit', 'form#form-authentification', this.authentificate.bind(this));
			$('#app').on('submit', 'form#enregistrement', this.enregistrement.bind(this));
		},
		renderHome: function() {
			var self = this;
			//self.isLogged(function(data) {
			//	self.router.setRoute('/liste-de-naissance');
			//}, function() {
				self.renderLogin();
				self.renderBienvenue();
			//});
		},
		renderListeDeNaissance: function () {
			var self = this;
			//self.isLogged(function(data) {
			//self.user = data;
			//if (data && data != "null") {
				if (self.user) {
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
			/*} else {
				self.renderLogin({
					erreurAuthentification: true
				});
			}
			}, function() {
				self.router.setRoute('/');
			})*/;
		},
		renderEnregistrement: function () {
			$('#app').html(this.enregistrementTemplate());
			$('#navbar').empty();
		},
		renderLogin: function (options) {
			$('#navbar').html(this.formLoginTemplate(options));
		},
		renderBienvenue: function () {
			$('#app').html(this.bienvenueTemplate());
		}
	};

	App.init();
	App.router.init('/');
})(jQuery);
