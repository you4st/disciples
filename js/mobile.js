$(document).ready(function() {
    $(".more").click(function() {
        var id = $(this).attr('id') + '_detail';

        if ($(this).html() == '더보기') {
            $(this).html('숨기기');
        } else {
            $(this).html('더보기');
        }

        $('#' + id).toggle();
    });
});