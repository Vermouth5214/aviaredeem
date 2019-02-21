<?php
    $userinfo = Session::get('userinfo');
	$avatar = url('img/noprofileimage.png');
	if (isset($userinfo['avatar'])){
		$avatar = url($userinfo['avatar']);
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="all,follow">
        <meta name="description" content="<?=getData('web_description');?>">
        <meta name="author" content="ODWin">
        <title><?=getData('web_title');?> | @yield('title')</title>        
		<link rel="apple-touch-icon" href="<?=url('img/avian.png');?>">
		<link rel="shortcut icon" href="<?=url('img/avian.png');?>">
        <!-- Bootstrap -->
        <link href="<?=url('vendors/bootstrap/dist/css/bootstrap.min.css');?>" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="<?=url('vendors/font-awesome/css/font-awesome.min.css');?>" rel="stylesheet">
        <!-- NProgress -->
        <link href="<?=url('vendors/nprogress/nprogress.css');?>" rel="stylesheet">
        <!-- bootstrap-wysiwyg -->
        <link href="<?=url('vendors/google-code-prettify/bin/prettify.min.css');?>" rel="stylesheet">
        <!-- jQuery custom content scroller -->
        <link href="<?=url('vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css');?>" rel="stylesheet"/>
        <!-- bootstrap-datetimepicker -->
        <link href="<?=url('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css');?>" rel="stylesheet">
        <!-- Datatables -->
        <link href="<?=url('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css');?>" rel="stylesheet">
        <link href="<?=url('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css');?>" rel="stylesheet">
        <link href="<?=url('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css');?>" rel="stylesheet">
        <link href="<?=url('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css');?>" rel="stylesheet">
        <link href="<?=url('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css');?>" rel="stylesheet">
		<!-- colorbox -->
		<link href="<?=url('vendors/colorbox/colorbox.css');?>" rel="stylesheet">
        <!-- blueimp -->
        <link href="<?=url('vendors/blueimp/jquery.fileupload-ui.css');?>" rel="stylesheet">
        <!-- Custom Theme Style -->
        <link href="<?=url('build/css/custom.min.css');?>" rel="stylesheet">
        <link href="<?=url('/css/style.back.css');?>" rel="stylesheet">
        @yield('css')

        <!-- jQuery -->
        <script src="<?=url('vendors/jquery/dist/jquery.min.js');?>"></script>
    </head>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <div class="col-md-3 left_col">
                    <div class="left_col scroll-view">
                        <div class="navbar nav_title" style="border: 0;">
                            <a href="<?=url('/')?>" class="site_title"><img src="<?=url('img/avian.png');?>" width="50px;"> <span>&nbsp;<img src="<?=url(getData('logo'));?>"; style="max-width:120px;"></span></a>
                        </div>
                        <div class="clearfix"></div>
                        <!-- menu profile quick info -->
                        <div class="profile clearfix">
                            <div class="profile_pic">
                                <img src="<?=$avatar;?>" alt="..." class="img-circle profile_img">
                            </div>
                            <div class="profile_info">
                                <span>Welcome,</span>
                                <h2><?=$userinfo['uname']?></h2>
                            </div>
                        </div>
                        <!-- /menu profile quick info -->
                        <br />
                        <!-- sidebar menu -->
                        @include('backend.partials.menu')
                        <!-- /sidebar menu -->
                    </div>
                </div>
                <!-- top navigation -->
                <div class="top_nav">
                    <div class="nav_menu">
                        <nav>
                            <div class="nav toggle">
                                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                            </div>
                            <ul class="nav navbar-nav navbar-right">
                                <li class="">
                                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <img src="<?=$avatar;?>" alt=""><?=$userinfo['uname'];?>
                                        <span class=" fa fa-angle-down"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                                        <?php
                                            if (env('APP_STATUS') == "local"):
                                        ?>  
                                        <li><a href="<?=url('http://localhost/AVIAN/customercare/public/logout');?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                        <?php
                                            endif;
                                            if (env('APP_STATUS') == "prod"):
                                        ?>
                                        <li><a href="<?=url('https://www.avianbrands.com/customercare/logout');?>"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                                        <?php
                                            endif;
                                        ?>
                                    </ul>
                                </li>
                                <li>
                                    <?php
                                        if (env('APP_STATUS') == "local"):
                                    ?>  
                                    <a href="<?=url('http://localhost/AVIAN/customercare/public/portal');?>">
                                        <i class="fa fa-home"> Menu Utama </i>
                                    </a>
                                    <?php
                                        endif;
                                        if (env('APP_STATUS') == "prod"):
                                    ?>
                                    <a href="<?=url('https://www.avianbrands.com/customercare/portal');?>">
                                        <i class="fa fa-home"> Menu Utama </i>
                                    </a>
                                    <?php
                                        endif;
                                    ?>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- /top navigation -->
                <!-- page content -->
                <div class="right_col" role="main">
                    <div class="">
                        @yield('content')
                    </div>
                </div>
                <!-- /page content -->
                <!-- footer content -->
                @include('backend.partials.footer')
                <!-- /footer content -->
            </div>
        </div>
        <!-- Bootstrap -->
        <script src="<?=url('vendors/bootstrap/dist/js/bootstrap.min.js');?>"></script>
        <!-- FastClick -->
        <script src="<?=url('vendors/fastclick/lib/fastclick.js');?>"></script>
        <!-- NProgress -->
        <script src="<?=url('vendors/nprogress/nprogress.js');?>"></script>
        <!-- jQuery custom content scroller -->
        <script src="<?=url('vendors/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js');?>"></script>
        <!-- Datatables -->
        <script src="<?=url('vendors/datatables.net/js/jquery.dataTables.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-buttons/js/dataTables.buttons.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-buttons/js/buttons.flash.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-buttons/js/buttons.html5.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-buttons/js/buttons.print.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-keytable/js/dataTables.keyTable.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-responsive/js/dataTables.responsive.min.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js');?>"></script>
        <script src="<?=url('vendors/datatables.net-scroller/js/dataTables.scroller.min.js');?>"></script>
        <!-- Colorbox -->
        <script src="<?=url('vendors/colorbox/jquery.colorbox.js');?>"></script>
        <!-- blueimp -->
		<script src="<?=url('vendors/blueimp/js/vendor/jquery.ui.widget.js');?>"></script>
		<script src="<?=url('vendors/blueimp/js/tmpl.min.js');?>"></script>
		<script src="<?=url('vendors/blueimp/js/load-image.min.js');?>"></script>
		<script src="<?=url('vendors/blueimp/js/canvas-to-blob.min.js');?>"></script>
		<script src="<?=url('vendors/blueimp/js/jquery.iframe-transport.js');?>"></script>
		<script src="<?=url('vendors/blueimp/js/jquery.fileupload.js');?>"></script>
		<script src="<?=url('vendors/blueimp/js/jquery.fileupload-fp.js');?>"></script>
        <script src="<?=url('vendors/blueimp/js/jquery.fileupload-ui.js');?>"></script>
        <!-- bootstrap-daterangepicker -->
        <script src="<?=url('vendors/moment/min/moment.min.js');?>"></script>
        <script src="<?=url('vendors/bootstrap-daterangepicker/daterangepicker.js');?>"></script>
        <!-- bootstrap-datetimepicker -->    
        <script src="<?=url('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');?>"></script>
        <!-- bootstrap-wysiwyg -->
        <script src="<?=url('vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js');?>"></script>
        <script src="<?=url('vendors/jquery.hotkeys/jquery.hotkeys.js');?>"></script>
        <script src="<?=url('vendors/google-code-prettify/src/prettify.js');?>"></script>
        <!-- Custom Theme Scripts -->
        <script src="<?=url('build/js/custom.min.js');?>"></script>
        <script src="<?=url('js/jquery-ui.js');?>"></script>
        @include('backend.partials.script')
		@yield('script')
    </body>
</html>