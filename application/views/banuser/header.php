<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>志工管理系統</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/css/ionicons.min.css">
    <!-- <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/plugins/datatables/dataTables.bootstrap.css"> -->
    <!-- <link href="https://cdn.datatables.net/v/bs/dt-1.13.6/rg-1.4.0/datatables.min.css" rel="stylesheet"> -->
    <link href="https://cdn.datatables.net/v/bs/dt-1.13.6/b-2.4.1/rg-1.4.0/datatables.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo base_url();?>resource/artdialog/css/ui-dialog.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/plugins/select2/select2.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/bootstrap-switch/css/bootstrap3/bootstrap-switch.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/fileinput/css/fileinput.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/jquery.tagsinput/jquery.tagsinput.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resource/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <!-- awesomplete -->
    <link rel="stylesheet" href="<?php echo base_url();?>resource/css/awesomplete.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery 2.1.3 -->
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/jQueryUI/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/arttemplate/template-native.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.form.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/bootbox.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/toastr/toastr.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/fastclick/fastclick.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/autosize/autosize.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/momentjs/moment.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/momentjs/locales.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>resource/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/select2/select2.full.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/fullcalendar/fullcalendar.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/fullcalendar/lang-all.js"></script>

    <!-- <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/datatables/dataTables.bootstrap.min.js"></script> -->

    <!-- <script type="text/javascript" src="https://cdn.datatables.net/rowgroup/1.4.0/js/dataTables.rowGroup.min.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/v/bs/dt-1.13.6/rg-1.4.0/datatables.min.js"></script> -->
    <script src="https://cdn.datatables.net/v/bs/dt-1.13.6/rg-1.4.0/datatables.min.js"></script>
    
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/dist/js/app.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/sammy/lib/min/sammy-latest.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/server.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/fileinput/js/fileinput.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/jquery.tagsinput/jquery.tagsinput.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/awesomplete.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>resource/js/bootstrap-popper.min.js"></script>

    <style type="text/css">

    .skin-red .main-header .navbar {
      background-color: #FFF !important ;
    }
    .skin-red .main-header .navbar .sidebar-toggle {
      color: #000 !important ;
    }
    .skin-red .main-header .logo {
      font-weight: 600 !important ;
    }
    .sidebar-menu>li {
      padding: 3px 0px !important ;
    }
    .main-sidebar, .left-side {
      padding-top: 65px !important ;
    }
    .skin-red .wrapper, .skin-red .main-sidebar, .skin-red .left-side {
      background-color: rgb(43,48,65) !important ;
    }
    </style>


  </head>
  <body class="skin-red fixed">
    <div class="wrapper">
      
      <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo base_url();?>volunteer_manage" class="logo">志工管理系統</a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <!-- Navbar Right Menu -->
         
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
      
      
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu" data-widget="tree">
            <!-- <li class="header">MAIN NAVIGATION</li> -->
            <li>
              <a href="#">
                <span>A.排班時間設定</span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url();?>volunteer_manage/scheduling_setup"><span style="margin-left: 10px">班務</span></a></li>
              <?php
                for ($i=0;$i<count($list);$i++) { 
                  echo '<li><a href="'.base_url('volunteer_manage/scheduling_setup_others/'.$list[$i]->id).'"><span style="margin-left: 10px">'.$list[$i]->name.'</span></a></li>';
                }
              ?>
              </ul>
            </li>
            
            <li>
              <a href="#">
                <span>B.報名時間設定</span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url('volunteer_manage/apply_time_setting')?>"><span style="margin-left: 10px">班務</span></a></li>
                <?php
                for ($i=0;$i<count($list);$i++) { 
                  echo '<li><a href="'.base_url('volunteer_manage/apply_time_setting_others/'.$list[$i]->id).'"><span style="margin-left: 10px">'.$list[$i]->name.'</span></a></li>';
                }
              ?>
              </ul>
            </li>

            <li>
              <a href="#">
                <span>C.志工選員</span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url();?>volunteer_select"><span style="margin-left: 10px">班務</span></a></li>
                <?php
                for ($i=0;$i<count($list);$i++) { 
                  echo '<li><a href="'.base_url('volunteer_select/volunteer_select_others/'.$list[$i]->id).'"><span style="margin-left: 10px">'.$list[$i]->name.'</span></a></li>';
                }
              ?>
              </ul>
            </li>

            <li>
              <a href="<?php echo base_url();?>volunteer_manage/publish">
                <span>D.公告檢視</span>
              </a>
            </li>

            <li>
              <a href="#">
                <span>E.服務時數統計表</span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url();?>Volunteer_sign_report"><span style="margin-left: 10px">志工服務簽到退紀錄表</span></a></li>
                <!-- <li><a href="<?php echo base_url();?>Volunteer_sign_report/volunteer_traffic_report"><span style="margin-left: 10px">志工餐點與交通補助清冊</span></a></li> -->
              </ul>
            </li>

            <li>
              <a href="<?php echo base_url();?>Volunteer_card_log">
                <span>F.志工刷卡紀錄管理</span>
              </a>
            </li>

            <li>
              <a href="<?php echo base_url();?>change_log">
                <span>G.異動通知（錄取及取消）</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url();?>volunteer_manage/set_report_stage">
                <span>H.設定報名階段名額</span>
              </a>
<!--               <ul class="treeview-menu">
                <li><a href="<?php echo base_url();?>volunteer_manage/volunteer_category_manage"><span style="margin-left: 10px">志工種類管理</span></a></li>
                <li><a href="<?php echo base_url();?>volunteer_manage/set_report_stage"><span style="margin-left: 10px">班務-設定報名階段名額</span></a></li>
              </ul>
 -->            </li>

            <li>
              <a href="<?php echo base_url('volunteer_manage/long_range_user') ?>">
                <span>I.長期管理班設定</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url('volunteer_manage/subsidy_list') ?>">
                <span>J.志工補助清冊</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url('volunteer_manage/checkout_to_user') ?>">
                <span>K.切換志工帳號</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url();?>evaluation">
                <span>L.績效考核</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url('volunteer_manage/evaluation_leader_user') ?>">
                <span>M.績效考核組長設定</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url();?>volunteer_manage/manage_admin">
                <span>N.類別承辦人</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url();?>volunteer_manage/user_list">
                <span>O.志工通訊錄</span>
              </a>
            </li>
            <li>
              <a href="<?php echo base_url();?>banned/ban_users">
                <span>P.停權設定</span>
              </a>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
       
        
        <!-- Main content -->
        <section class="content">
          <!-- Info boxes -->