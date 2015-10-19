<?php
    global $wpdb, $current_user, $FB_TABLES, $QA_URL, $QN_URL;  

?>

<div class="wrap fb-wrap">
    <h1>All Questions <a href="<?php echo $QN_URL; ?>" class="page-title-action">Add New</a></h1>
    
    <table class="wp-list-table widefat fixed striped questions">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="column-question-id" class="manage-column column-primary sortable desc"><a href="<?php echo $QA_URL . '&orderby=id&order=asc'; ?>"><span>Question ID</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-question-title" class="manage-column column-primary sortable desc"><a href="<?php echo $QA_URL . '&orderby=title&order=asc'; ?>"><span>Question Title</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-connected-to" class="manage-column column-primary">Connected To</th>
                <th scope="col" id="column-number-of-choices" class="manage-column column-primary sortable desc"><a href="<?php echo $QA_URL . '&orderby=number_of_choices&order=asc'; ?>"><span>Number of Choices</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-points" class="manage-column column-primary sortable desc"><a href="<?php echo $QA_URL . '&orderby=points&order=asc'; ?>"><span>Points</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-author-name" class="manage-column column-primary">Author Name</th>
                <th scope="col" id="column-created-at" class="manage-column column-primary sortable desc"><a href="<?php echo $QA_URL . '&orderby=create_at&order=asc'; ?>"><span>Created At</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-updated-at" class="manage-column column-primary sortable desc"><a href="<?php echo $QA_URL . '&orderby=updated_at&order=asc'; ?>"><span>Updated At</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-categories" class="manage-column column-primary">Categories</th>
            </tr>
        </thead>
        <tbody id="the-list">
        <?php
            $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLES['questions'] . " WHERE 1 order by id desc");
            
            foreach ($rows as $row) {
                
                $user = get_user_by("id", $row->author);
                $cats = unserialize($row->cats);
                
                $cats_str = array();
                if (!empty($cats)) {                    
                    foreach ($cats as $cat) {
                        $rows2 = $wpdb->get_results("SELECT name FROM " . $FB_TABLES['questions_cat'] . " WHERE id=" . $cat);
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
                <td><strong><a class="row-title" href="<?php echo $QN_URL . '&id=' . $row->id . '&action=edit'; ?>" title="Edit"><?php echo $row->title; ?></a></strong></td>
                <td></td>
                <td><?php echo $row->number_of_choices; ?></td>
                <td><?php echo $row->points; ?></td>
                <td><a href="<?php echo $QA_URL . '&author=' . $row->author; ?>"><?php echo $user->user_nicename; ?></a></td>
                <td><?php echo $row->created_at; ?></td>
                <td><?php echo $row->created_at; ?></td>
                <td><?php if (!empty($cats_str)) echo implode(", ", $cats_str); ?></td>                
            </tr>
        <?php 
            }
        ?>
        </tbody>
    </table>
</div>
