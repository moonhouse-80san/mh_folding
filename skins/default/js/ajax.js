jQuery(document).ready(function($) {
	$(document).on('click', '.mf_w a:not(".mf_box a")', function(e) {
		e.preventDefault();
		var href = $(this).attr('href');
		$.ajax({
			url: href,
			dataType: 'html',
			success: function(data) {
				var selectedContent = $(data).find('.mf_w').html();
				$('.mf_w').html(selectedContent);
			}
		});
	});
});