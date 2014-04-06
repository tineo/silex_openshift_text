jQuery(function($){
	$(window).load(function() {
		var $container = $('#wpex-grid-wrap');
		$container.infinitescroll({
			loading: {
				msg: null,
				finishedMsg : null,
				msgText : null,
				msgText: '<div class="infinite-scroll-loader"><i class="icon-spinner icon-spin"></i></div>',
			},
			navSelector  : 'div.infinite-scroll-nav',
			nextSelector : 'div.infinite-scroll-nav div.older-posts a',
			itemSelector : '.loop-entry',
			dataType: 'html'
		}, function( newElements ) {
			var $newElems = $( newElements );
			$newElems.css({ opacity: 0 });
			$newElems.imagesLoaded(function() {
				$newElems.animate({ opacity: 1 }, 'normal');
				$container.isotope( 'appended', $newElems );
				$(".fitvids").fitVids();
			}); // end imagesLoaded()
		});
	});
});