<?php
    global $wpdb, $current_user, $FB_TABLE, $FB_URL;  

?>

<div class="wrap fb-wrap">
    <h1>All Quizzes <a href="<?php echo $FB_URL['un']; ?>" class="page-title-action">Add New</a></h1>
    
    <table class="wp-list-table widefat fixed striped quizzes" id="quizzes-table">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="column-quiz-id" class="manage-column column-primary sortable desc">Quiz ID</th>
                <th scope="col" id="column-quiz-title" class="manage-column column-primary sortable desc">Quiz Title</th>
                <th scope="col" id="column-connected-to" class="manage-column column-primary">Connected To</th>
                <th scope="col" id="column-number-of-questions" class="manage-column column-primary sortable desc">Number of Questions</th>
                <th scope="col" id="column-passing-percentage" class="manage-column column-primary sortable desc">Passing Percentage</th>
                <th scope="col" id="column-author-name" class="manage-column column-primary">Author Name</th>
                <th scope="col" id="column-created-at" class="manage-column column-primary sortable desc">Created At</th>
                <th scope="col" id="column-updated-at" class="manage-column column-primary sortable desc">Updated At</th>                
            </tr>
        </thead>
        <tbody id="the-list">
        <?php
            $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE 1 order by id desc");
            
            foreach ($rows as $row) {
                
                $user = get_user_by("id", $row->author);
                
        ?>
            <tr id="post-<?php echo $row->id; ?>>" class="">
                <th scope="row" class="check-column">
                    <input id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                    <div class="locked-indicator"></div>
                </th>
                <td><?php echo $row->id; ?></td>
                <td><strong><a class="row-title" href="<?php echo $FB_URL['un'] . '&id=' . $row->id . '&action=edit'; ?>" title="Edit this item"><?php echo $row->title; ?></a></strong>
                    <div class="row-actions">
                        <span class="edit"><a href="<?php echo $FB_URL['un'] . '&id=' . $row->id . '&action=edit'; ?>" title="Edit this item">Edit</a> | </span>
                        <span class="trash"><a class="submitdelete" title="Move this item to the Trash" href="">Delete</a> | </span>
                        <span class="view"><a href="<?php echo $FB_URL['quizzes'] . $row->id; ?>" title="View Quiz" rel="permalink">View</a></span>
                    </div>
                </td>
                <td>
                <?php
                    $q_connected_to = unserialize($row->connected_to);
                    if (!empty($q_connected_to)) {
                        $dumb = array();
                        foreach ($q_connected_to as $q_connection) {
                            $obj = get_post_type_object($q_connection);                    
                            array_push($dumb, $obj->label);
                        } 
                        echo implode(', ', $dumb);
                    }
                
                ?></td>
                <td><?php echo $row->num_of_questions; ?></td>
                <td><?php echo $row->passing_percentage; ?></td>
                <td><a href="<?php echo $FB_URL['qa'] . '&author=' . $row->author; ?>"><?php echo $user->user_nicename; ?></a></td>                
                <td><?php echo $row->created_at; ?></td>
                <td><?php echo $row->updated_at; ?></td>                
            </tr>
        <?php 
            }
        ?>
        </tbody>
    </table>
</div>
<script>
    jQuery( "#quizzes-table" ).DataTable({
        "aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0 ] }
        ],
        "order": [[ 1, "desc" ]],
        "pageLength": 25
    });
</script>
