/**
 * @author   Nicolas Rod <nico@alaxos.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.alaxos.ch
 */
function register_user_toggle_right(start_granted, app_root_url, span_id, user_id, plugin, controller, action)
{
	if(start_granted)
	{
		var url1 = app_root_url + "admin/acl/aros/deny_user_permission/" + user_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/grant_user_permission/" + user_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
	}
	else
	{
		var url1 = app_root_url + "admin/acl/aros/grant_user_permission/" + user_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/deny_user_permission/" + user_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
	}
	
	$("#" + span_id).toggle(function()
                    		{
								$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").show();
								
								$.ajax({	url: url1,
											dataType: "html", 
											cache: false,
    										success: function (data, textStatus) 
    										{
    											$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action).html(data);
    										},
    										complete: function()
        									{
        										$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").hide();
        									}
										});
                    		}, 
                    		function()
                    		{
                    			$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").show();
                    			
                    			$.ajax({	url: url2,
        									dataType: "html", 
        									cache: false,
        									success: function (data, textStatus) 
        									{
        										$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action).html(data);
        									},
        									complete: function()
        									{
        										$("#right_" + plugin + "_" + user_id + "_" + controller + "_" + action + "_spinner").hide();
        									}
								});
                    		}); 
}

function register_role_toggle_right(start_granted, app_root_url, span_id, role_id, plugin, controller, action)
{
	if(start_granted)
	{
		var url1 = app_root_url + "admin/acl/aros/deny_role_permission/" + role_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/grant_role_permission/" + role_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
	}
	else
	{
		var url1 = app_root_url + "admin/acl/aros/grant_role_permission/" + role_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
		var url2 = app_root_url + "admin/acl/aros/deny_role_permission/" + role_id + "/plugin:" + plugin + "/controller:" + controller + "/action:" + action;
	}
	
	$("#" + span_id).toggle(function()
                    		{
								$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").show();
								
								$.ajax({	url: url1,
											dataType: "html", 
											cache: false,
    										success: function (data, textStatus) 
    										{
    											$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action).html(data);
    										},
    										complete: function()
        									{
        										$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").hide();
        									}
										});
                    		}, 
                    		function()
                    		{
                    			$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").show();
                    			
                    			$.ajax({	url: url2,
        									dataType: "html", 
        									cache: false,
        									success: function (data, textStatus) 
        									{
        										$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action).html(data);
        									},
        									complete: function()
        									{
        										$("#right_" + plugin + "_" + role_id + "_" + controller + "_" + action + "_spinner").hide();
        									}
								});
                    		}); 
}