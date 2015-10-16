<?php
    global $wpdb, $FB_TABLES, $current_user;
    
    
?>

<div class="wrap fb-wrap">
    
    <h1>Add New/Edit Question</h1>
    <div class="fb-submitbox">
        <div id="fb-major-publishing-actions">            
            <label for="post-status">Status:</label>
            <span id="post-status-display">Draft</span>
            <div id="publishing-action">                                
                <input type="button" class="button button-primary button-large" id="fb-publish" value="Publish"/>
                <input type="button" class="button button-large" id="fb-draft" value="Draft"/>
                <input type="button" class="button button-large" id="fb-delete" value="Delete Question"/>
            </div>
        </div>
    </div>
    <div class="fb-body">
        <div class="fb-half-left">
            <div class="fb-input-field fb-row">
                <label for="fb-question">Question:</label>        
                <?php wp_editor( '', 'fb-question-title', $settings = array('textarea_name'=>'fb-question-title', 'editor_height'=>'200px') ); ?>
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
                        <div class="fb-input-wrapper1 ui-sortable" id="fb-choices">
                            
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="fb-input-field fb-row">
                <label for="fb-explanation">Correct Choice Explanation:</label>        
                <?php wp_editor( '', 'fb-correct-explanation', $settings = array('textarea_name'=>'fb-correct-explanation', 'editor_height'=>'200px') ); ?>
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
                            <option value="1">Single Choice</option>
                            <option value="2">Multiple Choice</option>
                            <option value="3">Sorting</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="fb-point">Points:</label></td>
                    <td><input type="text" name="fb-point" id="fb-point"/></td>
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
                                                $cats = $wpdb->get_results("SELECT * FROM " . $FB_TABLES['questions_cat'] );
                                                foreach ($cats as $cat) {
                                                    echo '<li class="popular-category"><label class="selectit"><input value="' . $cat->id . '" type="checkbox" name="post_category[]"><span> ' . $cat->name . '</span></label></li>';
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
    </div>
</div>
