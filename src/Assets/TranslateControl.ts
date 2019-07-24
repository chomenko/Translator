if (module.hot) {
	module.hot.accept();
}

import {App, BaseComponent, SAGA_REDRAW_SNIPPET, Saga} from "Stage"

class TranslateControl extends BaseComponent {

	initial() {
		super.initial();
		this.installPlugins();
	}

	@Saga(SAGA_REDRAW_SNIPPET)
	public installPlugins(action = null){
		let target = document;
		if (action) {
			const {content} = action.payload;
			target = content
		}

		let enable = false;
		const modal = $(target).find('#translate-modal');
		$(target).keydown(function (event) {
			if (event.ctrlKey) {
				if (event.altKey) {
					if (!enable) {
						enable = true;
						$('.translate-item').addClass('active');
						$('[data-toggle="tooltip"]').tooltip("toggle");
					}
				}
			}
		});
		$(target).find('body').on('click', '.translate-item.active', function(event) {
			event.preventDefault();
			event.stopPropagation();
			var name = $(this).attr('data-trans-name');
			var file = $(this).attr('data-trans-file');
			var url = modal.attr('data-link');
			$.ajax({
				url: url,
				data: {name: name, file: file},
				success: function(data){
					var form = modal.find("form");
					form.find('.trans-name').text(name);
					form.find('input[name="name"]').val(name);
					form.find('input[name="file"]').val(file);
					form.find('textarea[name="translate"]').val(data.translate);
					modal.modal("show")
				}
			});
		})
		$(target).keyup(function(event) {
			if(enable) {
				$('.translate-item').removeClass('active');
				$('[data-toggle="tooltip"]').tooltip("hide");
				enable = false;
			}
		});

	}



}

App.addComponent("TranslateControl", TranslateControl);
