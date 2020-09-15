<?php

function writeToDb($data)
{
    global $fp;
    $dbh = getDbCon();
    try {
        $stmt = $dbh->prepare('INSERT INTO questions(question) VALUES (:question)');
        $stmt->bindParam(':question', $data['question']);
        $stmt->execute();
        $question_id = $dbh->lastInsertId();

        $stmt = $dbh->prepare('INSERT INTO answers(answer,length) VALUES(:answer, :length)');
        foreach ($data['answer'] as $key => $value) {
            $stmt->bindParam(':answer', $value);
            $stmt->bindParam(':length', $data['length'][$key]);
            $stmt->execute();
            $answer_id[] = $dbh->lastInsertId();
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
        fwrite($fp,"$e->getMessage() \n");
        throw $e;
    }
    $dbh->commit();
    fwrite($fp,"Successful written id = $question_id \n");
}