<template>
  <div class="md">
  	<div class="md-inner">
  		<div class="md-editor">
  			<div class="form-group">
  				<label>Email To</label>
  				<input type="text" class="form-control" v-model="form.email_to">
  			</div>
  			<div class="form-group">
  				<label>Subject</label>
  				<div class="form-lookup">
  					<input type="text" class="form-control" v-model="form.subject">
  					<ul class="form-list">
  						<li v-for="(value, key) in activeSubjectOptions"
  							draggable="true" @dragstart="onDrag(key, $event)">
  							{{value}}
  						</li>
  					</ul>
  				</div>
  			</div>
  			<div class="form-group">
  				<label>Message <small>(MARKDOWN)</small></label>
  				<div class="form-lookup">
  					<textarea class="form-control" v-model="form.message"></textarea>
  					<ul class="form-list">
  						<li v-for="(value, key) in activeMessageOptions"
  							draggable="true" @dragstart="onDrag(key, $event)">
  							{{value}}
  						</li>
  					</ul>
  				</div>

  			</div>
  			<div class="md-btn">
  				<button class="btn btn-success" @click="send">Send</button>
  			</div>
  		</div>
  		<div class="md-preview">
  			<div class="md-subject">{{replacedSubject}}</div>
  			<div class="md-content" v-html="compiledMarkdown"></div>
  		</div>
  	</div>
  </div>
</template>
<script>
	import Vue from 'vue'
	import axios from 'axios'
	import MarkdownIt from 'markdown-it'

	const md = new MarkdownIt({
		linkify: true
	})

  export default {
  	props: {
  		url: String
  	},
  	data() {
  		return {
  			form: {},
  			options: {
  				variables: {
  					subject: {},
  					message: {}
  				},
  				replaceable: {}
  			}
  		}
  	},
  	computed: {
  		activeSubjectOptions() {
  			const active = {}

  			Object.keys(this.options.variables.subject).forEach((key) => {
  				if(this.form.subject.indexOf(key) <= 0) {
  					active[key] = this.options.variables.subject[key]
  				}
  			})

  			return active
  		},
  		activeMessageOptions() {
  			const active = {}

  			Object.keys(this.options.variables.message).forEach((key) => {
  				if(this.form.message.indexOf(key) <= 0) {
  					active[key] = this.options.variables.message[key]
  				}
  			})

  			return active
  		},
  		replacedSubject() {
  			return this.replaceVariables(this.form.subject || '')
  		},
  		compiledMarkdown() {
  			const input = this.replaceVariables(this.form.message || '')
  			return md.render(input)
  		}
  	},
  	mounted() {
  		this.fetch()
  	},
  	methods: {
  		replaceVariables(input) {
  			let lookup = this.options.replaceable || {}
  			let updated = input

  			Object.keys(lookup).forEach((key) => {
  				updated = updated.replace(new RegExp(key, 'g'), lookup[key])
  			})

  			return updated
  		},
  		onDrag(value, e) {
  			e.dataTransfer.setData('text/plain', value)
  		},
  		fetch() {
  			axios.get(this.url)
  				.then((res) => {
  					Vue.set(this.$data, 'form', res.data.form)
  					Vue.set(this.$data, 'options', res.data.options)
  				})
  		},
  		send() {
  			axios.post(this.url, this.form)
  				.then((res) => {
  					alert('Done!')
  				})
  		}
  	}
  }
</script>