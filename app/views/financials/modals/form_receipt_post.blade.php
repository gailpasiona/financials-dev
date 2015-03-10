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
                                      <input class="form-control" placeholder="Customer" type="text" readonly="readonly" name="payee" id="payee" value="{{$data['record']['customer']['supplier_name']}}">
                                       <!-- <select class="form-control" name="payee" id="payee" readonly="readonly">
                                          <option selected="selected" value="{{{$data['record']['customer']['id']}}}">{{$data['record']['customer']['supplier_name']}}</option>
                                        </select> -->
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="payee_name" class="col-md-4 control-label">Receipt Date</label>
                                    <div class="col-md-6">
                                     @if(!isset($data['record']['invoice_date']))
                                        <input class="form-control datepicker" placeholder="Invoice Date" type="text" readonly="readonly" name="receipt_date" id="receipt_date" value="{{{ date("Y-m-d")}}}">
                                      @else
                                        <input disabled="disabled" class="form-control datepicker" placeholder="Invoice Date" type="text" readonly="readonly" name="receipt_date" id="receipt_date" value="{{{$data['record']['invoice_date']}}}">
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
                                    <label for="payee_name" class="col-md-4 control-label">Receipt Reference</label>
                                    <div class="col-md-6">
                                      @if(isset($data['type']))
                                        <input class="form-control" placeholder="Reference" type="text" name="receipt_ref" id="receipt_ref" value="">
                                      @else
                                        <input readonly="readonly" class="form-control" placeholder="Reference" type="text" name="receipt_ref" id="receipt_ref" value="{{{$data['record']['register_refno']}}}">
                                      @endif
                                    </div>
                                </div>
                                    
                                <div class="form-group row">
                                    <label for="cost_dept" class="col-md-4 control-label">Target Bank</label>
                                    <div class="col-md-6">
                                       <select class="form-control" name="bank_account" id="bank_account" disabled="disabled">
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
                                <div class="form-group col-md-12">
                                    <span class="col-md-6 col-md-offset-1 control-label"><label>Account</label></span>
                                    <div id="credit" class="col-md-12 account_items">
                                        <!-- <div class="col-md-12 col-md-offset-0">
                                            <input class="btn btn-primary btn-block btn-sm" onclick="addAccountRow(this.form,1);" 
                                            type="button" value="Add Account(s)" />
                                        </div> -->

                                        @if ( isset($data['record']['sales_lines']))
                                          @if ( !empty($data['record']['sales_lines']))
                                              @foreach( $data['record']['sales_lines'] as $items)
                                                  <div id="AccountrowNum{{{ $items['line_no'] }}}">
 
                                                      <div class="col-md-12 lines">
                                                        <div class="col-md-2"><span class="col-md-1 control-label"></span><select class="form-control" id="entry_type[]" name="entry_type[]">
                                                              @if ($items['entry_type'] == 'D')
                                                                <option value="0" selected="selected"> Debit</option>
                                                                <!-- <option value="1"> Credit</option> -->
                                                              @else
                                                                <!-- <option value="0"> Debit</option> -->
                                                                <option value="1" selected="selected"> Credit</option>
                                                              @endif 
                                                             
                                                             </select>
                                                          </div>
                                                          <div class="col-md-4 coa"><span class="col-md-1 control-label"></span>
                                                            <select class="form-control acct_old" id="account[]" name="account[]">
                                                              @foreach($data['coa'] as $coa)
                                                                 @if ($items['account_id'] == $coa['account_id'])
                                                                    <option selected="selected" value="{{{$coa['account_id']}}}">{{$coa['account_title']}}</option>
                                                                 @else
                                                                    <option disabled value="{{{$coa['account_id']}}}">{{$coa['account_title']}}</option>
                                                                 @endif
                                                               @endforeach
                                                            </select>
                                                          </div>
                                                          <div class="col-md-2"><span class="col-md-1 control-label"></span>
                                                             <span class="col-md-1 control-label"></span>
                                                            <!-- <div class="input-group"> -->
                                                              <!-- <span class="input-group-addon">
                                                                <input type="radio" aria-label="..." name="subject_payment" value="{{{$items['line_no']}}}">
                                                              </span> -->
                                                              <input type="text" class="form-control" readonly = "readonly" id="account_amount[]" name="account_amount[]" placeholder="Amount" value="{{{$items['line_amount']}}}">
                                                            <!-- </div> -->
                                                          </div>
                                                          <div class="col-md-4"><span class="col-md-4 control-label"></span>
                                                            <input type="text" class="form-control" readonly = "readonly" id="account_description[]" name="account_description[]" placeholder="Description" value="{{{$items['description']}}}">
                                                          </div>
                                                          <!--<div class="col-sm-1"> <span class="col-md-1 control-label"></span>
                                                            <button type="button" class="close" onclick="removeAccountRow('{{{ $items['line_no'] }}}');"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                          </div>-->
                                                      </div>

                                                      
                                                  </div>
                                              @endforeach
                                          @endif
                                        @endif

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
           
          <button type="button" id="postBtn" class="btn btn-success postBtn">Verify Receipt</button>
           

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
    



