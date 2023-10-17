<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>

	<!-- Main content -->
<section class="content">
<div class="container">
    <div class="starter-template">
      <div class="panel panel-info ovrhid">
        <div class="panel-heading">
          <h3 class="panel-title">Add Course</h3>
          <div class="pull-right">

          </div>
        </div>
        <div class="row" style="margin:10px;padding:10px">
          <div class="col-xs-12">
              <form class="add_course_form" method="post" name="add_course_form" id="add_course_form">
                    
                <div class="row">
                    <div class="form-group col-12">
                      <label for="course_title">Course Title <span class="error required text-danger">*</span></label>          
                      <input type="text" id="course_title" class="form-control" name="course_title" value="" placeholder="">
                      <span class="error" id="course_title_error"></span>
                    </div>

                    <div class="form-group col-12">
                      <label for="course_description">Course Description <span class="error required">*</span></label>   
                      <textarea id="course_description" class="form-control" name="course_description"></textarea>   
                      <span class="error" id="course_description_error"></span>
                    </div>

                    <div class="form-group col-12">
                      <label for="course_link">Course Link <span class="error required">*</span></label>          
                      <input type="text" id="course_link" class="form-control" name="course_link" value="" placeholder="">
                      <span class="error" id="course_link_error"></span>
                    </div>

                    <div class="form-group col-12">
                      <label for="course_source">Course Source <span class="error required">*</span></label>          
                      <input type="text" id="course_source" class="form-control" name="course_source" value="" placeholder="">
                      <span class="error" id="course_source_error"></span>
                    </div>  
                      
                      <div class="text-center">
                        <input id="submit" type="submit" name="submit" class="btn btn-blue" value="Add">
                        
                        <a href="<?= base_url(); ?>Courses" class="btn btn-red">Cancel</a>
                        <span class="msg">
                        </span>
                      </div>
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
</div> 
</section>


   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

  $(document).ready(function() {
    // process the form
    $('.add_course_form').submit(function(course) {
      $( "#submit" ).prop( "disabled", true );     
      event.preventDefault();
      $('.loading').show();
      var formData = new FormData($(this)[0]);
      $.ajax({
          url: '<?php echo base_url('courses/add_course'); ?>',
          type: 'POST',
          data: formData,
          async: true,            
          cache: false,
          contentType: false,
          processData: false
      })
      .done(function(data) {
        $( "#submit" ).prop( "disabled", false );
        $('.loading').hide();            
        console.log(data);
        if ( ! data.success) {
          if (data.errors.course_title) {				  
				    $('#course_title_error').html('<div >' + data.errors.course_title + '</div>');
          }
          else {
            $('#course_title_error').html('');
          }

          if (data.errors.course_description) {          
            $('#course_description_error').html('<div >' + data.errors.course_description + '</div>');
          }
          else {
            $('#course_description_error').html('');
          }

        }
        else {
          $('.error').html('');
          $('.msg').html('<div class="alert alert-success">' + data.message + '</div>');
          $('.alert-success').fadeOut(5000);
          window.location = '<?php echo base_url('courses') ?>'; // redirect a user to another page
        }
      });
    });

  });
</script>
<?php include 'footer.php'; ?>
</body>
</html>