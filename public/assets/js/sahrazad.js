$(function() {
    $('.listing tbody tr').on('mouseenter', function() {
        $(this).addClass('hover');
    }).on('mouseleave', function() {
        $(this).removeClass('hover');
    }).on('click', function() {
        window.location.replace($(this).data('action'));
    });
});