<?php
  session_start();
  date_default_timezone_set('Asia/Taipei');
  include 'inc/config.php';
  include 'inc/common.php';
  // if ($_SERVER["REMOTE_ADDR"] != '220.135.235.10' && $_SERVER["REMOTE_ADDR"] != '10.41.68.40') die('測試模式'.$_SERVER["REMOTE_ADDR"]);  
  //==========================================================================
  // GLOBAL VARIABLE
  // =========================================================================
  $go = 'html';
  $now_date=date('YmdH');
  $menu_data='';   //LEFT-MENU
  if (isset($_GET['do']))
      $do = preg_replace('/[^a-zA-Z0-9_]/','',$_GET['do']); //只允許英文和數字
  else
      $do='';
      
  if (isset($_GET['op']))
      $op = preg_replace('/[^a-zA-Z0-9_]/','',$_GET['op']); //只允許英文和數字
  else
      $op='';  
  
  $ro='guest';

  if (isset($_SESSION['log_admin'])) {
      $ro ='admin';
      if ($do=='logout') {
        session_destroy();
        $content = redirect("已經登出",'index.php',1);
      }
      if (!$do) $do='admin';
  }else {
     if (!$do) $do='news';
  }
  if ($do=='down') {
     include 'inc/download.php';
     exit;
  }
  $modscript='';
  ob_start();
  switch ($do) {
  case 'logout':
     break;
  case 'news':
  case 'qa':
  case 'cate':
  case 'menu':
  case 'years':
  case 'file':
  case 'pages':
  case 'user':
  case 'school':
  case 'career':
  	//include 'guest/over.php';
    include 'mod/'.$do.'/index.php';
  	break;
  case 'password':
    include 'admin/pwd.php';
    break;
  case 'mail':
   include 'admin/mail.php';
  case 'admin': 	   
    include 'admin/index.php';
    break;
  default :
  	include 'mod/news/index.php';
}
 $content.=ob_get_contents();
 ob_end_clean(); 

  header("Content-Type:text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>桃園市校長主任儲訓網</title>
   <meta name="keywords" content="校長主任，儲訓">
   <meta name="description" content="桃園市校長主任儲訓網">
   <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
   <script src="vendor/jquery/jquery.min.js"></script>
   <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
   <SCRIPT src='inc/ckeditor/ckeditor.js' type="text/javascript"></SCRIPT>
   <?php echo $modscript; ?>
<style>
.navbar-dark .navbar-nav .nav-link {
    color: rgba(255,255,255,.9);
}
</style>
</head>
<body>
<div class="container">
<?php
if ($go=='goto') {
  echo $content;
}elseif ($go=='html') {

   $mlist = ezList("SELECT * FROM `menu` WHERE stat>0 AND pid=0 order by `sort`,id"); //主層

   $menu='';
   foreach($mlist as $var) {
      $pid=$var['id'];
      $info = rsInfo('menu',"id = $pid ");
      $arr  =rsList('menu',"stat>0 AND pid = $pid ORDER BY `sort`,`id`");
      if (count($arr)==0) {
         $menu .="
           <li class=\"nav-item\">
            <a class=\"nav-link\" href=\"{$info['link']}\">{$info['title']}</a>
          </li>
         ";
      
      } else {
     
        $menu.="
          <li class=\"nav-item dropdown\">
            <a class=\"nav-link dropdown-toggle\" href=\"{$info['link']}\" id=\"dropdown{$info['id']}\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">{$info['title']}</a>
            <div class=\"dropdown-menu\" aria-labelledby=\"dropdown{$info['id']}\">";
     
           foreach($arr as $var2) {
              $menu.="<a class=\"dropdown-item\" href=\"{$var2['link']}\">{$var2['title']}</a>";
           }
         
         $menu.="</div>
          </li>
        ";
      }
   }
  
?>
<div ><a name='#top'><p align='center'><img id='logo' src='img/logo.png' width='820px' class="img-responsive img-rounded rounded" alt="Responsive image"></p> </a></div>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded" style="margin-top:-10px;">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample08" aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample08">
        <ul class="navbar-nav">
          <?php
            echo $menu;
           ?> 
           <li class="nav-item">
            <a class="btn btn-outline-danger my-2 my-sm-0" href="?do=admin">管理</a>
          </li>     
        </ul>
      </div>
    </nav>
 
<div id='content' style="min-height:480px;background-color:#FFFFFF;" align="center">

<?php 
   // $do='news' ;

   if (!$menu_data && !aid()) {
     echo $content;
   } else {
?>   
<div class="row">
  <div class="col-md-2">
    <div>
       <?php echo $menu_data ?>
    </div>
    <?php if ($admin=aid()) {
       $admin_yearsid = rsInfo('admin',"uname='$admin'",'yearsid');
       if ($admin_yearsid==0) {
    ?>
    <style>
     .list-group-item {
       padding:5px;
    }
    </style>
    <div class="list-group table-of-contents">
     <span class="list-group-item bg-primary" style='color:white' >管理選單</span>
    
    
    <a href="?do=logout" class="list-group-item">登出</a>
    <a href="?do=password" class="list-group-item">更改密碼</a>
    <a href="?do=mail" class="list-group-item">設定Mail</a>

     
     <a href="?do=news" class="list-group-item">最新消息管理</a>
     <a href="?do=qa" class="list-group-item">Q&A管理</a>
     <a href="?do=cate" class="list-group-item">分類管理</a>
     <a href="?do=menu" class="list-group-item">選單管理</a>
     <a href="?do=years" class="list-group-item">期別管理</a>
     <a href="?do=pages" class="list-group-item">內容管理</a>
     <a href="?do=user" class="list-group-item">人員管理</a>
     <a href="?do=school" class="list-group-item">學校管理</a>
     <a href="?do=career" class="list-group-item">事求人管理</a>
    
     </div> 
    <?php } else { ?>
    <div class="list-group table-of-contents">
     <span class="list-group-item bg-primary" style='color:white' >管理選單</span>
     <a href="?do=logout" class="list-group-item">登出</a>
     <a href="?do=pages" class="list-group-item">內容管理</a>
     </div>        
       
    <?php } } ?>
  </div>
  <div class="col-md-10">
  <?php  echo $content; ?>
  </div>
</div>    
   
<?php   
   }
?>

</div>

<div align="center">
<table border="0" align="center" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;min-width:280px;max-width:840px;" width="95%">
  <tr>
    <td align="center" class="center" style="border-radius: 5px; background: #CC9900;" > 桃園市政府教育局版權所有 <span id='scroll'></span></td>
  </tr>
</table>
</div>

<script>
 $(function () {
  $(window).scroll(function () {
    var scrollVal = $(this).scrollTop();
    $("#scroll").text(scrollVal);
  });
});
</script>
<?php } ?>

</div>
</body>
</html>


