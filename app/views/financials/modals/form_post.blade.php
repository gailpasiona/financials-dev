<div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">{{{$data['title']}}}</h4>
      </div>
      <div class="modal-body">
        
            <div class="messages"> </div>
                <form class="form-horizontal" id="postingform" role="form" method="POST" accept-charset="UTF-8">
	                <fieldset>
                    <!-- <input class="form-control" type="hidden" name="po_reference" id="po_reference" value="{{{$data['invoice']}}}"> -->
                    <div class="form-group row">
                                    <label for="payee_name" class="col-md-4 control-label">Invoice Number</label>
                                    <div class="col-md-6">
                                       <input class="form-control" readonly = "readonly" name="invoice_no" id="invoice_no" value="{{{$data['invoice']}}}">
                                  </div>
                                </div>

	                	<div class="form-group row">
                                    <label for="payee_name" class="col-md-4 control-label">Payee Name</label>
                                    <div class="col-md-6">
                                       <input class="form-control" placeholder="Payee Name" type="text" readonly = "readonly" name="payee_name" id="payee_name" value="{{{$data['payee']}}}">
                                     </div>
                                </div>

                                <div class="form-group row">
                                    <label for="amount_request" class="col-md-4 control-label">Amount Requested</label>
                                    <div class="col-md-6">
                                      
                                        <input class="form-control" placeholder="Amount Requested" type="text" readonly = "readonly" name="amount_request" id="amount_request" value="{{{$data['amount']}}}">
                                      
                                    </div>
                                </div>

                    <div class="form-group row">
                        <label for="register_refno" class="col-md-4 control-label">Invoice Reference</label>
                                    <div class="col-md-6">
                                     
                                        <input class="form-control" placeholder="Invoice Reference" type="text" readonly = "readonly" name="register_refno" id="payee_name" value="{{{$data['refno']}}}">
                                      
                                    </div>
                                </div>

                    <div class="form-group col-md-12">
                                    <span class="col-md-6 col-md-offset-1 control-label"><label>Account</label></span>
                                    <div id="credit" class="col-md-12 account_items">
                                        <div class="col-md-12 col-md-offset-0">
                                            <input class="btn btn-primary btn-block btn-sm" onclick="addAccountRow(this.form,1);" 
                                            type="button" value="Add Account(s)" />
                                        </div>

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
         
          <button type="button" id="submitBtn" class="btn btn-primary submitBtn">Post</button>

      </div>
   </div>
</div>

<script src="{{ URL::asset('js/dyn_fields.js')}}"></script>

<script type="text/javascript">
$("#submitBtn").click(function(e){

    var request = $.ajax({
      url: 'invoice/posting',
      type: "POST",
      data: $("#postingform").serialize(),
            //dataType: "json"
    });
    $(".f_bar").addClass( "active" );
    $(".bar").css("width", "0%");
    $("#createform :input").prop("readonly", true);
    $("#submitBtn").prop("disabled", true);

    $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input#amount_request").prop("readonly", false);
            $("#submitBtn").prop("disabled", false);
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
