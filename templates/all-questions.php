<?php
    global $wpdb, $current_user, $FB_TABLE, $FB_URL, $quizzes;  

    $action = $_REQUEST['action'];
    $fb_msg = "";
    
    if ($action == 'trash') {
        $dumbs = $_REQUEST['id'];
        foreach ($dumbs as $dumb) {
            $quizzes->fb_question->delete_question($dumb);            
        }
                
        if (count($dumbs) == 1)
            $fb_msg = count($dumbs) . " quiz permanently deleted.";
        else
            $fb_msg = count($dumbs) . " quizzes permanently deleted.";
            
    } else {
        $title = $_REQUEST['title'];
        $quiz_id = $_REQUEST['quiz_id'];
        $num_choices = $_REQUEST['num_choices'];
        $points = $_REQUEST['points'];
        $author = $_REQUEST['author'];
        $from_date = $_REQUEST['from_date'];
        $to_date = $_REQUEST['to_date'];
        
        
        $where = ' WHERE 1 ';
        if (!empty($quiz_id)) {        
            $where = " INNER JOIN {$FB_TABLE['quiz_relationships']} ON {$FB_TABLE['questions']}.id={$FB_TABLE['quiz_relationships']}.question_id {$where} AND {$FB_TABLE['quiz_relationships']}.quiz_id={$quiz_id}";
        }
        if (!empty($title)) {
            $where = $where . ' AND title like "%' . $title . '%"';
        }
        if ($num_choices != '') {
            $where = $where . " AND number_of_choices={$num_choices}";
        }
        if ($points != '') {
            $where = $where . " AND points={$points}";
        }
        if (!empty($from_date)) {
            $where = $where . " AND created_at>='{$from_date}'";
        }
        if (!empty($to_date)) {
            $where = $where . " AND created_at<='{$to_date}'";
        } 
    }   
    
?>

