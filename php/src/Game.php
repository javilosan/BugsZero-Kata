<?php
namespace Game;
function echoln($string) {
    echo $string."\n";
}
//TODO: Hacer un esquema del juego rollo Excalidraw, Miro, ... y mira qué partes vamos a refactorizar
class Game {
    const MINIMUN_PLAYER_COUNT = 2;
    const MAXIMUM_PLAYER_COUNT = 7;
    const BOARD_SIZE = 12;
    const WIN_SCORE = 6;
    const NUMBER_OF_QUESTIONS = 50;

    var $players;
    var $places;
    var $purses;
    var $inPenaltyBox ;

    var $popQuestions;
    var $scienceQuestions;
    var $sportsQuestions;
    var $rockQuestions;

    var $currentPlayer = 0;
    var $isGettingOutOfPenaltyBox;

    function  __construct(){

        $this->players = array();
        $this->places = array(0);
        $this->purses  = array(0);
        $this->inPenaltyBox  = array(0);

        $this->popQuestions = array();
        $this->scienceQuestions = array();
        $this->sportsQuestions = array();
        $this->rockQuestions = array();

        $this->prepareQuestions(self::NUMBER_OF_QUESTIONS);
    }

    function createRockQuestion($index){
        return "Rock Question " . $index;
    }

    public function isPlayable() {
        return ($this->howManyPlayers() >= self::MINIMUN_PLAYER_COUNT
            && $this->howManyPlayers() <= self::MAXIMUM_PLAYER_COUNT);
    }

    function add($playerName) {
        array_push($this->players, $playerName);
        $this->places[$this->howManyPlayers()] = 0;
        $this->purses[$this->howManyPlayers()] = 0;
        $this->inPenaltyBox[$this->howManyPlayers()] = false;

        echoln($playerName . " was added");
        echoln("They are player number " . $this->howManyPlayers());
        return true;
    }

    function howManyPlayers() {
        return count($this->players);
    }

    function roll($roll) {
        echoln($this->players[$this->currentPlayer] . " is the current player");
        echoln("They have rolled a " . $roll);

        // Jugador hace una tirada de ROLL pasos
        // EL jugador está en prision?
        // NO: Se avanza ROLL posiciones y Se hace pregunta
        //    Si acierta se añade 1 gold coin. Si tiene 6 coins GANA! si no tien 6 PASA TURNO
        //    Si no acierta se QUEDA EN PRISON y PASA TURNO

        // SI: el ROLL es un número impar?
        //    SI: el jugador avanza ROLL pasos y Se le hace pregunta
        //        Si acierta sale de prision
        //        Si no acierta se qued aen prision y pasa turno
        //    NO: Salta turno

        // FALLO A ARREGLAR: Quitar la responsibilidad de la gestion del juego a GameRunner

        if ($this->inPenaltyBox[$this->currentPlayer]) {
            if ($roll % 2 != 0) {
                $this->isGettingOutOfPenaltyBox = true;
                echoln($this->players[$this->currentPlayer] . " is getting out of the penalty box");
                $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;

                //TODO: Extraer a funcion
                if ($this->places[$this->currentPlayer] >= self::BOARD_SIZE) $this->places[$this->currentPlayer] -= self::BOARD_SIZE;

                echoln($this->players[$this->currentPlayer]
                    . "'s new location is "
                    .$this->places[$this->currentPlayer]);
                echoln("The category is " . $this->currentCategory());

                $this->askQuestion();
            } else {
                echoln($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
                $this->isGettingOutOfPenaltyBox = false;
            }

        } else {

            $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
            //TODO: Extraer a funcion
            if ($this->places[$this->currentPlayer] >= self::BOARD_SIZE) $this->places[$this->currentPlayer] -= self::BOARD_SIZE;

            echoln($this->players[$this->currentPlayer]
                . "'s new location is "
                .$this->places[$this->currentPlayer]);
            echoln("The category is " . $this->currentCategory());

            $this->askQuestion();
        }

    }

    function  askQuestion() {
        if ($this->currentCategory() == "Pop")
            echoln(array_shift($this->popQuestions));
        if ($this->currentCategory() == "Science")
            echoln(array_shift($this->scienceQuestions));
        if ($this->currentCategory() == "Sports")
            echoln(array_shift($this->sportsQuestions));
        if ($this->currentCategory() == "Rock")
            echoln(array_shift($this->rockQuestions));
    }

    function currentCategory() {
        if ($this->places[$this->currentPlayer] == 0) return "Pop";
        if ($this->places[$this->currentPlayer] == 4) return "Pop";
        if ($this->places[$this->currentPlayer] == 8) return "Pop";
        if ($this->places[$this->currentPlayer] == 1) return "Science";
        if ($this->places[$this->currentPlayer] == 5) return "Science";
        if ($this->places[$this->currentPlayer] == 9) return "Science";
        if ($this->places[$this->currentPlayer] == 2) return "Sports";
        if ($this->places[$this->currentPlayer] == 6) return "Sports";
        if ($this->places[$this->currentPlayer] == 10) return "Sports";
        return "Rock";
    }

    function wasCorrectlyAnswered() {
        if ($this->inPenaltyBox[$this->currentPlayer]){
            if ($this->isGettingOutOfPenaltyBox) {
                echoln("Answer was correct!!!!");
                $this->purses[$this->currentPlayer]++;
                echoln($this->players[$this->currentPlayer]
                    . " now has "
                    .$this->purses[$this->currentPlayer]
                    . " Gold Coins.");

                $winner = $this->didPlayerWin();
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;

                return $winner;
            } else {
                $this->currentPlayer++;
                if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
                return true;
            }

        } else {

            echoln("Answer was corrent!!!!");
            $this->purses[$this->currentPlayer]++;
            echoln($this->players[$this->currentPlayer]
                . " now has "
                .$this->purses[$this->currentPlayer]
                . " Gold Coins.");

            $winner = $this->didPlayerWin();
            $this->currentPlayer++;
            if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;

            return $winner;
        }
    }

    function wrongAnswer(){
        echoln("Question was incorrectly answered");
        echoln($this->players[$this->currentPlayer] . " was sent to the penalty box");
        $this->inPenaltyBox[$this->currentPlayer] = true;

        $this->currentPlayer++;
        if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
        return true;
    }


    function didPlayerWin() {
        return !($this->purses[$this->currentPlayer] == self::WIN_SCORE);
    }

    /**
     * @param int $numberOfQuestions
     * @return void
     */
    private function prepareQuestions($numberOfQuestions)
    {
        for ($i = 0; $i < $numberOfQuestions; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, ("Science Question " . $i));
            array_push($this->sportsQuestions, ("Sports Question " . $i));
            array_push($this->rockQuestions, $this->createRockQuestion($i));
        }
    }
}
