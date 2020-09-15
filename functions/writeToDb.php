<?php

function writeToDb($data)
{
    global $error;
    global $success;
    $dbh = getDbCon();
    try {
        $checkQuestion = $dbh->prepare('SELECT question_id FROM questions WHERE question = :question');
        $checkQuestion->bindParam(':question', $data['question']);
        $checkQuestion->execute();
        $questionResult = $checkQuestion->fetchColumn();
        if (!$questionResult) {
            $question = $dbh->prepare('INSERT INTO questions(question) VALUES (:question)');
            $question->bindParam(':question', $data['question']);
            $question->execute();
            $question_id = $dbh->lastInsertId();
        } else $question_id = $questionResult;

        $checkAnswer = $dbh->prepare('SELECT answer_id FROM answers WHERE answer = :answer');
        foreach ($data['answer'] as $key => $value) {
            $checkAnswer->bindParam(':answer', $value);
            $checkAnswer->execute();
            $answer_id[$key] = $checkAnswer->fetchColumn();
            if ($answer_id[$key]) {
                unset($data['answer'][$key]);
                unset($data['length'][$key]);
            }
        }
        $answer = $dbh->prepare('INSERT INTO answers(answer,length) VALUES(:answer, :length)');
        foreach ($data['answer'] as $key => $value) {
            $answer->bindParam(':answer', $value);
            $answer->bindParam(':length', $data['length'][$key]);
            $answer->execute();
            $answer_id[$key] = $dbh->lastInsertId();
        }

        $stmt = $dbh->prepare('INSERT INTO QuestionsAnswers(question_id,answer_id) VALUES(?, ?)');
        foreach ($answer_id as $value) {
            $stmt->execute([
                $question_id,
                $value
            ]);
        }
    } catch (PDOException $e) {
        $dbh->rollBack();
        fwrite($error, $e->getMessage() . "\n");
        throw $e;
    }
    if($dbh->commit())
    {
        fwrite($success, "Successful written id = $question_id \n");
        return true;
    }
    return false;
}