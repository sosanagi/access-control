jQuery(document).ready(function(){
  $('[name="confirm"]').click(function (){
    var ID = $(this).val();
    var name = $('#'+ ID + ' [name="user_name"]').val();
    if(!confirm('本当に削除しますか？')){
        console.log(ID,name);
        return false;
    }
  });
});
