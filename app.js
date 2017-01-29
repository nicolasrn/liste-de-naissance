;(function(jQuery) {
	"use-strict";

	var loginAppSimple = Vue.extend({
		name: 'loginAppSimple',
		components: {
			'liste-cadeaux': listeCadeauxApp
		},
		data: function() {
			return {
				idLoggedIn: false,
				login: null, 
				mdp: null
			};
		},
		methods: {
			authentification: function(form, event) {
				if (event) {
					event.preventDefault();
				}

				$.ajax({
					url: "api/authentification", 
					method: 'GET',
					callback: this.callback,
					data : {
						'login': this.login,
						'password': this.mdp
					},
					success : function(data) {
						if (data && data != "null") {
							$.data(document, "user", data);
							this.callback(data);
							router.push('/liste-de-naissance');
						} else {
							console.log("login ou mot de passe incorrecte");
						}
					}
				});
			}, callback: function(data) {
				this.idLoggedIn = true
				this.login = JSON.parse(data)['login'];
			}
		},
		template: `
			<div id="form-authentification">
				<form v-if="!idLoggedIn" class="navbar-form navbar-right" v-on:submit.prevent="authentification(this)">
					<div class="form-group">
						<input type="text" placeholder="login" id="login" name="login" class="form-control" v-model="login">
					</div>
					<div class="form-group">
						<input type="password" placeholder="Mot de passe" id="mdp" name="password" class="form-control" v-model="mdp">
					</div>
					<button type="submit" class="btn btn-success">Se connecter</button>
				</form>
				<h1 v-else class="navbar-right navbar-brand nomargin">Bonjour, {{login}}</h1>
			</div>
		`
	});

	var counterApp = Vue.extend({
		name: 'counterApp',
		data: function() {
			return {
				valeur: 0
			};
		},
		props: {
			max: {
				required:true
			}
		},
		methods: {
			increment: function() {
				if (this.valeur < this.max) {
					this.valeur++;
				}
			},
			decrement: function() {
				if (this.valeur > 0) {
					this.valeur--;
				}
			}
		},
		template:`
			<div>
				<span>{{valeur}}</span>
				<button v-on:click="decrement">-</button> 
				<button v-on:click="increment">+</button>
			</div>
		`
	});

	var displayItemApp = Vue.extend({
		name: 'displayItemApp',
		components: {
			'counter': counterApp
		},
		props: {
			item: {
				required: true
			},
			index: {
				required: true
			}
		},
		template: `
			<div class="col-md-4">
				<div class="thumbnail">
					<img :src="item.img"/>
					<div class="caption">
						<h2>{{item.libelle}}</h2>
						<div><span>{{item.quantiteSouhaite}}</span> ça serait bien</div>
						<div>déjà <span>{{item.quantiteReserve}}</span> réserve(s)</div>
						<div v-if="item.quantiteSouhaite == item.quantiteReserve">le nécessaire est réservé</div>
						<counter v-else :max="item.quantiteSouhaite - item.quantiteReserve"></counter>
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
					libelle: 'biberon 1',
					quantiteSouhaite: 2,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'bavoir 2',
					quantiteSouhaite: 6,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'sucette 3',
					quantiteSouhaite: 1,
					quantiteReserve: 1,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'bavoir 4',
					quantiteSouhaite: 6,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'sucette 5',
					quantiteSouhaite: 1,
					quantiteReserve: 1,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'bavoir 6',
					quantiteSouhaite: 6,
					quantiteReserve: 2,
					img: 'http://lorempixel.com/500/300/'
				}, {
					libelle: 'sucette 7',
					quantiteSouhaite: 1,
					quantiteReserve: 1,
					img: 'http://lorempixel.com/500/300/'
				}
			]
		},
		methods: {
			getIndex: function(ligne, colonne) {
				return (ligne - 1) * 3 + colonne - 1;
			}
		},
		template: `
				<div class="container-fluid">
					<div class="row" v-for="ligne in Math.ceil(cadeaux.length / 3)">
						<display-item :item="cadeaux[getIndex(ligne, colonne)]" :index="colonne" v-for="colonne in 3" v-if="cadeaux[getIndex(ligne, colonne)]"></display-item>
					</div>
				</div>
		`
	});

	var listeCadeauxApp = Vue.extend({
		name: 'listeCadeauxApp',
		components: {
			'login': loginAppSimple,
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
			'cadeaux': listeCadeauxApp,
			'login-simple': loginAppSimple
		},
		router: router
	});

	// create a root instance
	/*var appLogin = new Vue({
		el: '#simpleLoginApp',
		components: {
			'loginSimple': loginAppSimple
		},
		template: `<loginSimple></loginSimple>`
	});*/
})($);
