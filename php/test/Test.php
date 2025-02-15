<?php

use Game\GameRunner;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{


    public function testGameIsPLayable()
    {
        srand(123455);
        ob_start();

        GameRunner::runGame(["Chet", "Pat", "Sue"]);

        $actual = ob_get_contents();
        ob_end_clean();

        $expected = file_get_contents(__DIR__ . '/approved.txt');
        $this->assertEquals($expected, $actual);

    }

    public function testGameIsNoTPlayable()
    {
        srand(123455);
        ob_start();

        GameRunner::runGame(["Chet"]);

        $actual = ob_get_contents();
        ob_end_clean();

        $expected = file_get_contents(__DIR__ . '/gameIsNotPlayable.txt');
        $this->assertEquals($expected, $actual);
    }
}
