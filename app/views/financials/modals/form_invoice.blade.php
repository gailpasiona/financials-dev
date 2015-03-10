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
                                  @if(isset($data['po_reference']))
                                    <input class="form-control" type="hidden" name="po_reference" id="po_reference" value="{{{$data['po_reference']}}}">
                                  @endif

                                  @if(!isset($data['invoice_no']))
                                    <input class="form-control" type="hidden" name="invoice_no" id="invoice_no" value="">
                                  @else
                                    <input class="form-control" type="hidden" name="invoice_no" id="invoice_no" value="{{{$data['invoice_no']}}}">
                                  @endif
                

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
                                    <label for="payee_name" class="col-md-4 control-label">Invoice Date</label>
                                    <div class="col-md-6">
                                     @if(!isset($data['invoice_date']))
                                        <input class="form-control datepicker" placeholder="Invoice Date" type="text" readonly="readonly" name="invoice_date" id="invoice_date" value="{{{date("Y-m-d")}}}">
                                      @else
                                        <input class="form-control datepicker" placeholder="Invoice Date" type="text" readonly="readonly" name="invoice_date" id="invoice_date" value="{{{$data['invoice_date']}}}">
                                      @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="register_refno" class="col-md-4 control-label">Invoice Reference</label>
                                    <div class="col-md-6">
                                      @if(!isset($data['register_refno']))
                                        <input class="form-control editable" readonly="readonly" placeholder="Invoice Reference" type="text" name="register_refno" id="payee_name" value="{{{$data['billing_reference']}}}">
                                      @else
                                        <input class="form-control editable" readonly="readonly" placeholder="Invoice Reference" type="text" name="register_refno" id="payee_name" value="{{{$data['register_refno']}}}">
                                      @endif
                                    </div>
                                </div>

                                 <div class="form-group col-md-12">
                                    <span class="col-md-3 control-label"><label><strong>Accounting Information</strong></label></span>
                                    <br />
                                    <br />
                                    <div id="credit" class="col-md-12 account_items">
                                        <div class="col-md-12 col-md-offset-0">
                                            <input class="btn btn-primary btn-block btn-sm" onclick="addAccountRow(this.form,1);" 
                                            type="button" value="Add Account(s)" />
                                        </div>
                                        <div class="col-md-12">
                                            <div>
                                              <br />
                                            </div>
                                            <div class="col-md-11">
                                              <div class="col-md-2"><span class="control-label"><strong>Type</strong></span></div>
                                              <div class="col-md-4"><span class="control-label"><strong>Account</strong></span></div>
                                              <div class="col-md-2"><span class="control-label"><strong>Amount</strong></span></div>
                                              <div class="col-md-4"><span class="control-label"><strong>Description</strong></span></div>
                                            </div>
                                        </div>

                                        @if ( isset($data['lines']))
                                          @if ( !empty($data['lines']))
                                              @foreach( $data['lines'] as $items)
                                                  <div id="AccountrowNum{{{ $items['line_no'] }}}">
  <!--                                                    <div class="col-md-3 ref_no">
                                                          <span class="col-md-1 control-label">Ref</span>
                                                          <input type="text" class="form-control" id="ref_no[]" name="ref_no[]" placeholder="Ref" value="{{{ $items['ref_no'] }}}">
                                                      </div>-->
                                                      <div class="col-md-12 lines">
                                                        <div class="col-md-11">
                                                          <div class="col-md-2"><span class="col-md-1 control-label"></span><select class="form-control" id="entry_type[]" name="entry_type[]">
                                                              @if ($items['entry_type'] == 'D')
                                                                <option value="0" selected="selected"> Debit</option>
                                                                <option value="1"> Credit</option>
                                                              @else
                                                                <option value="0"> Debit</option>
                                                                <option value="1" selected="selected"> Credit</option>
                                                              @endif 
                                                             
                                                             </select>
                                                          </div>
                                                          <div class="col-md-4 coa"><span class="col-md-1 control-label"></span>
                                                            <select class="form-control acct_old" id="account[]" name="account[]">
                                                              @foreach($data['coa_list'] as $coa)
                                                                 @if ($items['account_id'] == $coa['account_id'])
                                                                    <option selected="selected" value="{{{$coa['account_id']}}}">{{$coa['account_title']}}</option>
                                                                 @else
                                                                    <option value="{{{$coa['account_id']}}}">{{$coa['account_title']}}</option>
                                                                 @endif
                                                               @endforeach
                                                            </select>
                                                          </div>
                                                          <div class="col-md-2"><span class="col-md-1 control-label"></span>
                                                            
                                                            <input type="text" class="form-control" id="account_amount[]" name="account_amount[]" placeholder="Amount" value="{{{$items['line_amount']}}}">
                                                          </div>
                                                          <div class="col-md-4"><span class="col-md-4 control-label"></span>
                                                            <input type="text" class="form-control" id="line_description[]" name="line_description[]" placeholder="Description" value="{{{$items['description']}}}">
                                                          </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                          <div class="col-sm-1"> <span class="col-md-1 control-label"></span>
                                                            <button type="button" class="close" onclick="removeAccountRow('{{{ $items['line_no'] }}}');"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                          </div>
                                                        </div>  
                                                          
                                                      </div>

                                                      
                                                  </div>
                                              @endforeach
                                          @endif
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

<script src="{{ URL::asset('js/dyn_fields.js')}}"></script>

    
    <script>
        
        $('.datepicker').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
        });
        
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
    



