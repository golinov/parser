<?php

function writeToDb($data)
{
    $dbh = getDbCon();
    try {
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction();
        $stmt = $dbh->prepare('INSERT INTO questions(question) VALUES (:question)');
        $stmt->bindParam(':question', $data['question']);
        $stmt->execute();
        $question_id = $dbh->lastInsertId();
        $stmt = $dbh->prepare('INSERT INTO answers(answer,length) VALUES(:answer, :length)');
        $stmt->bindParam(':answer', $data['answer']);
        $stmt->bindParam(':length', $data['length']);
        $stmt->execute();
        $answer_id = $dbh->lastInsertId();
        $dbh->exec("INSERT INTO QuestionsAnswers(question_id,answer_id) VALUES($question_id, $answer_id)");
        $dbh->commit();
    } catch (PDOException $e) {
        $dbh->rollBack();
        throw $e;
//    $e->getMessage(); //записать в файл
    }
}