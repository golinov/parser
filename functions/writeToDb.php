<?php

function writeToDb($data)
{
    global $error;
    global $success;
    $dbh = getDbCon();
    $dbh->beginTransaction();
    try {
        $checkQuestion = $dbh->prepare('SELECT question_id FROM questions WHERE question = ?');
        $checkQuestion->execute([
            $data['question']
        ]);
        $questionResult = $checkQuestion->fetchColumn();
        if (!$questionResult) {
            $question = $dbh->prepare('INSERT INTO questions(question) VALUES (?)');
            $question->execute([
                $data['question']
            ]);
            $question_id = $dbh->lastInsertId();
        } else $question_id = $questionResult;

        $checkAnswer = $dbh->prepare('SELECT answer_id FROM answers WHERE answer = ?');
        $answer = $dbh->prepare('INSERT INTO answers(answer,length) VALUES(?, ?)');
        foreach ($data['answer'] as $key => $value) {
            $checkAnswer->execute([
                $value
            ]);
            $answer_id[$key] = $checkAnswer->fetchColumn() ?? null;
            if (empty($answer_id[$key])) {
                $answer->execute([
                    $value,
                    $data['length'][$key]
                ]);
                $answer_id[$key] = $dbh->lastInsertId();
            }
        }

        $stmt = $dbh->prepare('INSERT INTO QuestionsAnswers(question_id,answer_id) VALUES(?, ?)');
        foreach ($answer_id as $value) {
            $stmt->execute([
                $question_id,
                $value
            ]);
        }
    } catch (PDOException|Exception $e) {
        $dbh->rollBack();
        fwrite($error, $e->getMessage() . "\n");
        return false;
    }
    fwrite($success, "Successful written id = $question_id \n");
    return $dbh->commit();
}