<?php
    global $wpdb, $current_user, $FB_TABLE, $FB_URL, $quizzes;  

    $action = $_REQUEST['action'];
    $fb_msg = "";
    
    if ($action == 'trash') {
        $dumbs = $_REQUEST['id'];
        foreach ($dumbs as $dumb) {
            $quizzes->fb_quiz->delete_quiz($dumb);            
        }
                
        if (count($dumbs) == 1)
            $fb_msg = count($dumbs) . " quiz permanently deleted.";
        else
            $fb_msg = count($dumbs) . " quizzes permanently deleted.";
            
    } else {
        $title = $_REQUEST['title'];    
        $num_questions = $_REQUEST['num_questions'];
        $passing_percentage = $_REQUEST['passing_percentage'];
        $author = $_REQUEST['author'];
        $from_date = $_REQUEST['from_date'];
        $to_date = $_REQUEST['to_date'];
        
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
        if (!empty($from_date)) {
            $where = $where . " AND created_at>='{$from_date}'";
        }
        if (!empty($to_date)) {
            $where = $where . " AND created_at<='{$to_date}'";
        }
    }
?>

<div class="wrap fb-wrap">
    <h1>All Quizzes <a href="<?php echo $FB_URL['un']; ?>" class="page-title-action">Add New</a></h1>
    <?php if ($fb_msg != '') : ?>
    <div id="message" class="updated notice is-dismissible below-h2">
        <p><?php echo $fb_msg; ?></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <?php endif; ?>
    <div class="fb-filters">        
        <label class="fb-filter-label">Filter By: </label>
        <label style="width: 120px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_title" placeholder="Title" value="<?php echo $title; ?>"></label>        
        <select id="fb_course_id" style="display: none;">
            <option value="">All</option>
        </select>
        <label style="width: 195px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_num_questions" placeholder="Number of Questions" value="<?php echo $num_questions; ?>"></label>
        <label style="width: 180px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_passing_percentage" placeholder="Passing Percentage" value="<?php echo $passing_percentage; ?>"></label>
        <label style="width: 120px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_author" placeholder="Author" value="<?php echo $author; ?>"></label>
        <label style="width: 125px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_from_date" placeholder="From Date" value="<?php echo $from_date; ?>"></label>
        <label style="width: 125px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_to_date" placeholder="To Date" value="<?php echo $to_date; ?>"></label>
        <label style="width: 80px; margin-right: 2px;"><input id="fb_filter_button" type="button" class="button" value="Filter"></label>        
        <label style="width: 80px;"><a href="<?php echo $FB_URL['ua']; ?>" id="fb_reset_button" class="button">Reset</a></label>
    </div>
    <div class="fb-data-table-wrap">
        <table class="wp-list-table widefat fixed striped quizzes fb-data-table" id="quizzes-table">
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
                
                $sql = "SELECT * FROM {$FB_TABLE['quizzes']} {$where} order by id desc";            
                $rows = $wpdb->get_results($sql);
                
                foreach ($rows as $row) {
                    
                    $user = get_user_by("id", $row->author);                
                    if (!empty($author) && strpos($user->user_nicename, $author) === false) {
                        continue;
                    }
                    
            ?>
                <tr id="post-<?php echo $row->id; ?>">
                    <th scope="row" class="check-column">
                        <input class="fb-check-item" id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                        <div class="locked-indicator"></div>
                    </th>
                    <td><?php echo $row->id; ?></td>
                    <td><strong><a class="row-title" href="<?php echo $FB_URL['un'] . '&id=' . $row->id . '&action=edit'; ?>" title="Edit this item"><?php echo $row->title; ?></a></strong>
                        <div class="row-actions">
                            <span class="edit"><a href="<?php echo $FB_URL['un'] . '&id=' . $row->id . '&action=edit'; ?>" title="Edit this item">Edit</a> | </span>
                            <span class="trash"><a href="<?php echo $FB_URL['ua'] . '&id[]=' . $row->id . '&action=trash'; ?>" class="fb_submitdelete" title="Move this item to the Trash" href="">Delete</a> | </span>
                            <span class="view"><a href="<?php echo $FB_URL['quizzes'] . $row->id; ?>" title="View Quiz" rel="permalink">View</a></span>
                        </div>
                    </td>
                    <td>
                    <?php
                        $q_connected_to = unserialize($row->connected_to);
                        if (!empty($q_connected_to)) {
                            $dumb = array();
                            foreach ($q_connected_to as $q_connection) {
                                $obj = get_post($q_connection);                    
                                array_push($dumb, $obj->post_title);
                            } 
                            echo implode(', ', $dumb);
                        }
                    
                    ?></td>
                    <td><?php echo $row->num_of_questions; ?></td>
                    <td><?php echo $row->passing_percentage . '%'; ?></td>
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
</div>                                
<script>
    var ua = "<?php echo $FB_URL['ua']; ?>";
    var trash_url = ua + '&action=trash';    
    jQuery(document).ready(function($) {        
        $( ".fb-data-table" ).DataTable({
            "aoColumnDefs": [
              { 'bSortable': false, 'aTargets': [ 0 ] }
            ],
            "order": [[ 1, "desc" ]],
            "pageLength": 25,
            
            "searching": false,
            "dom": 'rtlp',
            buttons: [
                'selectRows',
                'selectColumns',
                'selectCells'
            ]
        });
        $(".fb-data-table-wrap .dataTables_paginate").append('<input type="button" id="fb_delete_selected" class="button" value="Delete Selected"/>');
        
        
        $("#fb_from_date").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $("#fb_to_date").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        
        $("#fb_filter_button").click(function(e) {
            title = $("#fb_title").val();            
            num_questions = $("#fb_num_questions").val();
            passing_percentage = $("#fb_passing_percentage").val();
            author = $("#fb_author").val();            
            from_date = $("#fb_from_date").val();
            to_date = $("#fb_to_date").val();
            
            if (title != '') {
                ua = ua + '&title=' + title; 
            }
            if (num_questions != '') {
                ua = ua + '&num_questions=' + num_questions; 
            }            
            if (passing_percentage != '') {
                ua = ua + '&passing_percentage=' + passing_percentage; 
            }
            if (author != '') {
                ua = ua + '&author=' + author; 
            }
            if (from_date != '') {
                ua = ua + '&from_date=' + from_date; 
            }
            if (to_date != '') {
                ua = ua + '&to_date=' + to_date; 
            }
            
            window.location.href = ua;            
        });
    });
    
</script>
