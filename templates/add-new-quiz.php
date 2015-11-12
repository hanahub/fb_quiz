<?php
    global $wpdb, $FB_TABLE, $current_user;
    
    global $wp_rewrite;
    
    $id = $_REQUEST['id'];
    $action = $_REQUEST['action'];
    $edit_mode = 0;
    if (!empty($id) && is_numeric($id) && $action == 'edit') {
        $edit_mode = 1;
        $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $id );
        $q_data = $dumb[0];
        
        $q_title = $q_data->title;
        $q_description = $q_data->description;
        $q_questions = unserialize($q_data->questions);
        $q_num_of_questions = $q_data->num_of_questions;
        $q_connected_to = unserialize($q_data->connected_to);
        $q_passing_percentage = $q_data->passing_percentage;
        $q_layout = $q_data->layout;
        $q_allow_skipping = $q_data->allow_skipping;        
        $q_immediate_feedback = $q_data->immediate_feedback;
        $q_random_questions = $q_data->random_questions;
        $q_random_choices = $q_data->random_choices;        
    } else {
        $id = '';        
    }
    
    $cpt = array();
    $cpt_labels = array();
    $post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );                        
    foreach ( $post_types as $post_type ) {                         
       array_push($cpt, $post_type->name);
       $cpt_labels[$post_type->name] = $post_type->label;
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
                    <input type="button" class="button button-large" id="fb-delete" value="Delete Quiz"/>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="fb-body">
        <div class="fb-row">
            <div class="fb-half-left">
                <div class="fb-input-field fb-row">
                    <label for="fb-quiz-title">Quiz Title:</label>        
                    <input type="text" name="fb-quiz-title" id="fb-quiz-title" value="<?php echo $q_title; ?>" class="fb-fullwidth"/>                
                </div>
                <div class="fb-input-field">
                    <label for="fb-quiz-description">Quiz Description:</label>        
                    <?php wp_editor( $q_description, 'fb-quiz-description', $settings = array('textarea_name'=>'fb-quiz-description', 'editor_height'=>'200px') ); ?>
                </div>
            </div>
            <div class="fb-half-right">
                <table class="form-table fb-table2">
                    <tr>
                        <td><label for="fb-quiz-layout">Layout:</label></td>
                        <td>
                            <select id="fb-quiz-layout">                        
                                <option value="single" <?php if ($q_layout == 'single') echo 'selected'; ?>>Single Page</option>                            
                                <option value="multiple" <?php if ($q_layout == 'multiple') echo 'selected'; ?>>Multiple Page</option>                            
                            </select>
                        </td>
                    </tr>
                    <tr class="fb-quiz-checkboxes">
                        <td colspan="2">
                            <label for="fb-allow-skipping"><input type="checkbox" name="fb-allow-skipping" id="fb-allow-skipping" <?php if ($q_allow_skipping == 1 || $edit_mode != 1) echo "checked"; ?>>Allow Skipping questions (if one question per page layout)</label>
                            <!--<label for="fb-immediate-feedback"><input type="checkbox" name="fb-immediate-feedback" id="fb-immediate-feedback" <?php if ($q_immediate_feedback == 1) echo "checked"; ?>>Immediate Feedback</label>-->
                            <label for="fb-random-questions"><input type="checkbox" name="fb-random-questions" id="fb-random-questions" <?php if ($q_random_questions == 1 || $edit_mode != 1) echo "checked"; ?>>Random Questions Order</label>
                            <label for="fb-random-choices"><input type="checkbox" name="fb-random-choices" id="fb-random-choices" <?php if ($q_random_choices == 1 || $edit_mode != 1) echo "checked"; ?>>Random Choices Order</label>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="fb-passing-percentage">Passing Percentage:</label></td>
                        <td><input type="text" name="fb-passing-percentage" id="fb-passing-percentage" value="<?php echo $q_passing_percentage; ?>"/>%</td>
                    </tr>                
                </table>
            </div>
        </div>
        
        <div class="fb-row">
            <div class="fb-input-field">
                <label for="fb-questions-total" class="fb-inline">Total Number of Questions:</label>        
                <span id="fb-questions-total"><?php echo $q_num_of_questions; ?></span>
            </div>
        </div>        
        <div class="fb-row">                
            <div class="fb-fieldset fb-choices-box" id="fb-questions-box">
                <fieldset>
                    <legend>Questions</legend>
                    <div class="fb-input-wrapper1">
                    <?php                                                 
                        if ($edit_mode == 1 && count($q_questions) > 0) {                                
                            echo '<div class="ui-sortable" id="fb-choices">';
                            foreach ($q_questions as $question) {
                                
                                $dumb = $wpdb->get_row( "SELECT * FROM " . $FB_TABLE['questions']  . " WHERE id=" . $question );
                                $question_title = $dumb->title;
                                
                                echo '
                                    <div class="fb-choice" data-id="' . $question . '">
                                        <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a>
                                        <a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a>
                                        <span class="fb-choice-name ui-state-default ui-sortable-handle">' . $question_title . '</span>
                                    </div>';    
                            }                                                
                            echo '</div>';
                            
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
                            $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions'] . " WHERE 1 order by id desc");
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
                        <script>
                            jQuery( "#fb-questions-table" ).DataTable({
                                "aoColumnDefs": [
                                  { 'bSortable': false, 'aTargets': [ 0 ] }
                                ],
                                "ordering": false,
                                "info": false,
                                "language": {
                                    search: '<i class="fb-icon icon-search"></i>',
                                    searchPlaceholder: "Search a question",
                                    "paginate": {
                                        "previous": "<",
                                        "next": ">",
                                    }
                                },
                                "pageLength": 25,
                                
                            });
                        </script>    
                        </div>
                    </fieldset>
                </div>
            </div>            
            <div class="fb-half-right">
                <div class="fb-fieldset fb-add-choice-box" id="fb-add-quiz-box2">
                    <fieldset>
                        <legend>Pools (categories)</legend>
                        <div class="fb-datatable-wrapper">
                            <div class="fb-questions-num-wrapper">
                                <label>Number of questions: </label><input type="number" value="3" id="fb-questions-num"/><input type="button" class="button" value="Add" id="fb-add-questions"/>
                            </div>
                            <?php
                                $cats = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions_cat'] );
                                echo '<table id="fb-categories-table" class="fb-datatable fb-fullwidth">';
                                
                                foreach ($cats as $row) {
                                    echo '                                
                                    <tr id="category_row_' . $row->id . '"><td>
                                        <div class="fb-choice">
                                            <label for="fb_cat_' . $row->id . '"><input id="fb_cat_' . $row->id . '" type="radio" value="' . $row->id . '" name="fb_cat_radio" class="fb_cat_radio"/>' . $row->name . '</label>                                        
                                        </div>
                                    
                                    </td></tr>';
                                }
                                echo '</table>';
                            ?>
                            <script>
                                jQuery( "#fb-categories-table" ).DataTable({
                                    "aoColumnDefs": [
                                      { 'bSortable': false, 'aTargets': [ 0 ] }
                                    ],
                                    "ordering": false,
                                    "info": false,
                                    "language": {
                                        search: '<i class="fb-icon icon-search"></i>',
                                        searchPlaceholder: "Search a category",
                                        "paginate": {
                                            "previous": "<",
                                            "next": ">",
                                        }
                                    },
                                    "pageLength": 5
                                });                                
                            </script>    
                        </div>
                    </fieldset>
                </div>
            </div>            
        </div>            
        <div class="fb-fieldset fb-connections-box fb-row">
            <fieldset>
                <legend>Connected to</legend>
                <div class="fb-input-wrapper">
                    <div id="fb-connections">
                    <?php
                        if (!empty($q_connected_to)) {
                            foreach ($q_connected_to as $q_connection) {                                
                                $fb_connection = get_post($q_connection);                                
                                echo '
                                    <div class="fb-choice" data-id="' . $q_connection . '">
                                        <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a>                                        
                                        <span class="fb-choice-name">[' . $cpt_labels[$fb_connection->post_type] . '] ' . $fb_connection->post_title . '</span>
                                    </div>';
                            }
                        }
                    ?>
                    </div>
                    <div class="fb-datatable-wrapper">
                    <?php
                        
                        $rows = get_posts(array('post_type' => $cpt, 'posts_per_page' => -1, 'post_status' => 'publish'));
                        
                        echo '<table id="fb-connections-table" class="fb-datatable fb-fullwidth">';
                        foreach ($rows as $row) {
                            echo '                                
                            <tr id="connection_row_' . $row->ID . '"><td>
                                <div class="fb-choice">
                                    <a data-id="' . $row->ID . '" href="javascript:void(0)" class="fb-add-connections"><i class="fb-icon icon-plus-squared"></i></a>                                        
                                    <span class="fb-choice-name">[' . $cpt_labels[$row->post_type] . '] ' . $row->post_title . '</span>
                                </div>
                            
                            </td></tr>';
                        }
                        echo '</table>';
                    ?>
                    <script>
                        jQuery( "#fb-connections-table" ).DataTable({
                            "aoColumnDefs": [
                              { 'bSortable': false, 'aTargets': [ 0 ] }
                            ],
                            "ordering": false,
                            "info": false,
                            "language": {
                                search: '<i class="fb-icon icon-search"></i>',
                                searchPlaceholder: "Search",                
                                "paginate": {
                                    "previous": "<",
                                    "next": ">"
                                }
                            },
                            "pageLength": 5
                        });
                    </script>    
                    </div>
                    <input type="hidden" class="fb-checklist-values" id="fb-connections" value=""/>
                </div>
            </fieldset>
                        
        </div>
        
        
        <input type="hidden" id="fb-author" value="<?php echo $current_user->ID; ?>"/>
        <input type="hidden" id="fb-edit-id" value="<?php echo $id; ?>"/>
        
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        
        var questions_total = $("#fb-choices .fb-choice").length;
        
        $( "#fb-choices" ).sortable();
        if ($("#fb-quiz-layout").val() == "single") {
            disableSkipping();
        }
        
        function update_quiz(status) {
            var title = $("#fb-quiz-title").val();
            var description = $("#fb-quiz-description").hasClass("tmce-active") ? tinyMCE.get("fb-quiz-description").getContent() : $("#fb-quiz-description").val();
            var author = $("#fb-author").val();
            var questions = [], connected_to = [];
            var $choices_list = $("#fb-choices .fb-choice");            
            $choices_list.each(function(i, obj) {
                question_id = $(obj).attr("data-id");
                questions.push(question_id);                
            });
            var $connections_list = $("#fb-connections .fb-choice");            
            $connections_list.each(function(i, obj) {
                connection_id = $(obj).attr("data-id");
                connected_to.push(connection_id);                
            });
            
            var passing_percentage = $("#fb-passing-percentage").val();
            var layout = $("#fb-quiz-layout").val();
            var allow_skipping = $("#fb-allow-skipping").is(":checked") ? 1 : 0;
            var immediate_feedback = $("#fb-immediate-feedback").is(":checked") ? 1 : 0;
            var random_questions = $("#fb-random-questions").is(":checked") ? 1 : 0;
            var random_choices = $("#fb-random-choices").is(":checked") ? 1 : 0;
            
            var params = { 'title': title, 'description': description, 'questions': questions, 'connected_to': connected_to, 'passing_percentage': passing_percentage, 'layout': layout, 'allow_skipping': allow_skipping,
                 'author': author, 'status': status, 'num_of_questions': questions_total, 'immediate_feedback': immediate_feedback, 'random_questions': random_questions, 'random_choices': random_choices };
            var data = {};
            if ($("#fb-edit-id").val() != '')
                data = {'action' : 'fb_edit_quiz', 'params' : params, 'id' : $("#fb-edit-id").val()};
            else
                data = {'action' : 'fb_add_quiz', 'params' : params };            
            
            fb_add(data);
        }
        
        $("#fb-publish").click(function(e) {
            update_quiz("publish");
        });
        
        $("#fb-draft").click(function(e) {
            update_quiz("draft");
        });
        
        $("#fb-delete").click(function(e) {
            
        });
        
        $("#fb-questions-table .fb-add").live("click", function(e) {
            id = $(this).attr("data-id");
            title = $(this).next(".fb-choice-name").text();
            if ( $("#fb-choices .fb-choice[data-id='" + id + "']").length != 0 ) {
                alert("This question was already added to the list.");
            } else {            
                row = ' \
                        <div class="fb-choice" data-id="' + id + '"> \
                            <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a> \
                            <a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a> \
                            <span class="fb-choice-name ui-state-default ui-sortable-handle">' + title + '</span> \
                        </div> \
                       ';            
                $(this).parents("tr").fadeOut(200, function() {
                    $("#fb-choices").append(row);    
             
                    questions_total = $("#fb-choices .fb-choice").length;
                    $("#fb-questions-total").text(questions_total);                    
                });
            }
        });
        
        $("#fb-questions-box .fb-remove").live("click", function(e) {
            var dumb = $(this).parents(".fb-choice");
            id = $(dumb).attr("data-id");
            
            if ($("#fb-edit-id").val() != '') {
                fb_block('#fb-questions-box');
                var params = { 'question_id' : id };
                data = {'action' : 'fb_remove_relationship', 'params' : params, 'id' : $("#fb-edit-id").val()};
                
                $.post(ajaxurl, data, function(response) {
                    var result = JSON.parse(response);
                    if (result['status'] == 1) {
                        fb_unblock('#fb-questions-box');
                        $(dumb).fadeOut(200, function() {
                            $(this).remove();
                            $("#question_row_" + id).show();
                            
                            questions_total = $("#fb-choices .fb-choice").length;
                            $("#fb-questions-total").text(questions_total);
                        });            
                    }
                });
            } else {
                $(dumb).fadeOut(200, function() {
                    $(this).remove();
                    $("#question_row_" + id).show();
                    
                    questions_total = $("#fb-choices .fb-choice").length;
                    $("#fb-questions-total").text(questions_total);
                });                
            }
        });
        
        $("#fb-connections .fb-remove").live("click", function(e) {
            var dumb = $(this).parents(".fb-choice");
            id = $(dumb).attr("data-id");
                        
            $(dumb).fadeOut(200, function() {
                $(this).remove();
                $("#connection_row_" + id).show();
            });
            
        });
        
        $("#fb-categories-table .fb-add").live("click", function(e) {
            id = $(this).attr("data-id");            
        });
        
        $("#fb-add-questions").click(function(e) {
            
            var cat_id = $('input[name=fb_cat_radio]:checked').val();
            var num_questions = $("#fb-questions-num").val();
            if (typeof cat_id != "undefined" && cat_id != "") {
                var data = {'action' : 'fb_get_random_questions_by_category', 'id' : cat_id, 'num_questions' : num_questions};
                fb_block('.fb-wrap');
                $.post(ajaxurl, data, function(response) {
                    var result = JSON.parse(response);
                    var row = '';
                    fb_unblock('.fb-wrap'); 
                    if (result['status'] == 1) {
                        for (i = 0; i < result['rows'].length; i++) {
                            console.log(result['rows'][i]);
                            id = result['rows'][i]['id'];
                            title = result['rows'][i]['title'];
                            if ( $("#fb-choices .fb-choice[data-id='" + id + "']").length == 0 ) {
                                row = ' \
                                        <div class="fb-choice fb-from-cats" data-id="' + id + '"> \
                                            <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a> \
                                            <a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a> \
                                            <span class="fb-choice-name ui-state-default ui-sortable-handle">' + title + '</span> \
                                        </div> \
                                       ';
                                $("#fb-choices").append(row);
                                questions_total = $("#fb-choices .fb-choice").length;
                                $("#fb-questions-total").text(questions_total);
                            }
                        }
                        
                    }
                });
            } else {
                alert("Please select a category to add.");
            }
        });
        
        $("#fb-quiz-layout").change(function(e) {
            if ($(this).val() == "single") {
                disableSkipping();
            } else {
                enableSkipping();                
            }
        });
        
        $(".fb-add-connections").click(function(e) {
            id = $(this).attr("data-id");
            title = $(this).next(".fb-choice-name").text();
            
            if ( $("#fb-connections .fb-choice[data-id='" + id + "']").length != 0 ) {
                alert("This item was already added to the list.");
            } else {            
                row = ' \
                        <div class="fb-choice" data-id="' + id + '"> \
                            <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a> \
                            <span class="fb-choice-name">' + title + '</span> \
                        </div> \
                       ';            
                $(this).parents("tr").fadeOut(200, function() {
                    $("#fb-connections").append(row);
                });
            }
        });
        
        function disableSkipping() {
            $("#fb-allow-skipping").prop("disabled", true);
            $("#fb-allow-skipping").parents("label").addClass("fb-disabled");
        }
        function enableSkipping() {
            $("#fb-allow-skipping").prop("disabled", false);
            $("#fb-allow-skipping").parents("label").removeClass("fb-disabled");
        }
        
        
    });
    
</script>
