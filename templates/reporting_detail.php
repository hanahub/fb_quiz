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
                <th scope="col" class="manage-column column-primary sortable desc">Student Name</th>
                <th scope="col" class="manage-column column-primary sortable desc">Total Attempts</th>
                <th scope="col" class="manage-column column-primary">Last Score</th>
                <th scope="col" class="manage-column column-primary sortable desc">Highest Score</th>
            </tr>
        </thead>
        <tbody id="the-list">
        <?php
            
            $sql = "SELECT ID as id FROM {$wpdb->prefix}users ORDER BY ID ASC";
            $rows = $wpdb->get_results($sql);
            
            foreach ($rows as $row) {        
                $user = get_user_by("id", $row->id);        
                
        ?>
            <tr id="post-<?php echo $row->id; ?>>">
                <th scope="row" class="check-column">
                    <input id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                    <div class="locked-indicator"></div>
                </th>                
                <td><strong><a class="row-title" href=""><?php echo $user->user_nicename; ?></a></strong></td>                
                <td><?php echo $quizzes->getTotalAttempts($quiz_id, $row->id); ?></td>
                <td><?php echo $quizzes->getLastScore($quiz_id, $row->id); ?></td>                
                <td><?php echo $quizzes->getHighestScore($quiz_id, $row->id); ?></td>
            </tr>
        <?php 
            }
        ?>
        </tbody>
    </table>
</div>
<script>
    jQuery(document).ready(function($) {
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
