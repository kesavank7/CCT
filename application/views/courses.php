<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>
	<div class="container">
    <div class="starter-template">
      <!-- Filter --->
      <div class="panel panel-info ovrhid">
        <div  class="panel-heading" >
          <div class="row" style="margin-top: -5px;">
            <h3 class="panel-title pull-left" style="margin-top: 8px;font-size: 20px">Courses</h3>
            <a href="<?php echo base_url("courses/add") ?>" class="btn btn-blue pull-right">Add new</a>
          </div>
        </div>

        <div class="form-group col-sm-12" style="margin-top: 10px">
          <!-- Loding Bar -->
          <div class="loading-bar" id="loading-bar">
             <div class="loading-inner">
                 <div class="progress">
                     <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45"
                          aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                         <span class="sr-only">100% Complete</span>
                     </div>
                 </div>
             </div>
          </div>
          <!-- Table Entrying -->
          <div class="no-more-tables">
            <table id="item-list" class="table table-bordered table-striped table-hover">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Link</th>
                  <th>Source</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                    foreach($all_courses as $course){
                    ?>
                      <tr>
                        <td>
                          <?php echo ($course->course_title=="" ? "" : $course->course_title ); ?>
                        </td>
                        <td>
                          <?php echo ($course->course_description=="" ? "" : $course->course_description ); ?>
                        </td>                   
                        <td>
                          <?php echo ($course->course_link=="" ? "" : $course->course_link ); ?>
                        </td> 
                        <td>
                          <?php echo ($course->course_source=="" ? "" : $course->course_source ); ?>
                        </td>
                        <td>
                          <a class="edit_course" href="<?php echo base_url("courses/edit/".$course->course_id); ?>" ><span class="glyphicon glyphicon-pencil" title="Edit" data-toggle="tooltip" data-placement="top"></span></a>
                          &nbsp; &nbsp;
                          <a onclick="return confirm('Are you sure want to delete?')" href="<?php echo base_url()."courses/delete_course/".$course->course_id; ?>" ><span class="glyphicon glyphicon-remove" title="DELETE" data-toggle="tooltip" data-placement="top"></span></a>
                        </td>

                      </tr>
                    <?php
                    }
                    ?>
              </tbody>
            </table>
          </div>
          <p>&nbsp;</p> 
        </div>
      </div>
    </div>
	</div>
  <?php include 'footer.php'; ?>
</body>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#item-list').DataTable();
    });
</script>
</html>
