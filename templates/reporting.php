<?php
    global $wpdb, $current_user, $FB_TABLE, $FB_URL, $quizzes;
    
    $title = $_REQUEST['title'];    
    $num_questions = $_REQUEST['num_questions'];
    $total_attempts = $_REQUEST['total_attempts'];
    $average_score = $_REQUEST['average_score'];    
    
    $where = ' WHERE 1 ';

    if (!empty($title)) {
        $where = $where . ' AND title like "%' . $title . '%"';
    }
    if ($num_questions != '') {
        $where = $where . " AND num_of_questions={$num_questions}";
    }
    if ($passing_percentage != '') {
        $where = $where . " AND passing_percentage={$passing_percentage}";
    }    
?>

<div class="wrap fb-wrap">
    <h1>Reporting</h1>
    <div class="fb-filters">        
        <label class="fb-filter-label">Filter By: </label>
        <label style="width: 150px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_title" placeholder="Title" value="<?php echo $title; ?>"></label>
        <label style="width: 195px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_num_questions" placeholder="Number of Questions" value="<?php echo $num_questions; ?>"></label>
        <label style="width: 160px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_total_attempts" placeholder="Total Attempts" value="<?php echo $total_attempts; ?>"></label>
        <label style="width: 160px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_average_score" placeholder="Average Score" value="<?php echo $average_score; ?>"></label>
        <!--<label style="width: 125px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_from_date" placeholder="From Date" value="<?php echo $from_date; ?>"></label>
        <label style="width: 125px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_to_date" placeholder="To Date" value="<?php echo $to_date; ?>"></label>-->
        <label style="width: 80px; margin-right: 2px;"><input id="fb_filter_button" type="button" class="button" value="Filter"></label>        
        <label style="width: 80px;"><a href="<?php echo $FB_URL['reporting']; ?>" id="fb_reset_button" class="button">Reset</a></label>
    </div>
    <div class="fb-data-table-wrap">
        <table class="wp-list-table widefat fixed striped quizzes fb-data-table" id="answers-table">
            <thead>
                <tr>
                    <td style="width: 80px;" id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th style="width: 10%" scope="col" class="manage-column column-primary sortable desc">Quiz ID</th>
                    <th style="width: 25%" scope="col" class="manage-column column-primary sortable desc">Quiz Title</th>
                    <th style="width: 22%" scope="col" class="manage-column column-primary">Number of questions</th>
                    <th style="width: 22%" scope="col" class="manage-column column-primary sortable desc">Total Attempts</th>
                    <th style="width: 21%" scope="col" class="manage-column column-primary sortable desc">Average Score</th>
                    <!--<th scope="col" class="manage-column column-primary">Total time on quiz</th>-->
                    <!--<th scope="col" class="manage-column column-primary sortable desc">Average time on quiz</th>-->
                </tr>
            </thead>
            <tbody id="the-list">
            <?php
                
                $sql = "SELECT * FROM {$FB_TABLE['quizzes']} {$where} order by id desc";
                $rows = $wpdb->get_results($sql);
                
                foreach ($rows as $row) {                
                    $totalAttempts = $quizzes->getTotalAttempts($row->id);
                    $averageScore = $quizzes->getAverageScore($row->id);
                    if ( $totalAttempts == 0 ) continue;
                    if (isset($total_attempts) && $total_attempts != $totalAttempts) continue;
                    if (isset($average_score) && $average_score != $averageScore) continue;
                    
            ?>
                <tr id="post-<?php echo $row->id; ?>">
                    <th scope="row" class="check-column">
                        <input id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                        <div class="locked-indicator"></div>
                    </th>
                    <td><?php echo $row->id; ?></td>
                    <td><strong><a class="row-title" href="<?php echo $FB_URL['reporting'] . '&quiz=' . $row->id; ?>"><?php echo $row->title; ?></a></strong></td>
                    <td><?php echo $row->num_of_questions; ?></td>
                    <td><?php echo  $totalAttempts; ?></td>
                    <td><?php echo  $averageScore . '%'; ?></td>                                                                
                </tr>
            <?php 
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    var reporting_url = "<?php echo $FB_URL['reporting']; ?>";
    
    jQuery(document).ready(function($) {        
        
        $("#fb_filter_button").click(function(e) {
            title = $("#fb_title").val();            
            num_questions = $("#fb_num_questions").val();
            total_attempts = $("#fb_total_attempts").val();
            average_score = $("#fb_average_score").val();
            
            if (title != '') {
                reporting_url = reporting_url + '&title=' + title; 
            }
            if (num_questions != '') {
                reporting_url = reporting_url + '&num_questions=' + num_questions; 
            }            
            if (total_attempts != '') {
                reporting_url = reporting_url + '&total_attempts=' + total_attempts; 
            }
            if (average_score != '') {
                reporting_url = reporting_url + '&average_score=' + average_score; 
            }
            
            window.location.href = reporting_url;            
        });
    });
    
</script>