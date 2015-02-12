
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Attach Permissions to <strong>{{$role}}</strong> Role</h4>
      </div>
      <div class="modal-body">
        <div class="messages"> </div>
        <form id="roles_form" accept-charset="UTF-8">
          <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
          <input type="hidden" name="role" value="{{{ $role }}}">
          <fieldset>
              
              <div class="form-group">

                    <label>Available Permissions</label>

                  <div class="role_checkbox col-md-12">
                    <div class="">
                      @foreach($perms as $perm)
                          <div class='col-md-6'>
                            <label>
                              @if(in_array($perm['id'],$role_perms))
                                <input type="checkbox" value="{{{$perm['id']}}}" name="perms[]" id="perms[]" checked="checked">
                              @else <input type="checkbox" value="{{{$perm['id']}}}" name="perms[]" id="perms[]">
                              @endif
                              {{{$perm['display_name']}}}
                            </label>
                          </div>  
                          <!-- &nbsp; -->
                      @endforeach
                </div>
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
          <button type="button" id="submitBtn" class="btn btn-primary submitBtn">Save changes</button>
      </div>
  </div>
</div>

<script type="text/javascript">
 $(".submitBtn").click(function(e){
        $(".f_bar").addClass( "active" )
        $(".bar").css("width", "0%");
       
        $("#roles_form :input").prop("readonly", true);
        $("#submitBtn").prop("disabled", true);
   // console.log( $("#updateform").serialize() );
  var request = $.ajax({
      url: "roles/assign_permisions",
      type: "POST",
      data: $("#roles_form").serialize(),
      dataType: "json"
    });
    $(".bar").css("width", "50%");
    request.done(function( data ) {
      console.log("Complete");
      // if(changes_flag == 0) changes_flag = data.flag;
      // console.log(changes_flag);
      // console.log(data.error);
       $("#roles_form :input").prop("readonly", false);
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
  });
    
</script>
