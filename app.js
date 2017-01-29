;(function(jQuery) {
	"use-strict";

	var loginAppSimple = Vue.extend({
		name: 'loginAppSimple',
		data: function() {
			return {
				idLoggedIn: false,
				user: null
			};
		},
		methods: {
			authentification: function(form, event) {
				if (event) {
					event.preventDefault();
				}
				var form = $('div#form-authentification form');
				var login = form.find('#login').val();
				var mdp = form.find('#mdp').val();

				var callback = this.callback;
				
				$.ajax({
					url: "api/authentification", 
					method: 'GET',
					data : {
						'login': login,
						'password': mdp
					},
					success : function(data) {
						if (data && data != "null") {
							$.data(document, "user", data);
							callback(data);
							router.push('/liste-de-naissance');
						} else {
							console.log("login ou mot de passe incorrecte");
						}
					}
				});
			}, callback: function(data) {
				this.idLoggedIn = true;
				this.user = JSON.parse(data)['login'];
			}
		},
		template: `
			<div id="form-authentification">
				<form v-if="!idLoggedIn" class="navbar-form navbar-right" v-on:submit.prevent="authentification(this)">
					<div class="form-group">
						<input type="text" placeholder="login" id="login" name="login" class="form-control">
					</div>
					<div class="form-group">
						<input type="password" placeholder="Mot de passe" id="mdp" name="password" class="form-control">
					</div>
					<button type="submit" class="btn btn-success">Se connecter</button>
				</form>
				<h1 v-else class="navbar-right navbar-brand nomargin">Bonjour, {{user}}</h1>
			</div>
		`
	});

	var loginApp = Vue.extend({
		name: 'loginApp',
		methods: {
			authentification: function(form, event) {
				if (event) {
					event.preventDefault();
				}
				var form = $('form[name=form-authentification]');
				var login = form.find('#login').val();
				var mdp = form.find('#mdp').val();
				
				$.ajax({
					url: "api/authentification", 
					method: 'GET',
					data : {
						'login': login,
						'password': mdp
					},
					success : function(data) {
						if (data && data != "null") {
							$.data(document, "user", data);
							router.push('/liste-de-naissance');
						} else {
							console.log("login ou mot de passe incorrecte");
						}
					}
				});
			}
		},
		template: `
		<form name="form-authentification" method="get" v-on:submit.prevent="authentification(this)" >
			<div class="form-group row">
				<div class="col-md-10">
					<label class="col-md-2 col-form-label" for="login">login</label> 
					<div class="col-md-10">
						<input type="text" class="form-control" id="login" name="login" />
					</div>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-md-10">
					<label class="col-md-2 col-form-label" for="mdp">mot de passe</label>
					<div class="col-md-10">
						<input type="password" class="form-control" id="mdp" name="password"/>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<div class="offset-md-2 col-md-10">
					<button type="submit" class="btn btn-primary">se connecter</button>
				</div>
			</div>
		</form>
		`
	});

	var displayItemApp = Vue.extend({
		name: 'displayItemApp',
		props: {
			item: {
				required: true
			}
		},
		template: `
			<div class="col-sm-6 col-md-4">
				<div class="thumbnail">
					<img :src="item.img"/>
					<div class="caption">
						<h2>{{item.libelle}}</h2>
						<div>quantité : <span>{{item.quantiteSouhaite}}</span></div>
						<div>quantité réservé: <span>{{item.quantiteReserve}}</span></div>
					</div>
				</div>
			</div>
		`
	});

	var grilleCadeauApp = Vue.extend ({
		name: 'grilleCadeauApp',
		components: {
			'display-item': displayItemApp
		},
		data: function() {
			return {
				cadeaux: []
			};
		},
		mounted: function() {
			this.cadeaux = [
				{
					libelle: 'biberon',
					quantiteSouhaite: 2,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'bavoir',
					quantiteSouhaite: 6,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'sucette',
					quantiteSouhaite: 1,
					quantiteReserve: 1,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'bavoir',
					quantiteSouhaite: 6,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'sucette',
					quantiteSouhaite: 1,
					quantiteReserve: 1,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'bavoir',
					quantiteSouhaite: 6,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'sucette',
					quantiteSouhaite: 1,
					quantiteReserve: 1,
					img: 'http://lorempixel.com/500/300/'
				}
			]
		},
		template: `
			<div>
				<div class="container-fluid">
					<div class="row">
						<display-item :item="cadeau" v-for="cadeau in cadeaux"></display-item>
					</div>
				</div>
			</div>`
	});

	var listeCadeauxApp = Vue.extend({
		name: 'listeCadeauxApp',
		components: {
			'login': loginApp,
			'display-cadeaux': grilleCadeauApp
		},
		mounted: function() {
			var data = $.data(document, "user");
			if (!data) {
				router.push('/');
			}
		},
		// extension options
		template: `
			<div>
				<div class="jumbotron">
					<div class="container">
						<h1>Pour bébé</h1>
						<p>
							Voici quelques cadeaux que nous souhaiterions pour le bébé, si vous avez d'autres idées, vous pouvez les rajouter ;)
						</p>
					</div>
				</div>
				<display-cadeaux></display-cadeaux>
			</div>
		`
	});

	var BienvenueApp = Vue.extend({
		name: 'BienvenueApp',
		template:`
			<div class="jumbotron">
				<div class="container">
					<h1>Bonjour</h1>
					<p>
						Pour accéder à la liste de naissance, merci de vous authentifier
					</p>
				</div>
			</div>
		`
	});

	const routes = [
	  { path: '/', component: BienvenueApp },
	  { path: '/liste-de-naissance', component: listeCadeauxApp }
	];

	const router = new VueRouter({
		'routes': routes
	});

	// create a root instance
	var appPrincipale = new Vue({
		el: '#app',
		data: {
		},
		components: {
			'bienvenueapp': BienvenueApp,
			'cadeaux': listeCadeauxApp
		},
		router: router,
		methods: {
			
		}
	});

	// create a root instance
	var appLogin = new Vue({
		el: '#simpleLoginApp',
		components: {
			'loginSimple': loginAppSimple
		},
		methods: {
			
		},
		template: `<loginSimple></loginSimple>`
	});
})($);
