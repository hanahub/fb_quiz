<?php
    global $wpdb, $current_user, $FB_TABLE, $FB_URL, $quizzes;
    
?>

<div class="wrap fb-wrap">
    <h1>Reporting</h1>
    <div class="fb-filters"></div>
    <table class="wp-list-table widefat fixed striped quizzes" id="quizzes-table">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column" style="width: 50px;"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" class="manage-column column-primary sortable desc">Quiz ID</th>
                <th scope="col" class="manage-column column-primary sortable desc">Quiz Title</th>
                <th scope="col" class="manage-column column-primary">Number of questions</th>
                <th scope="col" class="manage-column column-primary sortable desc">Total Attempts</th>
                <th scope="col" class="manage-column column-primary sortable desc">Average Score</th>
                <th scope="col" class="manage-column column-primary">Total time on quiz</th>
                <th scope="col" class="manage-column column-primary sortable desc">Average time on quiz</th>
            </tr>
        </thead>
        <tbody id="the-list">
        <?php
            
            $sql = "SELECT * FROM {$FB_TABLE['quizzes']} {$where} order by id desc";
            $rows = $wpdb->get_results($sql);
            
            foreach ($rows as $row) {                
                
        ?>
            <tr id="post-<?php echo $row->id; ?>>">
                <th scope="row" class="check-column">
                    <input id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                    <div class="locked-indicator"></div>
                </th>
                <td><?php echo $row->id; ?></td>
                <td><strong><a class="row-title" href="<?php echo $FB_URL['reporting'] . '&quiz=' . $row->id; ?>"><?php echo $row->title; ?></a></strong></td>
                <td><?php echo $row->num_of_questions; ?></td>
                <td><?php echo $quizzes->getTotalAttempts($row->id); ?></td>
                <td><?php echo $quizzes->getAverageScore($row->id) . '%'; ?></td>                
                <td><?php echo ''; ?></td>
                <td><?php echo ''; ?></td>                
            </tr>
        <?php 
            }
        ?>
        </tbody>
    </table>
</div>
<script>
    jQuery(document).ready(function($) {
        var ua = "<?php echo $FB_URL['ua']; ?>";
        $( "#quizzes-table" ).DataTable({
            "aoColumnDefs": [
              { 'bSortable': false, 'aTargets': [ 0 ] }
            ],
            "order": [[ 1, "desc" ]],
            "pageLength": 25,
            "lengthChange": false,
            "searching": false
        });
    });
    
</script>
