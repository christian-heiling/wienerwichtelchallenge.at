jQuery(function ($) {
    $('.ajax').click(function() {
        var button = $(this);
        button.html('Loading ...');
        
        $.get(button.data('url'), function() {
            button.html('Success');
        }).fail(function() {
            button.html('Fail')
        });
    });
})
