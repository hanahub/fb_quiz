<?php
    
    $pl = menu_page_url('all_questions', false) . '&';
?>

<div class="wrap fb-wrap">
    <h1>All Questions <a href="<?php menu_page_url('add_new_question'); ?>" class="page-title-action">Add New</a></h1>
    
    <table class="wp-list-table widefat fixed striped questions">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" id="column-question-id" class="manage-column column-primary sortable desc"><a href="<?php echo $pl . 'orderby=id&order=asc'; ?>"><span>Question ID</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-question-title" class="manage-column column-primary sortable desc"><a href="<?php echo $pl . 'orderby=title&order=asc'; ?>"><span>Question Title</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-connected-to" class="manage-column column-primary">Connected To</th>
                <th scope="col" id="column-number-of-choices" class="manage-column column-primary sortable desc"><a href="<?php echo $pl . 'orderby=number_of_choices&order=asc'; ?>"><span>Number of Choices</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-points" class="manage-column column-primary sortable desc"><a href="<?php echo $pl . 'orderby=points&order=asc'; ?>"><span>Poitns</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-author-name" class="manage-column column-primary">Author Name</th>
                <th scope="col" id="column-created-at" class="manage-column column-primary sortable desc"><a href="<?php echo $pl . 'orderby=create_at&order=asc'; ?>"><span>Created At</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-updated-at" class="manage-column column-primary sortable desc"><a href="<?php echo $pl . 'orderby=updated_at&order=asc'; ?>"><span>Updated At</span><span class="sorting-indicator"></span></a></th>
                <th scope="col" id="column-categories" class="manage-column column-primary">Categories</th>
            </tr>
        </thead>
        <tbody id="the-list">
            <tr id="post-1" class="">
                <th scope="row" class="check-column">
                    <input id="cb-select-1" type="checkbox" name="post[]" value="1">
                    <div class="locked-indicator"></div>
                </th>
                <td>1</td>
                <td><strong><a class="row-title" href="http://213.240.249.227/quiz/wp-admin/post.php?post=1&amp;action=edit" title="Edit “Hello world!”">Hello world!</a></strong></td>
                <td>AAAAAAA</td>
                <td>5</td>
                <td>50</td>
                <td><a href="<?php echo $pl . 'author=1'; ?>">admin</a></td>
                <td>created</td>
                <td>updated</td>
                <td>cat</td>
                
            </tr>
        </tbody>
    </table>
</div>
