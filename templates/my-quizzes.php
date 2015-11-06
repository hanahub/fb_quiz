<?php

    global $wpdb, $FB_TABLE, $FB_URL, $user_ID, $quizzes;

    $rows = $wpdb->get_results("SELECT q.id as qid, q.title as title, count(a.quiz_id) as attempts, a.id as aid FROM " . $FB_TABLE['quizzes'] . 
            " as q INNER JOIN " . $FB_TABLE['answers'] . " as a ON a.quiz_id=q.id WHERE a.student_id=" . $user_ID . " GROUP BY a.quiz_id" );              
            
          
                
?>

<div class="fb_wrap">
    <h3 class="fb_title">My Quizzes</h3>    
    <table class="quizzes" id="quizzes-table">
        <thead>
            <tr>                
                <th id="column-quiz-id" class=""></th>
                <th id="column-quiz-title" class="">Quiz Title</th>
                <th id="column-quiz-attempts" class="">Total number of attempts</th>
                <th id="column-last-score" class="">Last Score</th>
                <th id="column-highest-score" class="">Highest Score</th>
                <th id="column-quiz-action" class=""></th>                
            </tr>
        </thead>
        <tbody id="the-list">
        <?php
            $i = 1;
            foreach ($rows as $row) {
                $last_score = $quizzes->getLastScore($row->qid, $user_ID);
                $highest_score = $quizzes->getHighestScore($row->qid, $user_ID);
                
                /*<td><a class="fb_link" href="' . $FB_URL['my-quizzes'] . $row->qid . '">' . $row->title . '</a></td>*/
                echo '
                    <tr>
                        <td>' . $i . '</td>
                        <td>' . $row->title . '</td>                        
                        <td>' . $row->attempts . '</td>
                        <td>' . $last_score . '</td>
                        <td>' . $highest_score . '</td>
                        <td><a class="fb_link" href="' . $FB_URL['quizzes'] . $row->qid . '">Take quiz again</a></td>
                    </tr>
                ';
                
                $i ++;
            }
        ?>
        </tbody>
    </table>
</div>    
