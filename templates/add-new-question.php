<?php
    global $wpdb, $FB_TABLES, $current_user;
    
    $id = $_REQUEST['id'];
    $action = $_REQUEST['action'];
    $edit_mode = 0;
    if (!empty($id) && is_numeric($id) && $action == 'edit') {
        $edit_mode = 1;
        $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLES['questions'] . " WHERE id=" . $id );
        $q_data = $dumb[0];
        
        $q_title = $q_data->title;
        $q_correct_explanation = $q_data->correct_explanation;
        $q_choices = unserialize($q_data->choices);
        $q_type = $q_data->type;
        $q_points = $q_data->points;
        $q_cats = unserialize($q_data->cats);
    } else {
        $id = '';
        $q_title = '';
        $q_correct_explanation = '';
        $q_type = '';
        $q_points = '';
        $q_cats = '';
    }
?>

<div class="wrap fb-wrap">
    
    <h1>Add New/Edit Question</h1>
    <div class="fb-submitbox">
        <div id="fb-major-publishing-actions">           
            <?php if ($edit_mode == 1) : ?>
                <label for="post-status">Status:</label>
                <span id="post-status-display"><?php echo ucwords($q_data->status); ?></span>
            <?php endif; ?>
            <div id="publishing-action">                                
                <input type="button" class="button button-primary button-large" id="fb-publish" value="<?php if ($edit_mode == 1) echo 'Update'; else echo 'Publish'; ?>"/>
                <input type="button" class="button button-large" id="fb-draft" value="Draft"/>
                <?php if ($edit_mode == 1) : ?>
                    <input type="button" class="button button-large" id="fb-delete" value="Delete Question"/>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="fb-body">
        <div class="fb-half-left">
            <div class="fb-input-field fb-row">
                <label for="fb-question">Question:</label>        
                <?php wp_editor( $q_title, 'fb-question-title', $settings = array('textarea_name'=>'fb-question-title', 'editor_height'=>'200px') ); ?>
            </div>
            <div class="fb-row">
                <div class="fb-fieldset fb-add-choice-box">
                    <fieldset>
                        <legend>Add a choice</legend>
                        <div class="fb-input-wrapper1">
                            <div class="fb-input1"><input type="text" name="fb-input-choice" id="fb-input-choice" placeholder="Choice..."/></div>
                            <div class="fb-input2"><input type="button" name="fb-button-add" id="fb-button-add" value="Add"/></div>
                        </div>
                    </fieldset>
                </div>
                <div class="fb-fieldset fb-choices-box">
                    <fieldset>
                        <legend>Choices</legend>
                        <div class="fb-input-wrapper1">
                        <?php                                                 
                            if ($edit_mode == 1 && count($q_choices['choices']) > 0) {
                                if ($q_type == "single") {
                                    echo '<div class="fb-choices-header"><span class="fb-correct">Correct</span></div><div class="clear"></div>';
                                    echo '<div class="ui-sortable" id="fb-choices">';
                                    foreach ($q_choices['choices'] as $choice) {
                                        $choice_checked = '';
                                        if ($q_choices['correct'] == $choice[0]) $choice_checked = "checked";
                                        echo '
                                        
                                        <div class="fb-choice">
                                            <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a>
                                            <a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a>
                                            <span class="fb-choice-name ui-state-default ui-sortable-handle">' . $choice[1] . '</span>
                                            <input type="radio" name="fb-correct-choice" class="fb-correct-choice" ' . $choice_checked . '>                     
                                        </div>';    
                                    }
                                    echo '</div>';
                                }                                
                            } else {
                                echo '<div class="ui-sortable" id="fb-choices"></div>';
                            }                        
                        ?>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="fb-input-field fb-row">
                <label for="fb-explanation">Correct Choice Explanation:</label>        
                <?php wp_editor( $q_correct_explanation, 'fb-correct-explanation', $settings = array('textarea_name'=>'fb-correct-explanation', 'editor_height'=>'200px') ); ?>
            </div>
            <div class="fb-fieldset fb-connected-to-box fb-row">
                <fieldset>
                    <legend>Connected to</legend>
                    <div class="fb-input-wrapper1">
                                    
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="fb-half-right">
            <table class="form-table fb-table1">
                <tr>
                    <td><label for="fb-question-type" name="fb-question-type">Question Type:</label></td>
                    <td>
                        <select id="fb-question-type">                        
                            <option value="single" <?php if ($q_type == 'single') echo 'selected'; ?>>Single Choice</option>
                            <option value="multiple" <?php if ($q_type == 'multiple') echo 'selected'; ?>>Multiple Choice</option>
                            <option value="sorting" <?php if ($q_type == 'sorting') echo 'selected'; ?>>Sorting</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="fb-point">Points:</label></td>
                    <td><input type="text" name="fb-point" id="fb-point" value="<?php echo $q_points; ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="categorydiv" class="postbox">
                            <label for="fb-question-category" class="fb-label1">Categories</label>
                            <div class="inside">
                                <div id="taxonomy-category" class="categorydiv">
                                    <ul id="category-tabs" class="category-tabs">
                                        <li class="tabs"><a href="#category-all">All Categories</a></li>                                    
                                    </ul>
                                    <div id="category-all" class="tabs-panel" style="display: block;">
                                        <input type="hidden" name="post_category[]" value="0">            
                                        <ul id="categorychecklist" class="categorychecklist form-no-clear">
                                        <?php
                                            $cats_ids = array();
                                            if (!empty($q_cats)) {                                                    
                                                foreach ($q_cats as $cat) {
                                                    $dumb = $wpdb->get_results("SELECT id FROM " . $FB_TABLES['questions_cat'] . " WHERE id=" . $cat);
                                                    $cats_ids[] = $dumb[0]->id;
                                                }
                                            }
                                        
                                            $cats = $wpdb->get_results("SELECT * FROM " . $FB_TABLES['questions_cat'] );
                                            foreach ($cats as $cat) {
                                                if (in_array($cat->id, $cats_ids)) {
                                                    $dumb = "checked";
                                                } else {
                                                    $dumb = "";
                                                }
                                                echo '<li class="popular-category"><label class="selectit"><input ' . $dumb . ' value="' . $cat->id . '" type="checkbox" name="post_category[]"><span> ' . $cat->name . '</span></label></li>';
                                            }
                                        ?>                                        
                                            
                                        </ul>
                                    </div>
                                    <div id="category-adder" class="wp-hidden-children">
                                        <h4><a id="fb-category-add-toggle" href="javascript: void(0)" class="hide-if-no-js">+ Add New Category</a></h4>                                        
                                        <p id="category-add" class="category-add wp-hidden-child">
                                            <label class="screen-reader-text" for="newcategory">Add New Category</label>
                                            <input type="text" name="fb-newcategory" id="fb-newcategory" class="form-required" aria-required="true">
                                            <input type="button" id="fb-category-add-submit" class="button category-add-submit" value="Add New Category">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>            
                </tr>                
            </table>
        </div>
        
        <input type="hidden" id="fb-author" value="<?php echo $current_user->ID; ?>"/>
        <input type="hidden" id="fb-edit-id" value="<?php echo $id; ?>"/>
        
    </div>
</div>
