jQuery(document).ready(function($){

	if($('#detheme-demo-form').length){
		$('#detheme-demo-form').submit(function(e){
			e.preventDefault();

			if(this.selected_package.value!=''){
				this.submit();
			}

		});

		$('#demo-content a').click(function (e) {
		  e.preventDefault();
		  $(this).tab('show');
		})
	}
});