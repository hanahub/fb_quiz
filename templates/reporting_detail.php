<?php
    global $wpdb, $current_user, $FB_TABLE, $FB_URL, $quizzes;      
?>

<div class="wrap fb-wrap">
    <h1>Reporting</h1>
    <div class="fb-filters"></div>
    <div class="fb-data-table-wrap">
        <table class="wp-list-table widefat fixed striped quizzes fb-data-table" id="quizzes-table">
            <thead>
                <tr>
                    <td style="width: 80px;" id="cb" class="manage-column column-cb check-column" style="width: 50px;"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th style="width: 25%;" scope="col" class="manage-column column-primary sortable desc">Student Name</th>
                    <th style="width: 25%;" scope="col" class="manage-column column-primary sortable desc">Total Attempts</th>
                    <th style="width: 25%;" scope="col" class="manage-column column-primary">Last Score</th>
                    <th style="width: 25%;" scope="col" class="manage-column column-primary sortable desc">Highest Score</th>
                </tr>
            </thead>
            <tbody id="the-list">
            <?php
                
                $sql = "SELECT ID as id FROM {$wpdb->prefix}users ORDER BY ID ASC";
                $rows = $wpdb->get_results($sql);
                
                foreach ($rows as $row) {        
                    $user = get_user_by("id", $row->id);        
                    
                    $attempts = $quizzes->getTotalAttempts($quiz_id, $row->id);
                    if ($attempts == 0) continue;
                    
                    $sql = "SELECT passing_percentage FROM {$FB_TABLE['quizzes']} where id={$quiz_id}";
                    $dumb = $wpdb->get_results($sql);
                    $passing_percentage = $dumb[0]->passing_percentage;
                    
                    $last_score = $quizzes->getLastScore($quiz_id, $row->id);
                    if (isset($last_score)) {
                        if ($last_score >= $passing_percentage)
                            $last_score = $last_score . '%(Passed)';
                        else
                            $last_score = $last_score . '%(Failed)';
                    }
                    
                    $highest_score = $quizzes->getHighestScore($quiz_id, $row->id);
                    if (isset($highest_score)) {
                        if ($highest_score >= $passing_percentage)
                            $highest_score = $highest_score . '%(Passed)';    
                        else
                            $highest_score = $highest_score . '%(Failed)';    
                    }
                    
                    
            ?>
                <tr id="post-<?php echo $row->id; ?>">
                    <th scope="row" class="check-column">
                        <input id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                        <div class="locked-indicator"></div>
                    </th>                
                    <td><strong><a class="row-title" href=""><?php echo $user->user_nicename; ?></a></strong></td>                
                    <td><?php echo $attempts; ?></td>
                    <td><?php echo $last_score; ?></td>                
                    <td><?php echo $highest_score; ?></td>
                </tr>
            <?php 
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