<div class="wrap fb-wrap">
    <h1>All Questions <a href="<?php echo $FB_URL['qn']; ?>" class="page-title-action">Add New</a></h1>
    <?php if ($fb_msg != '') : ?>
    <div id="message" class="updated notice is-dismissible below-h2">
        <p><?php echo $fb_msg; ?></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <?php endif; ?>
    <div class="fb-filters">        
        <label class="fb-filter-label">Filter By: </label>
        <label style="width: 120px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_title" placeholder="Title" value="<?php echo $title; ?>"></label>        
        <select id="fb_quiz_id">
            <option value="">All</option>
            <?php
                $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " ORDER BY title ASC");                        
                foreach ($rows as $row) {
                    if ($row->id == $quiz_id) echo '<option value="' . $row->id . '" selected>' . $row->title . '</option>';
                    else echo '<option value="' . $row->id . '">' . $row->title . '</option>';
                }
            ?>
        </select>
        <label style="width: 175px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_num_choices" placeholder="Number of Choices" value="<?php echo $num_choices; ?>"></label>
        <label style="width: 100px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_points" placeholder="Points" value="<?php echo $points; ?>"></label>
        <label style="width: 120px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_author" placeholder="Author" value="<?php echo $author; ?>"></label>
        <label style="width: 125px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_from_date" placeholder="From Date" value="<?php echo $from_date; ?>"></label>
        <label style="width: 125px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_to_date" placeholder="To Date" value="<?php echo $to_date; ?>"></label>
        <label style="width: 80px;"><input id="fb_filter_button" type="button" class="button" value="Filter"></label>        
        <label style="width: 80px;"><a href="<?php echo $FB_URL['qa']; ?>" id="fb_reset_button" class="button">Reset</a></label>
    </div>
    <div class="fb-data-table-wrap" id="questions-table-wrap">
        <table class="wp-list-table widefat fixed striped questions fb-data-table" id="questions-table">
            <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                    <th style="width: 9%;" scope="col" id="column-question-id" class="manage-column column-primary sortable desc">Question ID</th>
                    <th style="width: 19%;" scope="col" id="column-question-title" class="manage-column column-primary sortable desc">Question Title</th>
                    <th style="width: 9%;" scope="col" id="column-question-type" class="manage-column column-primary sortable desc">Question Type</th>
                    <th style="width: 9%;" scope="col" id="column-connected-to" class="manage-column column-primary sortable">Connected To</th>
                    <th style="width: 9%;" scope="col" id="column-number-of-choices" class="manage-column column-primary sortable desc">Number of Choices</th>
                    <th style="width: 9%;" scope="col" id="column-points" class="manage-column column-primary sortable desc">Points</th>
                    <th style="width: 9%;" scope="col" id="column-author-name" class="manage-column column-primary sortable">Author Name</th>
                    <th style="width: 9%;" scope="col" id="column-created-at" class="manage-column column-primary sortable desc">Created On</th>
                    <th style="width: 9%;" scope="col" id="column-updated-at" class="manage-column column-primary sortable desc">Updated On</th>
                    <th style="width: 9%;" scope="col" id="column-categories" class="manage-column column-primary sortable">Categories</th>
                </tr>
            </thead>
            <tbody id="the-list">
            <?php
                $sql = "SELECT * FROM " . $FB_TABLE['questions'] . " {$where} order by {$FB_TABLE['questions']}.id desc";            
                $rows = $wpdb->get_results($sql);
                
                foreach ($rows as $row) {
                    
                    $user = get_user_by("id", $row->author);
                    if (!empty($author) && strpos($user->user_nicename, $author) === false) {
                        continue;
                    }
                    
                    $cats = unserialize($row->cats);
                    
                    $cats_str = array();
                    if (!empty($cats)) {                    
                        foreach ($cats as $cat) {
                            $rows2 = $wpdb->get_results("SELECT name FROM " . $FB_TABLE['questions_cat'] . " WHERE id=" . $cat);
                            $cats_str[] = $rows2[0]->name;
                        }
                    }
                    
            ?>
                <tr id="post-<?php echo $row->id; ?>" class="">
                    <th scope="row" class="check-column">
                        <input class="fb-check-item" id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                        <div class="locked-indicator"></div>
                    </th>
                    <td><?php echo $row->id; ?></td>
                    <td><strong><a class="row-title" href="<?php echo $FB_URL['qn'] . '&id=' . $row->id . '&action=edit'; ?>" title="Edit"><?php echo stripslashes($row->title); ?></a></strong>
                        <div class="row-actions">
                            <span class="edit"><a href="<?php echo $FB_URL['qn'] . '&id=' . $row->id . '&action=edit'; ?>" title="Edit this item">Edit</a> | </span>
                            <span class="trash"><a href="<?php echo $FB_URL['qa'] . '&id[]=' . $row->id . '&action=trash'; ?>" class="fb_submitdelete" title="Delete this item" href="">Delete</a></span>
                        </div>
                    </td>
                    <td><?php echo ucfirst($row->type); ?></td>
                    <td>
                    <?php
                        global $quizzes;
                        $quizzes->fb_question->print_quizzes_connected($row->id);
                    ?>
                    </td>
                    <td><?php echo $row->number_of_choices; ?></td>
                    <td><?php echo $row->points; ?></td>
                    <td><a href="<?php echo $FB_URL['qa'] . '&author=' . $row->author; ?>"><?php echo $user->user_nicename; ?></a></td>
                    <td><?php echo fb_date($row->created_at); ?></td>
                    <td><?php echo fb_date($row->updated_at); ?></td>
                    <td><?php if (!empty($cats_str)) echo implode(", ", $cats_str); ?></td>                
                </tr>
            <?php 
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    var qa = "<?php echo $FB_URL['qa']; ?>";
    var trash_url = qa + '&action=trash';
    jQuery(document).ready(function($) {
        
        $("#fb_filter_button").click(function(e) {
            title = $("#fb_title").val();
            quiz_id = $("#fb_quiz_id").val();
            num_choices = $("#fb_num_choices").val();
            points = $("#fb_points").val();
            author = $("#fb_author").val();            
            from_date = $("#fb_from_date").val();
            to_date = $("#fb_to_date").val();
            
            if (title != '') {
                qa = qa + '&title=' + title; 
            }
            if (quiz_id != '') {
                qa = qa + '&quiz_id=' + quiz_id; 
            }
            if (num_choices != '') {
                qa = qa + '&num_choices=' + num_choices; 
            }
            if (points != '') {
                qa = qa + '&points=' + points; 
            }
            if (author != '') {
                qa = qa + '&author=' + author; 
            }
            if (from_date != '') {
                qa = qa + '&from_date=' + from_date; 
            }
            if (to_date != '') {
                qa = qa + '&to_date=' + to_date; 
            }
            
            window.location.href = qa;            
        });
    });
    
</script>
