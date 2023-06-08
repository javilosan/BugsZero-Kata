<?php


namespace Game;


final class QuestionsCollection
{
    public $list;

    public function __construct()
    {
        $this->list = [];
    }

    public function add(Question $question)
    {
        $this->list[] = $question;
    }
}