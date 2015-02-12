<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">{{{$data['title']}}}</h4>
      </div>
      <div class="modal-body">
                <div class="messages"></div>
                    <form class="form-horizontal" id="createform" role="form" method="POST" accept-charset="UTF-8">
                    <fieldset>
                        <div id="voucher_info" class="form-group">
                            <div class="col-md-12">
                                <div class="form-group row">
                                  @if(isset($data['po_reference']))
                                    <input class="form-control" type="hidden" name="po_reference" id="po_reference" value="{{{$data['po_reference']}}}">
                                  @endif

                                  @if(!isset($data['invoice_no']))
                                    <input class="form-control" type="hidden" name="invoice_no" id="invoice_no" value="">
                                  @else
                                    <input class="form-control" type="hidden" name="invoice_no" id="invoice_no" value="{{{$data['invoice_no']}}}">
                                  @endif
                                    <label for="cost_dept" class="col-md-4 control-label">Cost Department</label>
                                    <div class="col-md-6">
                                        @if(!isset($data['cost_dept']))
                                          <input class="form-control" placeholder="Cost Department" type="text" readonly = "readonly" name="cost_dept" id="cost_dept" value="">
                                        @else
                                          <input class="form-control" placeholder="Cost Department" type="text" readonly = "readonly" name="cost_dept" id="cost_dept" value="{{{$data['cost_dept']}}}">
                                        @endif
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="payee_name" class="col-md-4 control-label">Payee Name</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['payee_name']))
                                        <input class="form-control" placeholder="Payee Name" type="text" readonly = "readonly" name="payee_name" id="payee_name" value="">
                                      @else
                                        <input class="form-control" placeholder="Payee Name" type="text" readonly = "readonly" name="payee_name" id="payee_name" value="{{{$data['payee_name']}}}">
                                      @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="amount_request" class="col-md-4 control-label">Amount Requested</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['amount_request']))
                                        <input class="form-control editable" placeholder="Amount Requsted" type="text" name="amount_request" id="amount_request" value="">
                                      @else
                                        <input class="form-control editable" placeholder="Amount Requsted" type="text" name="amount_request" id="amount_request" value="{{{$data['amount_request']}}}">
                                      @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="register_refno" class="col-md-4 control-label">Invoice Reference</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['register_refno']))
                                        <input class="form-control editable" placeholder="Invoice Reference" type="text" name="register_refno" id="payee_name" value="">
                                      @else
                                        <input class="form-control editable" placeholder="Invoice Reference" type="text" name="register_refno" id="payee_name" value="{{{$data['register_refno']}}}">
                                      @endif
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <label for="payee_address" class="col-md-4 control-label">Payee Address</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['payee_address']))
                                        <input class="form-control" placeholder="Payee Address" type="text" readonly = "readonly" name="payee_address" id="payee_address" value="">
                                      @else
                                        <input class="form-control" placeholder="Payee Address" type="text" readonly = "readonly" name="payee_address" id="payee_address" value="{{{$data['payee_address']}}}">
                                      @endif
                                    </div>
                                </div> -->

                                <!-- <div class="form-group row">
                                    <label for="description" class="col-md-4 control-label">Description</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['description']))
                                        <input class="form-control" placeholder="Description" type="text" name="description" id="description" value="">
                                      @else
                                        <input class="form-control" placeholder="Description" type="text" name="description" id="description" value="{{{$data['description']}}}">
                                      @endif
                                    </div>
                                </div>
 -->
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
     <div class="modal-footer">
          
            <div class="progress progress-striped f_bar">

                <div class="progress-bar bar f_bar" style="width: 0%;">
                     
                    <span class="prog_txt"></span>

                </div>

            </div>

          <button type="button" class="btn btn-default" id="dumer" data-dismiss="modal">Close</button>
           @if(isset($data['invoice_no']))
              <button type="button" id="submitBtn" class="btn btn-primary submitBtn">Save changes</button>
           @else
              <button type="button" id="createBtn" class="btn btn-primary createBtn">Save</button>
           @endif

      </div>
    </div>
</div>
    
    <script>
        
        // $('.datepicker').datepicker({
        //   format: 'yyyy-mm-dd',
        //   autoclose: true,
        // });
        
        $("#submitBtn").click(function(e){
          $(".f_bar").addClass( "active" );
          $(".bar").css("width", "0%");
         
          $("#createform :input").prop("readonly", true);
          $("#submitBtn").prop("disabled", true);
          var request = $.ajax({
            url: 'invoice/' + encodeURIComponent($("#invoice_no").val()),
            type: "PATCH",
            data: $("#createform").serialize()
            // dataType: "json"
          });
          $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input#amount_request").prop("readonly", false);
            $("#submitBtn").prop("disabled", false);
            $(".bar").css("width", "100%");
            $(".f_bar").removeClass( "active" );

            show_message(data);

          });
          request.fail(function( jqXHR, textStatus ) {
            $(".bar").css("width", "50%");
            console.log("Failed");
            console.log(textStatus);
          });
      });

        $("#createBtn").click(function(e){
          $(".f_bar").addClass( "active" );
          $(".bar").css("width", "0%");
         
          $("#createform :input").prop("readonly", true);
          $("#createBtn").prop("disabled", true);
          var request = $.ajax({
            url: 'AP/invoice',
            type: "POST",
            data: $("#createform").serialize()
            // dataType: "json"
          });
          $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input.editable").prop("readonly", false);
            $("#createBtn").prop("disabled", false);
            $(".bar").css("width", "100%");
            $(".f_bar").removeClass( "active" );

            show_message(data);

          });
          request.fail(function( jqXHR, textStatus ) {
            $(".bar").css("width", "100%");
            console.log("Failed");
            console.log(textStatus);
            $(".messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Succeeded!</strong> <br />System Error, please contact your System Administrator</div>');
          });
      });

      function show_message(data){
            $("div").removeClass("has-error");
            $( ".message_content" ).remove();//remove first if exists
             var prompt = "<br />";

             if(data.status == 'success_error'){
                $.each(data.message, function(key,value) {
                    prompt += value + "<br />";
                    $('.' + key).addClass("has-error");
                 });
                 $(".modal-dialog .messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Errors Occured!</strong> '+prompt+' </div>');
             }
             else if(data.status == 'success_failed'){
                  $(".modal-dialog .messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Failed!</strong> <br />'+data.message+' </div>');
             }
             else{
                  $(".messages").append('<div class="message_content alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Succeeded!</strong> <br />'+data.message+' </div>');
                  
                  $('#modal_form').modal('hide');
             }
             
      }

    </script>
    



