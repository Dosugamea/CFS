<?php 
if($_GET['id']==11||$_GET['id']==12) {
  header('Location: /webview.php/maintenance/bundleUpdate');
}
//此用户已被封禁
elseif($_GET['id']==13) {
  echo '<h1>您的账号已被封禁：';
  try{
    $stmt=$mysql->prepare('SELECT username FROM authorize WHERE user_id=?');
    $stmt->execute([$_SESSION['server']['HTTP_USER_ID']]);
    $username=$stmt->fetchColumn();
    $banned=$mysql->query("select msg from banned_user where user='{$_SESSION['server']['HTTP_USER_ID']}' or user='{}'")->fetchColumn();
    if($banned) {
      echo $banned;
    }
  } catch(PDOException $e) {}
  echo '</h1>';
} 
//サポート
elseif($_GET['id']==5) {
  header('Location: /webview.php/announce/info');
}
//利用规约
elseif($_GET['id']==1) {
  header('Location: /webview.php/tos/read');
}
else{
  header('Location: /webview.php');
}