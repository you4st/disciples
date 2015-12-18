$(document).ready(function() {
    $(".more").click(function() {
        var id = 'detail_' + $(this).attr('id');

        if ($(this).html() == '더보기') {
            $(this).html('숨기기');
        } else {
            $(this).html('더보기');
        }

        $('#' + id).toggle("slow");
    });

    $(".search").click(function() {
        $(".error").hide();
        var data = {name: $.trim($("#keyword").val()), list: 1};

        if (data.name.length > 0) {
            $.post('/disciples/ajax/search-member/', data, function (response) {
                if (response.success) {
                    if (response.searchResult.length > 0) {
                        showRows(response.searchResult);
                        $('.show-all').show("slow");
                    } else {
                        showAll();
                        $(".error").show();
                    }
                }
            }, "json");
        } else {
            showAll();
            $(".error").show();
        }
    });

    $(".show-all").click(function() {
        $(".error").hide();
        showAll();
    });

    function showAll() {
        $("#list").children().find('tr').hide();
        $("#list").children().find('.header').show();
        $("#list").children().find('tr').each(function() {
            if (typeof $(this).attr('id') != 'undefined') {
                var id_str = $(this).attr('id').split('_');

                if (id_str[0] == 'row') {
                    $(this).show("slow");
                    $(this).children().find('.more').html('더보기');
                }
            } else {
                $(this).show();
            }
        });
        $(".show-all").hide();
    }

    function hideAll() {
        $("#list").children().find('tr').hide();
    }

    function showRows(list) {
        // show all rows first
        hideAll();

        $("#list").children().find('.header').show();
        $("#list").children().find('tr').each(function() {
            if (typeof $(this).attr('id') != 'undefined') {
                var id_str = $(this).attr('id').split('_');
                var id = id_str[1];

                if ($.inArray(id, list) != -1) {
                    $(this).show("slow");
                    $(this).children().find('.more').html('숨기기');
                }
            }
        });
    }
});