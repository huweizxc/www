window.onload = function() {
		
	// 详情图片触屏滑动效果
	var swiper = new Swiper('.swiper-container', {
			loop: true,
			pagination: '.swiper-pagination',
			paginationType: 'fraction',
			paginationClickable: true,
			preloadImages: false,
			lazyLoading: true
	});
	var WinWidth,touchRatio;

	function GetWinWidth(){
		WinWidth = $(window).width();
		touchRatio = WinWidth*0.72;
		$('.swiper-slide,.swiper-wrapper').height(touchRatio);
	}
	GetWinWidth();
	$(window).resize(function() {
		GetWinWidth();
	});

	// 数量累减，数量为1 .minus 状态为灰色不可用
	$('.num_wrap .minus').on('click', function(){
		var buy_num = $(this).next(),
			buy_num_val = parseInt(buy_num.val());
		if(buy_num_val > 1){
			buy_num.val(buy_num_val-1);
		};
		// 数量为1时 .minus 状态为灰色不可用
		if(parseInt(buy_num.val()) == 1){
			$(this).addClass('minus_disabled');
		}else{
			$(this).removeClass('minus_disabled');
		}
	});
	// 数量累加
	$('.num_wrap .plus').on('click', function(){
		var buy_num = $(this).prev(),
			buy_num_val = parseInt(buy_num.val());

		buy_num.val(buy_num_val+1);
		if($(this).parent().find('.minus').hasClass('minus_disabled')){
			$(this).parent().find('.minus').removeClass('minus_disabled');
		}
	})


	$('.href').click(function(){
		var url = $(this).attr('data-href');
		window.location.href = url;
	});
}