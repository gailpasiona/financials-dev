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
		<div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>
                                        10
                                    </h3>
                                    <p>
                                        Open/Aging Invoices
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a class="small-box-footer" href="#">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>
                                      14
                                        <!-- 53<sup style="font-size: 20px">%</sup> -->
                                    </h3>
                                    <p>
                                        New Billings
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a class="small-box-footer" href="#">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                        27
                                    </h3>
                                    <p>
                                        For Payments
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a class="small-box-footer" href="#">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                        12
                                    </h3>
                                    <p>
                                        Cancelled Invoices
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a class="small-box-footer" href="#">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                    </div>
	</div>
</div>
@stop

@section('styles')
<link rel="stylesheet" href="{{ URL::asset('css/dashboard.css')}}" />
<!-- <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css"/> -->
@stop

@section('scripts')

<script>


        //     $(function() {
        // // Highlight the active nav link.
        //     var url = window.location.pathname;
        //     var filename = url.substr(url.lastIndexOf('/') + 1);
        //     $('.subnavbar a[href$="' + filename + '"]').parent().addClass("active");
 
        // });

    // $(function() {
    //     // Highlight the active nav link.
    //         var url = window.location.pathname;
    //         var filename = url.substr(url.lastIndexOf('/') + 1);
    //         if ( $('.subnavbar a[href$="' + filename + '"]').parents(".mainnav").length == 1 ) { 
    //             $('.subnavbar a[href$="' + filename + '"]').parents("li").addClass("active");
    //            // YES, the child element is inside the parent

    //         } else {
    //          $('.subnavbar a[href$="' + filename + '"]').parent().addClass("active");
    //            // NO, it is not inside
    //        }
    //    });

     
</script>
@stop