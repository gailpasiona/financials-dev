
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">System User Panel</h4>
      </div>
      <div class="modal-body">
        <div class="messages"> </div>
        <form id="user_form" accept-charset="UTF-8">
          <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
          @if(isset($info))
              <input type="hidden" name="id" value="{{{ $info['id'] }}}">
          @endif
          <fieldset>
              <div class="form-group">
                  <label for="full_name">Employee Name</label>
                  @if(isset($info))
                    <input class="form-control" placeholder="Employee Name" type="text" name="full_name" id="full_name" value="{{{$info['full_name']}}}">
                  @else
                    <input class="form-control" placeholder="Employee Name" type="text" name="full_name" id="full_name">
                  @endif
              </div>
              <div class="form-group">
                  <label for="username">{{{ Lang::get('confide::confide.username') }}}</label>
                  @if(isset($info))
                    <input class="form-control" placeholder="{{{ Lang::get('confide::confide.username') }}}" type="text" name="username" id="username" value="{{{$info['username']}}}">
                  @else
                     <input class="form-control" placeholder="{{{ Lang::get('confide::confide.username') }}}" type="text" name="username" id="username">
                  @endif
              </div>
              <div class="form-group">
                  <label for="password">{{{ Lang::get('confide::confide.password') }}}</label>
                  <input class="form-control" placeholder="{{{ Lang::get('confide::confide.password') }}}" type="password" name="password" id="password">
              </div>
              <div class="form-group">
                  <label for="password_confirmation">{{{ Lang::get('confide::confide.password_confirmation') }}}</label>
                  <input class="form-control" placeholder="{{{ Lang::get('confide::confide.password_confirmation') }}}" type="password" name="password_confirmation" id="password_confirmation">
              </div>

          </fieldset>
      </form>
      </div>
      <div class="modal-footer">
          
            <div class="progress progress-striped f_bar">

                <div class="progress-bar bar f_bar" style="width: 0%;">
                     
                    <span class="prog_txt">Please Wait</span>

                </div>

            </div>

          <button type="button" class="btn btn-default" id="dumer" data-dismiss="modal">Close</button>
          @if(isset($info))
            <button type="button" id="submitBtn" class="btn btn-primary submitBtn" onclick="submit_request('update')">Save changes</button>
          @else
            <button type="button" id="submitBtn" class="btn btn-primary submitBtn" onclick="submit_request('create')">Create User</button>
          @endif
          
      </div>
  </div>
</div>

<script type="text/javascript">
 //$(".submitBtn").click(function(e){
  function submit_request(target){
        $(".f_bar").addClass( "active" )
        $(".bar").css("width", "0%");
       
        $("#createform :input").prop("readonly", true);
        $("#submitBtn").prop("disabled", true);
    console.log( $("#updateform").serialize() );
    var address = "users/" + target;
    // if($("#id").value != null) address = "users/update";
    // else address = "users/create";
  var request = $.ajax({
      url: address,
      type: "POST",
      data: $("#user_form").serialize(),
      dataType: "json"
    });
    $(".bar").css("width", "50%");
    request.done(function( data ) {
      console.log("Complete");
      // if(changes_flag == 0) changes_flag = data.flag;
      // console.log(changes_flag);
      // console.log(data.error);
       $("#user_form :input").prop("readonly", false);
        $("#submitBtn").prop("disabled", false);
         $(".bar").css("width", "100%");
         $(".f_bar").removeClass( "active" );
         $(".prog_txt").hide();
         $("div").removeClass("has-error");
         if(data.status == '0'){
             $( ".message_content" ).remove();//remove first if exists
             var prompt = "<br />";
             $.each($.parseJSON(data.error), function(key,value) {
                    prompt += value + "<br />";
                    $('.' + key).addClass("has-error");
             });
             $(".messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                <strong>Errors Occured!</strong> '+prompt+' </div>');
            
             
           
         }
         else if(data.status == '1'){
             $( ".message_content" ).remove();//remove first if exists
             $(".messages").append('<div class="message_content alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                <strong>Completed!</strong> '+ data.message +'. </div>');
             $("#createform").trigger("reset");
         }
         else{
             //catch errors here....
         }
        
         //$(".bar").css("width", "0%");
         //$(".f_bar").hide();
      // $('#progress').modal('hide');
    });
    

    request.fail(function( jqXHR, textStatus ) {
      $(".bar").css("width", "100%");
      $( ".message_content" ).remove();//remove first if exists
             $(".messages").append('<div class="message_content alert alert-error alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                <strong>System Failure!</strong> Unknown error occured, contact SYSTEM ADMINISTRATOR </div>');
             $("#createform").trigger("reset");
             $(".prog_txt").hide();

      console.log("Failed");
      console.log(textStatus);
    });
  }//);
    
</script>
