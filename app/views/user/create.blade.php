@extends('templates.master')

@section('nav')
@include('templates.nav.nav_public',array('admin' => true))
@stop
@section('sub_nav')
@include('templates.nav.admin_menu')
@stop


@section('content')
<div class="page">
  <div class="container">
      <div class="col-md-6">
          <div class="panel panel-warning">
            <div class="panel-heading">System Users</div>
           
                 <div class="panel-table"><table id="table-users" class="table"></table></div>
            
                 
                  <div class="panel-footer clearfix">

        <div class="pull-right">

            <a href="{{ route('user_form')}}" class="btn btn-warning" data-toggle="modal" data-target="#users_modal">Create New</a>

        </div>

    </div>
          
          </div>
      </div>

      <div class="col-md-6">
          <div class="panel panel-warning">
            <div class="panel-heading">System Roles</div>
           
                 <div class="panel-table"><table id="table-roles" class="table"></table></div>
            
                 
                  <div class="panel-footer clearfix">

                      <div class="pull-right">

                          <a href="{{route('role_form')}}" class="btn btn-warning" data-toggle="modal" data-target="#roles_modal">Create New</a>

                      </div>

                  </div>
          
          </div>
      </div>
        <!--modals -->
      <div class="modal fade" id="users_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
      <div class="modal fade" id="users_edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
      <div class="modal fade" id="roles_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
      <div class="modal fade" id="roles_attach_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>

  </div>
</div>
@stop


@section('styles')
<link rel="stylesheet" href="{{ URL::asset('css/bootstrap-table.css')}}" />
@stop
@section('scripts')
<script src="{{ URL::asset('js/bootstrap-table.js')}}"></script>
<script>
$('.subnavbar').affix({
      offset: {
        top: $('#navtop').height()
      }
});

            $(function() {
        // Highlight the active nav link.
            var url = window.location.pathname;
            var filename = url.substr(url.lastIndexOf('/') + 1);
            $('.subnavbar a[href$="' + filename + '"]').parent().addClass("active");
        });

$('#table-users').bootstrapTable({
                        url: "users/list",
                        showRefresh: true,
                        //showColumns: true,
                        search: true,
                       //    showToggle: true,
                        pagination: true,
                        columns: [{
                            field: 'full_name',
                            title: 'Name',
                            align: 'left',
                            sortable: true,
                            //width: 200/4,
                        }, {
                            field: 'username',
                            title: 'Username',
                            align: 'left',
                            //valign: 'bottom',
                            //width: 200/4,
                            sortable: true
                        }, {
                            field: 'roles',
                            title: 'Roles',
                            align: 'left',
                            //valign: 'bottom',
                            //width: 200/3,
                            formatter: json2string,
                            sortable: false
                        },{
                            field: 'id',
                            title: 'Action',
                            align: 'center',
                            //valign: 'bottom',
                            //width: 200/3,
                            formatter: useractionFormatter,
                            sortable: false
                        }]});
$('#table-roles').bootstrapTable({
                        url: "roles/list",
                        //showRefresh: true,
                       // showColumns: true,
                       // search: true,
                       //    showToggle: true,
                        pagination: true,
                        columns: [{
                            field: 'name',
                            title: 'Role Name',
                            align: 'left',
                            sortable: true,
                            //width: 200/4,
                        }, {
                            field: 'id',
                            title: 'Action',
                            align: 'center',
                            //valign: 'bottom',
                            //width: 200/3,
                            formatter: roleactionFormatter,
                            sortable: false
                        }]});

function json2string(obj){
    var roles = "";
    jQuery.each(obj, function(i, val) {
      var item = val.name + ",";
      roles = roles + item;
    });
    return roles;
}
function useractionFormatter(value){
    return '<a href="users/modify/'+ value +'" data-toggle="modal" data-target="#users_edit_modal" data-tooltip="tooltip" data-placement="top" title="Modify"><i class="fa fa-wrench fa-lg"></i></a>';
}
function roleactionFormatter(value){
    return '<a href="roles/users/'+ value +'" data-toggle="modal" data-target="#roles_attach_modal" data-tooltip="tooltip" data-placement="top" title="Attach Users"><i class="fa fa-users fa-lg"></i></a>' + 
           '&nbsp;<a href="roles/members/'+ value +'" data-toggle="modal" data-target="#roles_attach_modal" data-tooltip="tooltip" data-placement="top" title="Detach Users"><i class="fa fa-eraser fa-lg"></i></a>' +
           '&nbsp;<a href="roles/permissions/'+ value +'" data-toggle="modal" data-target="#roles_attach_modal" data-tooltip="tooltip" data-placement="top" title="Role Permissions"><i class="fa fa-unlock fa-lg"></i></a>';
}
function roledetachFormatter(value){
    return '';
}
$('.modal').on('hidden.bs.modal', function () {
 $(this).removeData('bs.modal');
  $('.table').bootstrapTable('refresh');
  $(this).empty();
});
</script>
@stop
