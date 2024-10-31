jQuery(document).ready(function($) {
$(document).ready(function(){
//Pagination
pageSize = 20;
incremSlide = 5;
startPage = 0;
numberPage = 0;



var pageCount =  $(".pnwpaginate").length / pageSize;
var totalSlidepPage = Math.floor(pageCount / incremSlide);
    
for(var i = 0 ; i<pageCount;i++){
    $("#pagin").append('<li><button class="btno">'+(i+1)+'</button></li> ');
    if(i>pageSize){
       $("#pagin li").eq(i).hide();
    }
}

var prev = $("<li/>").addClass("sbwprev").html("Prev").click(function(){
   startPage-=5;
   incremSlide-=5;
   numberPage--;
   slide();
});

prev.hide();

var next = $("<li/>").addClass("sbwnext").html("Next").click(function(){
   startPage+=5;
   incremSlide+=5;
   numberPage++;
   slide();
});

$("#pagin").prepend(prev).append(next);

// $("#pagin li").first().find("btno").addClass("current");

slide = function(sens){
   $("#pagin li").hide();
   
   for(t=startPage;t<incremSlide;t++){
     $("#pagin li").eq(t+1).show();
   }
   if(startPage == 0){
     next.show();
     prev.hide();
   }else if(numberPage == totalSlidepPage ){
     next.hide();
     prev.show();
   }else{
     next.show();
     prev.show();
   }
   
    
}

showPage = function(page) {
	  $(".pnwpaginate").hide();
	  $(".pnwpaginate").each(function(n) {
	      if (n >= pageSize * (page - 1) && n < pageSize * page)
	          $(this).show();
	  });        
}
    
showPage(1);
// $("#pagin li").eq(0).addClass("current");

$("#pagin li button").click(function() {
	
	 $("#pagin .btno.current").removeClass("current");
	 $(this).addClass("current");
	 showPage(parseInt($(this).text()));

});


	
	var mypageCount = Math.ceil(pageCount);
	$("#showingpageof").html("Showing page 1 of "+mypageCount);
	
	
	// Then change it once a click happens
	$("#pagin li").click(function() {
	
	var decurrent = $(".btno.current").text();
	var mypageCount = Math.ceil(pageCount);
	
	$("#showingpageof").html("Showing page "+decurrent+" of "+mypageCount);
	
	});
	
	

});
});