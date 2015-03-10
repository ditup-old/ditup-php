(function($){
  var xhr;
  
  (function(input, xhr){
    input.bind('keyup', function(e){
      if(xhr && xhr.readyState != 4){
        xhr.abort();
      }
      xhr = $.ajax('/ajax-search',{data: "szuk="+input.val(),type:'POST',async:true,success:function(backpack){
        console.log(backpack);
      }});
    });
  })($('#search-form-header>input'), xhr);
  
  
  
})($);
