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
	             + '<td><a class="down">move down</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a class="up">move up</a></td>');

	    row.insertBefore($(this).closest('tr'));
	    
	    bindActions();
	});
	
	$("#apply").click(function() {
		var duties = new Array();
		var displayPriority = 1;
		
		$("#duty-table").find('tr').each(function() {
			
			if ($(this).attr('id').length > 0) {
				var duty = {id: $(this).attr('id'), duty_name: $(this).find('.duty-old').html(), display_priority: displayPriority++};
				duties.push(duty);
			}
		});
		
		console.log(duties);
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
}

