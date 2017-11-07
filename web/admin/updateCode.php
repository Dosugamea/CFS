<pre>
<?php
include_once("includes/check_admin.php");
exec('cd ../.. && git pull origin master', $output, $return_val); 
print_r($output);
?>
</pre>
<a href='javascript:history.go(-1);'>返回上一页</a>