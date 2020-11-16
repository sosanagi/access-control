jQuery(document).ready(function(){
  $(".replace").click(function (){
    var val = $(this).val();
    $("." + val).show();
    console.log(val);
    });
  });
