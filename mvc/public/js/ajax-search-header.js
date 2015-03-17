(function($){
  var xhr;

  var SearchName=function(name, substring){
    var pieces = name.split(substring);
    var ret = $(document.createElement('span'));
    for(var i=0, len=pieces.length; i<len; i++){
      ret.append(document.createTextNode(pieces[i]));
      if(i!=len-1){
        ret.append($(document.createElement('span')).append(document.createTextNode(substring)).css({'font-weight':'bold'}));
      }
    }
    //console.log(ret);
    //return $(document.createTextNode('asdf'));
    return ret;
  }

  var SearchName2=function(name, substring){
    function escapeRegExp(string){
      return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
    }
    var result;
    var ret = $(document.createElement('span'));
    var re = new RegExp(/*'.*('+*/escapeRegExp(substring)/*+').*'*/, 'ig');
    var finish = 0;
    while((result=re.exec(name))!==null){
      //console.log(result);
      var between=result.input.substring(finish, result.index);
      ret.append(document.createTextNode(between));
      //console.log([finish, result.index, between]);
      //var match = $(document.createElement('span'));
      ret.append($(document.createElement('span')).append(document.createTextNode(result[0])).css({'font-weight':'bold'}));
      //ret.append(document.createTextNode('***'));
      finish=result.index+result[0].length;
      //console.log(finish);
    }
    ret.append(document.createTextNode(name.substring(finish, name.length)));
    //console.log();
    return ret;

  }

  var Output=(function(SearchName){
    var user=function(userdata, substring){
      var pic = $(document.createElement('img')).attr({src: userdata.img}).css({height:'20px'});
      var link = $(document.createElement('a')).attr({href: '/user/'+userdata.username}).append(pic).append(SearchName(userdata.username, substring));
      var ret = $(document.createElement('div')).append(link).css({color:'red', 'background-color':'white'});

      return ret;
    }

    var dit=function(ditdata, substring){
      var pic = $(document.createElement('img')).attr({src: '/img/logo.png'}).css({height:'20px'});
      var link = $(document.createElement('a')).attr({href: '/'+ditdata.type+'/'+ditdata.url}).append(pic).append(document.createTextNode(ditdata.type+': ')).append(SearchName(ditdata.ditname, substring));
      var ret = $(document.createElement('div')).append(link).css({color:'green', 'background-color': 'white'});
      return ret;
    }

    return {
      user: user,
      dit: dit
    }
  })(SearchName2);
  
  (function(input, xhr, results){
    results.css({position: 'absolute'});
    input.bind('keyup', function(e){
      if(xhr && xhr.readyState != 4){
        xhr.abort();
      }
      var szuk=input.val();
      xhr = $.ajax('/ajax-search',{data: "szuk="+szuk,type:'POST',async:true,success:function(backpack){
        var backpack = JSON.parse(backpack);
        //console.log(backpack);
	results.empty();
        for(var user in backpack.users){
          Output.user(backpack.users[user], szuk).appendTo(results);
	}
        for(var dit in backpack.dits){
          Output.dit(backpack.dits[dit], szuk).appendTo(results);
	}
      }});
    });
  })($('#search-form-header>input'), xhr, $('#search-form-header-results'));
  
  
  
})($);
