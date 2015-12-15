$(document).ready(function() {
	bindActions();

	$("#new-duty").click(function() {
		var numRow = 0;
	    var relations = '';
	    
	    $("#duty-table").find('tr').each(function() {
	        numRow++;
	    });
 
	    var row = $('<tr id="' + (numRow - 1) + '"></tr>');
	    
	    row.html('<td><div class="duty-old hide"></div><div class="duty-new"><input type="text" name="duty_name" /><span class="button-light change">변경</span></div></td>'
                 + '<td align="center"><a class="down">move down</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a class="up">move up</a></td>'
                 + '<td align="center"><input type="checkbox" name="remove" value="1"></td>');

	    row.insertBefore($(this).closest('tr'));
	    
	    bindActions();
	});
	
	$("#apply").click(function() {
		var duties = new Array();
		var displayPriority = 1;
		
		$("#duty-table").find('tr').each(function() {
			
			if ($(this).attr('id').length > 0) {
				var duty = {
                    id: $(this).attr('id'),
                    duty_name: $(this).find('.duty-old').html(),
                    display_priority: displayPriority++,
                    remove: $(this).find('input[type="checkbox"]').is(':checked')
                };
				duties.push(duty);
			}
		});

        $("p.addition").html('');
        $.post('/disciples/ajax/update-duties/', {data: duties}, function(response) {
            if (response.success) {
                window.location.href = '/disciples/admin';
            } else {
                $("p.addition").html(response.message);
            }
        }, "json");
	});

	$("#cancel").click(function() {
		window.location.href = '/disciples/admin';
	});
});

bindActions = function() {
	$(".duty-old").unbind('click');
	$(".change").unbind('click');
	$(".down").unbind('click');
	$(".up").unbind('click');
    $("input[name='remove']").unbind('click');
	
	$(".duty-old").click(function() {
		$(this).hide();
		var duty_new = $(this).parent().find(".duty-new");
		duty_new.show(500);
		duty_new.find("input[name='duty_name']").val($(this).html());
	});
	
	$(".change").click(function() {
		var duty_old = $(this).parent().parent().find(".duty-old");
		var duty_new = $(this).parent().parent().find(".duty-new");
		duty_new.hide();
		duty_old.html(duty_new.find("input[name='duty_name']").val());
		duty_old.show();
	});
	
	$(".down").click(function() {
		var tr = $(this).closest('tr');
		var next = tr.next();
		
		if (next.attr('id').length > 0) {
			tr.insertAfter(next);
		}
	});
	
	$(".up").click(function() {
		var tr = $(this).closest('tr');
		var prev = tr.prev();
		
		if (prev.attr('id').length > 0) {
			tr.insertBefore(prev);
		}
	});

    $("input[name='remove']").click(function() {
        if ($(this).is(':checked')) {
            if (!confirm("Are you sure to remove the selected duty option?")) {
                $(this).attr('checked',false);
            }
        }
    });
}

