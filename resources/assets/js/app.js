import Vue from 'vue'
import ComposeEmail from './components/ComposeEmail.vue'

const app = new Vue({
	el: '#root',
	components: { ComposeEmail },
	data() {
		return {
			url: '/api/email/create'
		}
	},
	template: `
		<compose-email :url="url"></compose-email>
	`
})