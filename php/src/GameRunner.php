<?php
namespace Game;

class GameRunner {

    public static function runGame(array $players)
    {

        $aGame = new Game();
        foreach ($players as $player) {
            $aGame->add($player);
        }

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
        else {
            echoln("Game is not playable");
        }
    }

}



