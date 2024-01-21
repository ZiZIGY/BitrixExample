BX.namespace('ReactiveListing')
BX.ReactiveListing = {
	navigation: {
		page: 1,
		size: 10,
	},
	init() {
		this.events()
		this.reactiveEvents()
	},
	events() {
		BX.bind(BX('filter'), 'submit', this.handlers.submit)
	},
	reactiveEvents() {
		BX.findChildren(BX.ReactiveListing.ul, { tag: 'input' }, true).forEach(input => {
			const id = input.dataset.id
			BX.bind(input, 'change', () => this.handlers.change(id, input.checked))
		})
		BX.findChildren(BX.ReactiveListing.nav, { tag: 'button' }, true).forEach(button => {
			const type = button.dataset.type
			BX.bind(button, 'click', () => this.handlers.click(type))
		})
	},
	handlers: {
		submit(e) {
			e.preventDefault()
			BX.ajax.runComponentAction('example:reactive-listing', 'changeList', {
				mode: 'class',
				data: new FormData(this),
				signedParameters,
				navigation: BX.ReactiveListing.navigation
			}).then(response => BX.ReactiveListing.render(response.data))
		},
		change(id, checked) {
			BX.ajax.runComponentAction('example:reactive-listing', 'changeStatus', {
				mode: 'class',
				data: {
					id: id,
					complete: checked
				},
				signedParameters
			}).then(response => {
				console.log(response)
			}).then(response => {
				
			})
		},
		click(type) {
			type == 'next'
			? BX.ReactiveListing.navigation.page++
			: BX.ReactiveListing.navigation.page--

			BX.fireEvent(BX('filter'), 'submit')
		}
	},
	render(html) {
		const template = BX.create('template', { html })

		BX.cleanNode(BX.ReactiveListing.ul)
		BX.cleanNode(BX.ReactiveListing.nav)

		BX.adjust(BX.ReactiveListing.ul, { children: [...template.content.querySelectorAll('.listing__item')] })
		BX.adjust(BX.ReactiveListing.nav, { children: [ template.content.querySelector('#navigation ul') ] })

		BX.ReactiveListing.reactiveEvents()
	}
}

BX.ready(() => {
	BX.ReactiveListing.ul = BX('content')
	BX.ReactiveListing.nav = BX('navigation')
	BX.ReactiveListing.init()
})