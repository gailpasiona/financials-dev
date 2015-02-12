<div class="modal-dialog modal-wide">
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
                                  
                                  @if(isset($data['record']['register_id']))
                                    @if(isset($data['type']))
                                      <input class="form-control" type="hidden" name="invoice_no" id="invoice_no" value="{{{$data['record']['register_id']}}}">
                                    @else
                                      <input class="form-control" type="hidden" name="receipt_no" id="receipt_no" value="{{{$data['record']['register_id']}}}">
                                    @endif
                                  @endif
                                  
                                    <label for="cost_dept" class="col-md-4 control-label">Customer</label>
                                    <div class="col-md-6">
                                       <select class="form-control" name="payee" id="payee">
                                          <option selected="selected" value="{{{$data['record']['customer']['id']}}}">{{$data['record']['customer']['supplier_name']}}</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="payee_name" class="col-md-4 control-label">Receipt Date</label>
                                    <div class="col-md-6">
                                     @if(!isset($data['record']['invoice_date']))
                                        <input class="form-control datepicker" placeholder="Invoice Date" type="text" readonly="readonly" name="receipt_date" id="receipt_date" value="{{{ date("Y-m-d")}}}">
                                      @else
                                        <input class="form-control datepicker" placeholder="Invoice Date" type="text" readonly="readonly" name="receipt_date" id="receipt_date" value="{{{$data['record']['invoice_date']}}}">
                                      @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="payee_name" class="col-md-4 control-label">Amount</label>
                                    <div class="col-md-6">
                                      <input class="form-control" placeholder="Amount" type="text" readonly="readonly" name="amount" id="amount" value="{{{$data['record']['account_value']}}}">
                                    </div>
                                </div>
                                    
                                <div class="form-group row">
                                    <label for="cost_dept" class="col-md-4 control-label">Account</label>
                                    <div class="col-md-6">
                                       <select class="form-control" name="account" id="account">
                                           @foreach($data['coa'] as $coa)
                                             @if (isset($data['record']['account_id']) && $data['record']['account_id'] == $coa['account_id'])
                                                <option selected="selected" value="{{{$coa['account_id']}}}">{{$coa['account_title']}}</option>
                                             @else
                                                <option value="{{{$coa['account_id']}}}">{{$coa['account_title']}}</option>
                                             @endif
                                           @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- <div class="form-group row">
                                    <label for="register_refno" class="col-md-4 control-label">Invoice Reference</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['register_refno']))
                                        <input class="form-control editable" placeholder="Invoice Reference" type="text" name="register_refno" id="payee_name" value="">
                                      @else
                                        <input class="form-control editable" placeholder="Invoice Reference" type="text" name="register_refno" id="payee_name" value="{{{$data['register_refno']}}}">
                                      @endif
                                    </div>
                                </div> -->

                                 <!-- <div class="form-group col-md-12">
                                    <span class="col-md-6 col-md-offset-1 control-label"><label>Items</label></span>
                                    <br >
                                    <div /id="credit" class="col-md-12 account_items">
                                        <div class="col-md-12 col-md-offset-0">
                                            <input class="btn btn-primary btn-block btn-sm" onclick="addAccountRow(this.form,2);" 
                                            type="button" value="Add Account(s)" />
                                        </div>

                                    </div>
                                </div> -->
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
           @if(!isset($data['type']))
              <button type="button" id="submitBtn" class="btn btn-primary submitBtn">Save changes</button>
              <button type="button" id="postBtn" class="btn btn-success postBtn">Verify Receipt</button>
           @else
              <button type="button" id="createBtn" class="btn btn-primary createBtn">Save</button>
           @endif

      </div>
    </div>
</div>

<!-- <script src="{{ URL::asset('js/dyn_fields.js')}}"></script> -->

    
    <script>
        
        $('.datepicker').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
        });
        
        $("#submitBtn").click(function(e){
          $(".f_bar").addClass( "active" );
          $(".bar").css("width", "0%");
         
          $("#createform :input.editable").prop("readonly", true);
          $("#submitBtn").prop("disabled", true);
          $("#postBtn").prop("disabled", true);
          var request = $.ajax({
            url: 'receipts/' + encodeURIComponent($("#receipt_no").val()),
            type: "PATCH",
            data: $("#createform").serialize()
            // dataType: "json"
          });
          $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input#amount_request").prop("readonly", false);
            $("#submitBtn").prop("disabled", false);
             $("#postBtn").prop("disabled", false);
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
           $("#postBtn").prop("disabled", true);
          var request = $.ajax({
            url: 'receipts',
            type: "POST",
            data: $("#createform").serialize()
            // dataType: "json"
          });
          $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input").prop("readonly", false);
            $("#createform :input.datepicker").prop("readonly", true);
            $("#createBtn").prop("disabled", false);
            $("#postBtn").prop("disabled", false);
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

      $("#postBtn").click(function(e){

          var request = $.ajax({
            url: 'receipts/posting',
            type: "POST",
            data: $("#createform").serialize(),
                  //dataType: "json"
          });
          $(".f_bar").addClass( "active" );
          $(".bar").css("width", "0%");
          $("#createform :input").prop("readonly", true);
          $("#postBtn").prop("disabled", true);

          $(".bar").css("width", "50%");

          request.done(function( data ) {
            $("#createform :input").prop("readonly", false);
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
    



