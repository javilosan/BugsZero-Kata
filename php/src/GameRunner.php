<?php
namespace Game;

class GameRunner {

    public static function runGame()
    {

        $aGame = new Game();

        $aGame->add("Chet");
        $aGame->add("Pat");
        $aGame->add("Sue");


        if ($aGame->isPlayable()) {
            do {
                $aGame->roll(mt_rand(0,5) + 1);

                if (mt_rand(0,9) === 7) {
                    $notAWinner = $aGame->wrongAnswer();
                } else {
                    $notAWinner = $aGame->wasCorrectlyAnswered();
                }
            } while ($notAWinner);
        }
    }
}



