<?php
    
    
?>

<div class="wrap">
    <h1>Add New/Edit Question</h1>
    <div class="fb-half-left">
        <div class="fb-input-field fb-row">
            <label for="fb-question">Question:</label>        
            <?php wp_editor( '', 'fb-question', $settings = array('textarea_name'=>'fb-question', 'editor_height'=>'200px') ); ?>
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
                    <div class="fb-input-wrapper1" id="fb-choices">
                                    
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="fb-input-field fb-row">
            <label for="fb-explanation">Correct Choice Explanation:</label>        
            <?php wp_editor( '', 'fb-explanation', $settings = array('textarea_name'=>'fb-explanation', 'editor_height'=>'200px') ); ?>
        </div>
        <div class="fb-fieldset fb-connected-to-box fb-row">
            <fieldset>
                <legend>Connected to</legend>
                <div class="fb-input-wrapper1">
                                
                </div>
            </fieldset>
        </div>
    </div>
</div>
