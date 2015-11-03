<?php
    global $wpdb, $current_user, $FB_TABLE, $FB_URL;  

    $title = $_REQUEST['title'];
    
    $where = '1';
    if (!empty($title)) {
        $where = 'title like "%' . $title . '%"';
    }
    
?>

<div class="wrap fb-wrap">
    <h1>All Questions <a href="<?php echo $FB_URL['qn']; ?>" class="page-title-action">Add New</a></h1>
    <div class="fb-filters">        
        <label class="fb-filter-label">Filter By: </label>
        <label style="width: 120px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_title" placeholder="Title" value="<?php echo $title; ?>"></label>        
        <select name="fb_quiz_title">
            <option value="">All</option>
            <?php
                $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " ORDER BY title ASC");                        
                foreach ($rows as $row) {
                    echo '<option value="' . $row->title . '">' . $row->title . '</option>';
                }
            ?>
        </select>
        
        <label style="width: 175px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_num_choices" placeholder="Number of Choices"></label>
        <label style="width: 100px;"><i class="fb-icon icon-search"></i><input type="number" id="fb_points" placeholder="Points"></label>
        <label style="width: 120px;"><i class="fb-icon icon-search"></i><input type="search" id="fb_author" placeholder="Author"></label>
        <label style="width: 115px;"><i class="fb-icon icon-search"></i><input type="text" id="fb_from_date" placeholder="From Date"></label>
        <label style="width: 115px;"><i class="fb-icon icon-search"></i><input type="text" id="fb_to_date" placeholder="To Date"></label>
        <label style="width: 80px;"><input id="fb_filter_button" type="button" class="button" value="Filter"></label>        
    </div>
    <table class="wp-list-table widefat fixed striped questions" id="questions-table">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="column-question-id" class="manage-column column-primary sortable desc">Question ID</th>
                <th scope="col" id="column-question-title" class="manage-column column-primary sortable desc">Question Title</th>
                <th scope="col" id="column-connected-to" class="manage-column column-primary">Connected To</th>
                <th scope="col" id="column-number-of-choices" class="manage-column column-primary sortable desc">Number of Choices</th>
                <th scope="col" id="column-points" class="manage-column column-primary sortable desc">Points</th>
                <th scope="col" id="column-author-name" class="manage-column column-primary">Author Name</th>
                <th scope="col" id="column-created-at" class="manage-column column-primary sortable desc">Created At</th>
                <th scope="col" id="column-updated-at" class="manage-column column-primary sortable desc">Updated At</th>
                <th scope="col" id="column-categories" class="manage-column column-primary">Categories</th>
            </tr>
        </thead>
        <tbody id="the-list">
        <?php
            $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions'] . " WHERE {$where} order by id desc");
            
            foreach ($rows as $row) {
                
                $user = get_user_by("id", $row->author);
                $cats = unserialize($row->cats);
                
                $cats_str = array();
                if (!empty($cats)) {                    
                    foreach ($cats as $cat) {
                        $rows2 = $wpdb->get_results("SELECT name FROM " . $FB_TABLE['questions_cat'] . " WHERE id=" . $cat);
                        $cats_str[] = $rows2[0]->name;
                    }
                }
                
        ?>
            <tr id="post-<?php echo $row->id; ?>>" class="">
                <th scope="row" class="check-column">
                    <input id="cb-select-<?php echo $row->id; ?>" type="checkbox" name="post[]" value="<?php echo $row->id; ?>">
                    <div class="locked-indicator"></div>
                </th>
                <td><?php echo $row->id; ?></td>
                <td><strong><a class="row-title" href="<?php echo $FB_URL['qn'] . '&id=' . $row->id . '&action=edit'; ?>" title="Edit"><?php echo $row->title; ?></a></strong></td>
                <td>
                <?php
                    global $quizzes;
                    $quizzes->fb_question->print_quizzes_connected($row->id);
                ?>
                </td>
                <td><?php echo $row->number_of_choices; ?></td>
                <td><?php echo $row->points; ?></td>
                <td><a href="<?php echo $FB_URL['qa'] . '&author=' . $row->author; ?>"><?php echo $user->user_nicename; ?></a></td>
                <td><?php echo $row->created_at; ?></td>
                <td><?php echo $row->updated_at; ?></td>
                <td><?php if (!empty($cats_str)) echo implode(", ", $cats_str); ?></td>                
            </tr>
        <?php 
            }
        ?>
        </tbody>
    </table>
</div>
<script>
    
    jQuery(document).ready(function($) {
        var qa = "<?php echo $FB_URL['qa']; ?>";
        $( "#questions-table" ).DataTable({
            "aoColumnDefs": [
              { 'bSortable': false, 'aTargets': [ 0 ] }
            ],
            "order": [[ 1, "desc" ]],
            "pageLength": 25,
            "lengthChange": false,
            "searching": false
        });
        
        $("#fb_from_date").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $("#fb_to_date").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        
        $("#fb_filter_button").click(function(e) {
            title = $("#fb_title").val();
            if (title != '') {
                qa = qa + '&title=' + title; 
            }
            location.href = qa;            
        });
    });
    
</script>
