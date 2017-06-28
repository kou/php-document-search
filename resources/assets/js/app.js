
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import $ from 'jquery';
window.$ = window.jQuery = $;
import 'jquery-ui/ui/widgets/autocomplete.js';

$('#query').autocomplete({
    source: function(request, response) {
	$.ajax({
	    url: "/terms/",
	    dataType: "json",
	    data: {
		query: this.term
	    },
	    success: function(data) {
		console.log(data);
		response(data);
	    },
	    error: function(xhr, status, error) {
		console.log("error");
		console.log(status);
		console.log(error);
	    }
	})
    }
}).autocomplete("instance")._renderItem = function(ul, item) {
    return $("<li>")
	.attr("data-value", item.value)
	.append(item.label)
	.appendTo(ul);
};


window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: '#app'
});
