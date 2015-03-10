(function($){
  var xhr;
  
  (function(input, xhr, results){
    input.bind('keyup', function(e){
      if(xhr && xhr.readyState != 4){
        xhr.abort();
      }
      xhr = $.ajax('/ajax-search',{data: "szuk="+input.val(),type:'POST',async:true,success:function(backpack){
        backpack = JSON.parse(backpack);
	results.empty();
        for(var user in backpack.users){
          $(document.createElement('div')).appendTo(results).text(backpack.users[user].username);
	}
        for(var dit in backpack.dits){
          $(document.createElement('div')).appendTo(results).text(backpack.dits[dit].ditname);
	}
      }});
    });
  })($('#search-form-header>input'), xhr, $('#search-form-header-results'));
  
  
  
})($);
