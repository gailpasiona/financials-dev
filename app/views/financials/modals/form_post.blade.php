<div class="modal-dialog modal-wide">
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
                                    <span class="col-md-3 control-label"><label><strong>Accounting Information</strong></label></span>
                                    <div id="credit" class="col-md-12 account_items">
                                       <!--  <div class="col-md-12 col-md-offset-0">
                                            <input class="btn btn-primary btn-block btn-sm" onclick="addAccountRow(this.form,1);" 
                                            type="button" value="Add Account(s)" />
                                        </div> -->
                                        <div class="col-md-12">
                                            <div>
                                              <br />
                                            </div>
                                            <div class="col-md-9">
                                              <div class="col-md-3"><span class="control-label"><strong>Type</strong></span></div>
                                              <div class="col-md-5"><span class="control-label"><strong>Account</strong></span></div>
                                              <div class="col-md-4"><span class="control-label"><strong>Amount</strong></span></div>
                                              
                                            </div>
                                            <div class="col-md-3"><div class="col-md-3"><span class="control-label"><strong>Description</strong></span></div></div>
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
                                                        <div class="col-md-9">
                                                          <div class="col-md-3"><span class="col-md-1 control-label"></span><select class="form-control" id="entry_type[]" name="entry_type[]">
                                                              @if ($items['entry_type'] == 'D')
                                                                <option value="0" selected="selected"> Debit</option>
                                                                <!-- <option value="1"> Credit</option> -->
                                                              @else
                                                                <!-- <option value="0"> Debit</option> -->
                                                                <option value="1" selected="selected"> Credit</option>
                                                              @endif 
                                                             
                                                             </select>
                                                          </div>
                                                          <div class="col-md-5 coa"><span class="col-md-1 control-label"></span>
                                                            <select class="form-control acct_old" id="account[]" name="account[]">
                                                              @foreach($data['coa_list'] as $coa)
                                                                 @if ($items['account_id'] == $coa['account_id'])
                                                                    <option selected="selected" value="{{{$coa['account_id']}}}">{{$coa['account_title']}}</option>
                                                                 @endif
                                                               @endforeach
                                                            </select>
                                                          </div>
                                                          <div class="col-md-4">
                                                            <span class="col-md-4 control-label"></span>
                                                            <span class="col-md-4 control-label"></span>
                                                            <div class="input-group">
                                                              <span class="input-group-addon">
                                                                <input type="radio" aria-label="..." name="subject_payment" value="{{{$items['line_no']}}}">
                                                              </span>
                                                              <input readonly = "readonly" type="text" class="form-control" id="account_amount[]" name="account_amount[]" placeholder="Amount" value="{{{$items['line_amount']}}}">
                                                            </div>
                                                          </div>
                                                          
                                                        </div>
                                                        <div class="col-md-3"><span class="col-md-4 control-label"></span>
                                                            <input readonly = "readonly" type="text" class="form-control" id="line_description[]" name="line_description[]" placeholder="Description" value="{{{$items['description']}}}">
                                                        </div>
                                                        <!-- <div class="col-md-1">
                                                          <div class="col-sm-1"> <span class="col-md-1 control-label"></span>
                                                            <button type="button" class="close" onclick="removeAccountRow('{{{ $items['line_no'] }}}');"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                          </div>
                                                        </div>   -->
                                                          
                                                      </div>

                                                      
                                                  </div>
                                              @endforeach
                                          @endif
                                        @endif

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
         
          <button type="button" id="submitBtn" class="btn btn-primary submitBtn">Validate</button>

      </div>
   </div>
</div>

<!-- <script src="{{ URL::asset('js/dyn_fields.js')}}"></script> -->

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
