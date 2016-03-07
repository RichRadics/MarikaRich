$('img').each(function() {
    var deg = $(this).data('rotate') || 0;
    var rotate = 'rotate(' + $(this).data('rotate') + 'deg)';
    $(this).css({ 
        '-webkit-transform': rotate,
        '-moz-transform': rotate,
        '-o-transform': rotate,
        '-ms-transform': rotate,
        'transform': rotate 
    });
});

$(document).ready(function(){
	var language='english';
	change_lang(language);
    
    $(function(){
        $('.fadein img:gt(0)').hide();
        setInterval(function(){
          $('.fadein :first-child').fadeOut()
             .next('img').fadeIn()
             .end().appendTo('.fadein');}, 
          3000);
    });

    $(function() {
        var widthRatio = $('#map-div').width() / $(window).width();
        $(window).resize(function() {
            $('#map-div').css({width: $(window).width() * 0.9});
        }); 
        var heightRatio = $('#map-section').height()*0.75;
        $(window).resize(function() {
            $('#map-div').css({height: heightRatio});
        }); 
    });
    // do it now too...
    $(function(){
        $(window).trigger('resize');
    });
});
change_lang = function(lang) {
    var language = lang;
    $.ajax({
        url: '/languages/languages.xml',
        success: function(xml) {
            $(xml).find('translation').each(function(){
                var id = $(this).attr('id');
                var text = $(this).find(language).html();
                $("." + id).html(text);
            });
        }
    });
};
