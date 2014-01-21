
var invite_url = 'http://invite.paysdu42.fr/';
var service_name = 'test';

function        checkInvite(invite, success, failure) {
    if (invite == 'null') {
        failure();
        return ;
    }
    var successWrapper = function(res) {
        if (!res || res == 'false')
            failure();
        else
            success();
    };
    $.ajax({
	    dataType: "json",
		type: 'GET',
		url: invite_url + '/invites/' + invite,
		data: {service_name:service_name},
		error: failure,
		success: successWrapper,
		timeout: 2000
		});
}

