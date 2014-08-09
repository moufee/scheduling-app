<!doctype html>
<html>
<head>
<?php include('head.html'); ?>
<style>
body {padding-top:60px;}

</style>
<title>Dropbox</title>
</head>
<?php include('navbar.php'); ?>
<body>
<div class="container">
<h1>Dropbox</h1>

<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if(isset($_REQUEST['todo'])&&$_REQUEST['todo']=='start'){
exec('sudo /bin/dropbox.py start',$startOutput);
echo $startOutput[0];
}
if(isset($_REQUEST['todo'])&&$_REQUEST['todo']=='grunt') {
    exec('sudo Dropbox/Website/html/english/grunt.bat',$testasdf);
    echo 'running grunt';
    echo $testasdf[0];
}
?>


<?php $status = shell_exec('sudo /bin/dropbox.py status'); ?>
<p>Status: <?php var_dump($status); ?></p>
<?php if(strlen($status) == 23){ ?>
	
<form method="post" action="dropbox.php">
<input type="hidden" name="todo" value="start">
<input type="submit" class="btn btn-primary" value="Start Dropbox">
</form>
<?php } ?>




<br><br>

</body>
</html>