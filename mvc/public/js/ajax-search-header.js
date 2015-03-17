(function($){
  var xhr;

  var Output=(function(){
    var user=function(userdata){
      var pic = $(document.createElement('img')).attr({src: userdata.img}).css({height:'20px'});
      var link = $(document.createElement('a')).attr({href: '/user/'+userdata.username}).append(pic).append(document.createTextNode(userdata.username));
      var ret = $(document.createElement('div')).append(link).css({color:'red', 'background-color':'white'});

      return ret;
    }

    var dit=function(ditdata){
      var pic = $(document.createElement('img')).attr({src: '/img/logo.png'}).css({height:'20px'});
      var link = $(document.createElement('a')).attr({href: '/'+ditdata.type+'/'+ditdata.url}).append(pic).append(document.createTextNode(ditdata.type+': '+ditdata.ditname));
      var ret = $(document.createElement('div')).append(link).css({color:'green', 'background-color': 'white'});
      return ret;
    }

    return {
      user: user,
      dit: dit
    }
  })();
  
  (function(input, xhr, results){
    input.bind('keyup', function(e){
      if(xhr && xhr.readyState != 4){
        xhr.abort();
      }
      xhr = $.ajax('/ajax-search',{data: "szuk="+input.val(),type:'POST',async:true,success:function(backpack){
        var backpack = JSON.parse(backpack);
        //console.log(backpack);
	results.empty();
        for(var user in backpack.users){
          Output.user(backpack.users[user]).appendTo(results);
	}
        for(var dit in backpack.dits){
          Output.dit(backpack.dits[dit]).appendTo(results);
	}
      }});
    });
  })($('#search-form-header>input'), xhr, $('#search-form-header-results'));
  
  
  
})($);
