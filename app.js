;(function() {
	"use-strict";
	var loginApp = Vue.extend({
		name: 'loginApp',
		// extension options
		template: `
		<form action="" method="post">
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
	})

	var listeCadeauxApp = Vue.extend({
		name: 'listeCadeauxApp',
		components: {
			'login': loginApp
		},
		// extension options
		template: `
			<div>cadeaux<login></login></div>
		`
	})

	const routes = [
	  { path: '/', component: loginApp },
	  { path: '/liste-de-naissance', component: listeCadeauxApp }
	]

	const router = new VueRouter({
		'routes': routes
	})

	// create a root instance
	var app = new Vue({
		el: '#app',
		data: {
			isLogin: false
		},
		components: {
			'login': loginApp,
			'cadeaux': listeCadeauxApp
		},
		router: router
	})

})();
