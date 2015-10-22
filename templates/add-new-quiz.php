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
    
    <h1>Add New/Edit Quiz</h1>
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
        <div class="fb-row">
            <div class="fb-half-left">
                <div class="fb-input-field fb-row">
                    <label for="fb-quiz-title">Quiz Title:</label>        
                    <input type="text" name="fb-quiz-title" id="fb-quiz-title" value="<?php echo $q_points; ?>" class="fb-fullwidth"/>                
                </div>
                <div class="fb-input-field">
                    <label for="fb-quiz-description">Quiz Description:</label>        
                    <?php wp_editor( $q_title, 'fb-quiz-description', $settings = array('textarea_name'=>'fb-quiz-description', 'editor_height'=>'200px') ); ?>
                </div>
            </div>
            <div class="fb-half-right">
                <table class="form-table fb-table2">
                    <tr>
                        <td><label for="fb-quiz-layout">Layout:</label></td>
                        <td>
                            <select id="fb-quiz-layout">                        
                                <option value="single" <?php if ($q_type == 'single') echo 'selected'; ?>>Single Page</option>                            
                            </select>
                        </td>
                    </tr>
                    <tr class="fb-quiz-checkboxes">
                        <td colspan="2">
                            <label for="fb-allow-skipping"><input type="checkbox" name="fb-allow-skipping" id="fb-allow-skipping">Allow Skipping questions (if one question per page layout)</label>
                            <label for="fb-immediate-feedback"><input type="checkbox" name="fb-immediate-feedback" id="fb-immediate_feedback">Immediate Feedback</label>
                            <label for="fb-random-questions"><input type="checkbox" name="fb-random-questions" id="fb-random-questions">Random Questions Order</label>
                            <label for="fb-random-choices"><input type="checkbox" name="fb-random-choices" id="fb-random-choices">Random Choices Order</label>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="fb-passing-percentage">Passing Percentage:</label></td>
                        <td><input type="text" name="fb-passing-percentage" id="fb-passing-percentage" value="<?php echo $q_points; ?>"/>%</td>
                    </tr>                
                </table>
            </div>
        </div>
        
        <div class="fb-row">
            <div class="fb-input-field">
                <label for="fb-questions-total" class="fb-inline">Total Number of Questions:</label>        
                <?php echo '<span id="fb-questions-total">0</span>'; ?>
            </div>
        </div>        
        <div class="fb-row">                
            <div class="fb-fieldset fb-choices-box" id="fb-questions-box">
                <fieldset>
                    <legend>Questions</legend>
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
        <div class="fb-row">                
            <div class="fb-half-left">
                <div class="fb-fieldset fb-add-choice-box" id="fb-add-quiz-box">
                    <fieldset>
                        <legend>Add a question</legend>
                        <div class="fb-datatable-wrapper">
                        <?php
                            $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLES['questions'] . " WHERE 1 order by id desc");
                            echo '<table id="fb-questions-table" class="fb-datatable fb-fullwidth">';
                            foreach ($rows as $row) {
                                echo '                                
                                <tr id="question_row_' . $row->id . '"><td>
                                    <div class="fb-choice">
                                        <a data-id="' . $row->id . '" href="javascript:void(0)" class="fb-add"><i class="fb-icon icon-plus-squared"></i></a>                                        
                                        <span class="fb-choice-name">' . $row->title . '</span>                                        
                                    </div>
                                
                                </td></tr>';
                            }
                            echo '</table>';
                        ?>    
                        </div>
                    </fieldset>
                </div>
            </div>            
            <div class="fb-half-right">
                <div class="fb-fieldset fb-add-choice-box" id="fb-add-quiz-box2">
                    <fieldset>
                        <legend>Pools (categories)</legend>
                        <div class="fb-datatable-wrapper">
                        <?php
                            $cats = $wpdb->get_results("SELECT * FROM " . $FB_TABLES['questions_cat'] );
                            echo '<table id="fb-categories-table" class="fb-datatable fb-fullwidth">';
                            
                            foreach ($cats as $row) {
                                echo '                                
                                <tr id="question_row_' . $row->id . '"><td>
                                    <div class="fb-choice">
                                        <a data-id="' . $row->id . '" href="javascript:void(0)" class="fb-add"><i class="fb-icon icon-plus-squared"></i></a>                                        
                                        <span class="fb-choice-name">' . $row->name . '</span>                                        
                                    </div>
                                
                                </td></tr>';
                            }
                            echo '</table>';
                        ?>    
                        </div>
                    </fieldset>
                </div>
            </div>            
        </div>            
        <div class="fb-fieldset fb-connected-to-box fb-row">
            <fieldset>
                <legend>Connected to</legend>
                <div class="fb-input-wrapper1">
                                
                </div>
            </fieldset>            
        </div>
        
        
        <input type="hidden" id="fb-author" value="<?php echo $current_user->ID; ?>"/>
        <input type="hidden" id="fb-edit-id" value="<?php echo $id; ?>"/>
        
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        
        var questions_total = 0;
        
        $( "#fb-choices" ).sortable();
        
        $( "#fb-questions-table, #fb-categories-table" ).DataTable({
            "aoColumnDefs": [
              { 'bSortable': false, 'aTargets': [ 0 ] }
            ],
            "ordering": false,
            "info": false,
            "language": {
                search: '<i class="fb-icon icon-search"></i>',
                searchPlaceholder: "Search a question",
                
            },
        });
        
        $(".fb-add").live("click", function(e) {
            id = $(this).attr("data-id");
            title = $(this).next(".fb-choice-name").text();
            row = ' \
                    <div class="fb-choice" data-id="' + id + '"> \
                        <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a> \
                        <a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a> \
                        <span class="fb-choice-name ui-state-default ui-sortable-handle">' + title + '</span> \
                    </div> \
                   ';            
            $(this).parents("tr").fadeOut(200, function() {
                $("#fb-choices").append(row);    
            });
            questions_total ++;
            $("#fb-questions-total").text(questions_total);
            
            
        });
        
        $("#fb-questions-box .fb-remove").live("click", function(e) {
            var dumb = $(this).parents(".fb-choice");
            id = $(dumb).attr("data-id");
            $(dumb).fadeOut(200, function() {
                $(this).remove();
                $("#question_row_" + id).show();
            });
            questions_total --;
            $("#fb-questions-total").text(questions_total);
        });
    });
    
</script>
