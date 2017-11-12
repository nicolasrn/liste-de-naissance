; (function ($) {
	"use-strict";

	Handlebars.registerHelper('eq', function (a, b, options) {
		return a === b ? options.fn(this) : options.inverse(this);
	});

	Handlebars.registerHelper('ifReservationPossible', function (a, b, options) {
		if (a - b > 0) {
			return options.fn(this);
		}
		return options.inverse(this);
	});

	Handlebars.registerHelper("math", function (lvalue, operator, rvalue, options) {
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

	Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
		switch (operator) {
			case '==':
				return (v1 == v2) ? options.fn(this) : options.inverse(this);
			case '===':
				return (v1 === v2) ? options.fn(this) : options.inverse(this);
			case '!=':
				return (v1 != v2) ? options.fn(this) : options.inverse(this);
			case '!==':
				return (v1 !== v2) ? options.fn(this) : options.inverse(this);
			case '<':
				return (v1 < v2) ? options.fn(this) : options.inverse(this);
			case '<=':
				return (v1 <= v2) ? options.fn(this) : options.inverse(this);
			case '>':
				return (v1 > v2) ? options.fn(this) : options.inverse(this);
			case '>=':
				return (v1 >= v2) ? options.fn(this) : options.inverse(this);
			case '&&':
				return (v1 && v2) ? options.fn(this) : options.inverse(this);
			case '||':
				return (v1 || v2) ? options.fn(this) : options.inverse(this);
			default:
				return options.inverse(this);
		}
	});

	Handlebars.registerHelper("ifmod", function (index, modulo, resultat, delta, options) {
		var index = parseFloat(index);
		var modulo = parseFloat(modulo);
		var resultat = parseFloat(resultat) + parseFloat(delta);

		var calcul = index % modulo;

		var resultatComparaison = calcul == resultat;

		return resultatComparaison ? options.fn(this) : options.inverse(this);
	});

	Handlebars.registerHelper('eachWithParent', function (context, parentId, options) {
		if (!options) {
			throw new Exception('Must pass iterator to #eachWithParent');
		}

		var fn = options.fn, inverse = options.inverse;
		var i = 0, ret = "", data;

		if (options.data) {
			data = Handlebars.createFrame(options.data);
		}

		if (context && typeof context === 'object') {
			if (Handlebars.Utils.isArray(context)) {
				for (var j = context.length; i < j; i++) {
					if (data) {
						data.index = i;
						data.first = (i === 0);
						data.last = (i === (context.length - 1));
						data.parentId = parentId;
					}
					ret = ret + fn(context[i], { data: data });
				}
			} else {
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

		if (i === 0) {
			ret = inverse(this);
		}

		return ret;
	});
})(jQuery);