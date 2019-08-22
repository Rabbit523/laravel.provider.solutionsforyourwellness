@extends('admin.layouts.default_layout')
@section('content')
@section('title','Nofication')
<!-- Content Wrapper. Contains page content -->
<style>
.wrapper > ul#results li {
  margin-bottom: 1px;
  background: #f9f9f9;
  padding: 20px;
  list-style: none;
}
.ajax-loading{
  text-align: center;
}
</style>
<div class="wrapper">
    
</div>
<script>
var page = 1; //track user scroll as page number, right now page number is 1
load_more(page); //initial content load
$(window).scroll(function() { //detect page scroll
    if($(window).scrollTop() + $(window).height() >= $(document).height()) { //if user scrolled from top to bottom of the page
        page++; //page number increment
        load_more(page); //load content   
    }
});     
function load_more(page){
  $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            beforeSend: function()
            {
                $('.ajax-loading').show();
            }
        })
        .done(function(data)
        {
            if(data.length == 0){
            console.log(data.length);
               
                //notify user if nothing to load
                $('.ajax-loading').html("No more records!");
                return;
            }
            $('.ajax-loading').hide(); //hide loading animation once data is received
            $("#notifications-dropdown").append(data); //append data into #results element          
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
 }
</script>
@stop
