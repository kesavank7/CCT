<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>
<div class="container">
    <div class="starter-template">
        <!-- Filter --->
        <div class="panel panel-info ovrhid">
            <div class="panel-heading">
                <h3 class="panel-title">Courses</h3>
            </div>
            <div class="form-group col-sm-12">
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
                    <div id="no-more-tables">
                        <table id="item-list" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
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
        jQuery('#item-list').DataTable({
            "ajax": {
                // url: "/get_items",
                url: "<?php echo base_url(); ?>courses/get_items",
                type: 'GET'
            },
        });
    });
</script>
</html>
