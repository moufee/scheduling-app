<?php session_start(); ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <title>Development Site</title>
    <?php require('head.html') ?>
    <style>
body {
	padding-top: 50px;
	padding-bottom: 20px;
	/* [disabled]background-color: #ADD0FF; */
}	
.jumbotron{
	/* [disabled]background-color: #C8F1C0; */	
	}
	</style>
    </head>
    
    
    <body>
    <?php require('navbar.php')  ?>
    <div class="jumbotron">
      <div class="container">
        <h1>Welcome!</h1>
        <p>This is my development site.</p>
        <p><a class="btn btn-primary btn-lg" role="button" href="scheduling">Scheduling Page &raquo;</a></p>
      </div>
    </div>
    <div class="container">
      <!-- Example row of columns -->
      <!--<div class="row">
        <div class="col-md-4">
          <h2>Upload Page</h2>
          <p>A PHP-powered file upload. Soon, users will be able to upload and access their personal files. </p>
          <p><a class="btn btn-default" href="http://moufee.com/upload2" role="button">View page &raquo;</a></p>
        </div>
        <div class="col-md-4">
          <h2>Contact Page</h2>
          <p>A page that allows you to contact me.</p>
          <p><a class="btn btn-default" href="http://moufee.com/contact" role="button">View page &raquo;</a></p>
       </div>
        <div class="col-md-4">
          <h2>Chat Page</h2>
          <p>Under development: A showcase of real time updating.</p>
          <p><a class="btn btn-default" href="http://moufee.com/chat" role="button">View page &raquo;</a></p>
        </div>
      </div>
            <hr>-->
<?php //include('footer.html') ?>
      
    </div> <!-- /container -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="resources/bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>