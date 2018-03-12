jQuery(document).ready(function() {

	/*
		Fullscreen background
	*/
	$.backstretch("assets/img/backgrounds/1.jpg");
	/*
		Form validation
	*/
	$('.login-form input[type="text"], .login-form input[type="password"], .login-form textarea').on('focus', function() {
		$(this).removeClass('input-error');
	});
	
	$('.login-form').on('submit', function(e) {
		
		$(this).find('input[type="text"], input[type="password"], textarea').each(function(){
			if( $(this).val() === "" ) {
				e.preventDefault();
				$(this).addClass('input-error');
			}
			else {
				$(this).removeClass('input-error');
			}
		});
	});

	$('[name=language]').click(function() {
		Cookies.set('language', $(this).val());
	});

	if(!Cookies.set('language') || Cookies.set('language') == 'en') {
		$('#en').prop('checked', true);
		Cookies.set('language', 'en');
	} else if(Cookies.set('language') == 'ja') {
		$('#ja').prop('checked', true);
	}
});
