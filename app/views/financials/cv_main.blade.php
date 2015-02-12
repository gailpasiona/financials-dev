@extends('templates.master')

@section('nav')
@include('templates.nav.nav_public',array('user' => $user))
@stop
@section('sub_nav')
@include('templates.nav.user_menu')
@stop


@section('content')
<div class="page">
  <div class="container">
      <div class="col-md-12">
         <div class="messages"></div>
          <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-credit-card fa-md"></i> <strong>Cheque Voucher Requests</strong></div>
           
                 <div class="panel-table"><table id="table-rfp" class="table"></table></div>
            
                 
                  <div class="panel-footer clearfix">

        <div class="pull-right">
             <form class="form-inline">
              <div class="control-group">
                <!-- <label class="control-label">Reload Table:</label> -->
                <div class="controls">
                  
                 <button type="button" class="btn btn-md btn-success" onclick="refreshTable();"><i class="fa fa-refresh fa-lg"></i>&nbsp;<strong>Refresh Table</strong></button>
                </div>
              </div>
            </form>
        </div>

    </div>
          
          </div>
      </div>

  </div>

  <div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
</div>
@stop


@section('styles')
<link rel="stylesheet" href="{{ URL::asset('css/bootstrap-table.css')}}" />
<link rel="stylesheet" href="{{ URL::asset('css/datepicker.css')}}" />
@stop
@section('scripts')
<script src="{{ URL::asset('js/accounting.js')}}"></script>
<script src="{{ URL::asset('js/bootstrap-table.js')}}"></script>
<script src="{{ URL::asset('js/processing.js')}}"></script>
<script src="{{ URL::asset('js/datepicker.js')}}"></script>
<script>

$('#table-rfp').bootstrapTable({
                        url: "cv_requests",
                        queryParams: function query(params)
                                    {
                                        var q = {
                                            "limit": params.pageSize,
                                            "offset": params.pageSize * (params.pageNumber - 1),
                                            "search": params.searchText,
                                            //
                                            "type" : $("#reload_type").val(),
                                            //
                                            "name": params.sortName,
                                            "order": params.sortOrder
                                        };
                     
                                        return q;
                                    },
                        pagination: true,
                        columns: [{
                            field: 'cv_number',
                            title: 'CV Request No',
                            align: 'left',
                            valign: 'center',
                            sortable: true,
                            // width: 200/2,
                        },{
                            field: 'rfp',
                            title: 'Payee',
                            align: 'left',
                            valign: 'canter',
                            formatter: json2string,
                            sortable: 'false'
                             // width: 200/2,
                        },{
                            field: 'created_at',
                            title: 'Date Requested',
                            align: 'left',
                            valign: 'center',
                            sortable: true,
                            // width: 200/5,
                        },{
                            field: 'amount',
                            title: 'Amount',
                            align: 'left',
                            valign: 'center',
                            //valign: 'bottom',
                            // width: 200/5,
                            formatter: amountFormatter,
                            sortable: false
                        },{
                            field: 'cheque_number',
                            title: 'Cheque No',
                            align: 'center',
                            valign: 'center',
                            sortable: false
                        },{
                            field: 'description',
                            title: 'Description',
                            align: 'left',
                            valign: 'center',
                            sortable: false
                        },{
                            field: 'cv_number',
                            title: 'Action',
                            align: 'center',
                            valign: 'center',
                            formatter: useractionFormatter,
                            sortable: false
                        }]});
function json2string(obj){
    return obj.register.reference.supplier.supplier_name;

}
function amountFormatter(value){
    return accounting.formatMoney(value,"Php ");;
}

function useractionFormatter(value,row){
    var url = "";

    if(row.approved == 'Y'){
        
            url = "cv/post/" + encodeURIComponent(value);
            return '<a class="btn btn-sm btn-warning" href="'+ url +'" data-toggle="modal" data-target="#modal_form"><strong>Post<strong></a>';
        
        
    }
    else{
        url = 'cv/' + encodeURIComponent(value) + '/edit';
        return '<a class="btn btn-sm btn-warning" href="'+ url +'" data-toggle="modal" data-target="#modal_form"><strong>Edit</strong></a>&nbsp;' + 
                 '<button type="button" class="btn btn-sm btn-default btn-approval" onclick="invoice_action(\'' + value + '\');"><strong>Approve Request</strong></button>';
    }
        
}
function refreshTable(){
	$('#table-rfp').bootstrapTable('refresh',{});
}
function invoice_action(value){
    console.log(value);
   myApp.showPleaseWait();
    var request = $.ajax({
            url: 'cv/approval',
            type: "POST",
            data: {cv: value},
            //dataType: "json"
    });
            
    request.done(function(data){
    myApp.hidePleaseWait();
    $('.table').bootstrapTable('refresh');
    // $('#table-records').bootstrapTable('refresh',{url: 'bp_records'});
    show_message_in_parent(data);

    });
    request.fail(function(jqXHR, textStatus){
     myApp.hidePleaseWait();
     $(".messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Succeeded!</strong> <br />System Error, please contact your System Administrator </div>');

    });
}

function show_message_in_parent(data){
            // console.log(data);
            $("div").removeClass("has-error");
            $( ".message_content" ).remove();//remove first if exists
             var prompt = "<br />";

             if(data.status == 'success_error'){
                $.each(data.message, function(key,value) {
                    prompt += value + "<br />";
                    $('.' + key).addClass("has-error");
                 });
                 $(".messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Errors Occured!</strong> '+prompt+' </div>');
             }
             else if(data.status == 'success_failed'){
                  $(".messages").append('<div class="message_content alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Succeeded!</strong> <br />'+data.message+' </div>');
             }
             else{
                  $(".messages").append('<div class="message_content alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert">\n\
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\n\
                    <strong>Transaction Succeeded!</strong> <br />'+data.message+' </div>');
             }
             
      }

$('.modal').on('hidden.bs.modal', function () {
  $(this).removeData('bs.modal');
  $('.table').bootstrapTable('refresh');
  $(this).empty();
});
</script>
@stop
