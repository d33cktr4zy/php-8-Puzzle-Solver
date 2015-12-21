<?php
/**
 * Created by PhpStorm.
 * User: gabriel
 * Date: 12/17/2015
 * Time: 14:47 PM
 */

namespace App\Puzzle;

use App\Model\distance;
use App\Puzzle\board;
use Psy\Test\CodeCleaner\AssignThisVariablePassTest;

class Solver2
{
    public $opened;
    protected $closed;
    protected $goalBoard;
    protected $startBoard;

    public $evalBoard;

    public function __construct(board $startBoard, board $goalBoard){
        $this->startBoard = $startBoard;
        $this->goalBoard = $goalBoard;
    }

    /**
     *
     */
    public function solvePuzzle(){
        //getting distance of the input board to the board
        $this->startBoard->distanceFromGoal = $this->getTileDistanceToGoal($this->startBoard);
        $this->goalBoard->distanceFromGoal = $this->getTileDistanceToGoal($this->goalBoard);

        $board = $this->startBoard; //for first iteration
        $goal = $this->goalBoard;

        //adding the current board to the opened list
        $this->addOpened($board); //need to add the start board before iteration

        //initializing the level
        $levels = 0;

        //starting iteration
        while($board->tiles !== $goal->tiles){
            //we need to unset the $newBoardTiles with every iteration
            if(isset($newBoardTiles)){unset($newBoardTiles);}

            //with every iteration we increase the tree level
            $levels++;
            echo '<h1>Starting Iteration No. '.$levels . ' ... </h1><br />';
            echo 'distance = ' . $board->distanceFromGoal . ' -- Move Done = ' . $board->moveSoFar .'<br />';
            //with every iteration start, we close the current board
            $this->addClosed($board);

            //main thingy
            //clearing if there is still children array in every new iteration
            //children is the neighbouring board of the $board (currently evaluated board)
            //it is only reachable within 1 move of the blank tile.
            if(isset($children)){unset($children);}

            //generate possible children
            foreach($board->validMovement as $movement){
                $children[$movement] = $this->moveBlankTile($board,$movement);
            }

            //now that the board has children, we need to check
            //if every children is unique.
            foreach($children as $key=>$tiles){
                if($this->isBoardUnique($tiles)){
                    //make an array that contain the tiles of will be
                    //generated board
                    $newBoardTiles[$key] = $tiles;
//                    echo '<pre>New TIles :'.print_r($newBoardTiles).'<br />';
                }
            }

            //weather that there is a new board will be created or not
            if(isset($newBoardTiles)){
                foreach($newBoardTiles as $key => $tiles) {
                    $newBoard = $this->createNewBoard(
                        $board,
                        $tiles,
                        $key
                    );
//                    dd("$newBoard = ",$this->{$newBoard});
                    //open each valid new board
                    $this->addOpened($this->{$newBoard});
//                    echo '<pre>Opened List :'.print_r($this->opened,true).'<br />';
//                    echo '<pre>ClosedList :'.print_r($this->closed,true) . '<br />';
                }
            }


            //finding the best node to evaluate
//            if(isset($costList)){unset($costList);}
//            foreach($this->opened as $key=>$board){
////                echo 'key = '. $key.'<br/>';
//                $costList[$key] = $board->distanceFromGoal;
//
//            }
//            echo '<pre>costlist = '. print_r($costList,true);
//            $minimumCost = min($costList);
//
//            echo '<pre>Minimum = ' . $minimumCost . '<br/>';
//
//            foreach($this->opened as $key=>$board){
//                $pQueue[] = $board->moveSoFar;
//            }
//            echo '<pre> PQUEUE = '. print_r($pQueue, true) . '<br/>';
//            if(isset($candidates)){unset($candidates);}
//            foreach($this->opened as $pord){
//                if($pord->moveSoFar === min($pQueue)&& $pord->distanceFromGoal === $minimumCost){
//                    $candidates[] = $pord;
//            echo '<pre> Candidates = '. print_r($candidates, true) . '<br/>';
//
//                }
//
//            }

//            $bestKey = min(array_keys($costList,$minimumCost));
//            echo '<pre>Best Key = '. print_r($bestKey,true);

//            $board = $this->opened[$bestKey];
            if(isset($costList)){unset($costList);}
            foreach($this->opened as $key=>$board){
                //make a new array containing only the total Cost of each memeber of the queue
                $costList[$key] = $board->distanceFromGoal;

            }

            //the costlist will always return an array.
            //problem arise when the costlist contain more then one element
            if(isset($minimumKeys)){unset($minimumKeys);}
            if(isset($levelsOfMinimums)){unset($levelsOfMinimums);}
            if(isset($lowestLevelOfMinimums)){unset($lowestLevelOfMinimums);}
            if(isset($nameOfLowest)){unset($nameOfLowest);}
            if(count($minimumKeys = array_keys($costList,min($costList))) > 1){
                //we need to check if the result is in the same level.
                //if not, we choose the lowest level first as board
                foreach($minimumKeys as $c=>$openedKey){
                    $levelsOfMinimums[$openedKey] = $this->opened[$openedKey]->moveSoFar;
                }

                if(count($lowestLevelOfMinimums = array_keys($levelsOfMinimums, min($levelsOfMinimums))) > 1){
                   //meaning there is more than one board with the same level
                    foreach ($lowestLevelOfMinimums as $co => $openKey) {
                        //get the nname of each
                        $nameOfLowest[$openKey] = $this->opened[$openKey]->name;
                   }

                $bestKey = array_keys($nameOfLowest, min($nameOfLowest));
                }else{
                    $bestKey = array_keys($levelsOfMinimums, min($levelsOfMinimums));
                }
                //if its the same level, compare the name
            }else{
                $bestKey = array_keys($costList, min($costList));
            }
//            echo '<pre>'print_r();

            //so after we have accuired on our hand a list of the distance from the goal,
            //now we can select the minimum distance and select the board as best node

                $board = $this->opened[$bestKey[0]];
//            dd($board);
            $this->evalBoard = $board;
                $this->drawBoard($board);
//            dd($this->opened, array_pop($this->opened), $this->opened);

//            echo '<pre>Cost List : ' . print_r($costList,true);


//            if($levels === 10){
//                break;
//            }
        }

        echo 'Solution Found : <pre>' . print_r($board,true);
        $this->drawBoard($board);

        $movementDone = array_reverse($this->traceBack($this->evalBoard));
        dd($movementDone);

        echo '<pre>Movement Done : <br />';
        echo '<div class="container">';
        echo '
            <table><tr>';
        echo '<td>Start Board</td><td>';
        $this->drawBoard($this->startBoard);
        echo '</td></tr>';
        $counter = 1;
        foreach($movementDone as $steps){
            echo '<tr><td> Step ' . $counter . ' - ' . $steps->lastMovement .'</td>';
            echo '<td>' . $this->drawBoard($steps) .'</td></tr>';
            $counter++;
        }

        echo'
            </table></div>
        ';
    }


    private function isBoardUnique($tiles){
        //checking if the board is unique
        //checking if the board is unique is done by comparing its tiles with those board
        // that has already be in the opened queue and closed queue
        //if the tiles that is passed to this method is not in the open and also not in the closed
        //then the board is considered unique.
        //
        //note: we didn't check against its parent tile. Since the parent tile is supposedly already
        //exist in the closed queue, it should return false if the board is in the closed queue meaning
        // its thesame with the parent.
        $isInTheOpened = false;
        $isInTheClosed = false;
        $isUnique = false;

        if(null !== $this->opened){
            //only reached when the opened is NOT null
            foreach($this->opened as $key=>$openBoard) {
                if ($tiles === $openBoard->tiles){
                    //if the tile
                    $isInTheOpened = true;
                }
            }
        }

        if(null !== $this->closed){
            foreach($this->closed as $key=>$closedBoard){
                if($tiles === $closedBoard->tiles){
                    $isInTheClosed = true;
                }
            }
        }

        if(!$isInTheClosed && !$isInTheOpened){
            $isUnique = true;
        }

        return $isUnique;
    }

    public function drawBoard(board $board){
        echo '<div style="font-size: larger; font-weight: bold;float: " >
            <p>'. $board->distanceFromGoal .'</p>
            <p>' . $board->moveSoFar . '</p>
            <table border="1px" cellpadding="0px" style="border: 1px solid;">';
        foreach($board->tiles as $row=>$col){
            echo '<tr>';
            foreach($col as $key=>$val){
                if($val === 'b'){
                    echo '<td bgcolor="black" width="30px" height="30px">&nbsp;</td>';
                }else {
                    $bl = $board->tiles;
                    $warna = $bl['B'][2].$bl['C'][1].$board->blankLoc.$bl['A'][3];
                    echo '<td width="30px" height="30px" align="center" bgcolor="#' . $warna . '" style="font-size: 14pt; font-weight: bold; color: white;">' . $val . '</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table></div>';
    }

    private function addOpened(board $board){
        //add board to open list
        $this->opened[] = $board;
    }

    private function addClosed(board $board){
        $key = array_search($board, $this->opened);
            //when you add something to the closed list
            //you also need to remove it from the opened list
            $this->closed[] = $board;
            unset($this->opened[$key]);

    }

    /**      4
     * @param \App\Puzzle\board $board
     * @return number
     */
    public function getTotalCost(board $board){
        //this is a simple method to calculate the total cost of each board passed
        $manhattanDistance = $this->getTileDistanceToGoal($board);
        $hammingDistance = $this->getHammingValue($board);
        if(!$manhattanDistance == 0 || !$hammingDistance == 0) {
            $totalCost = round(((($manhattanDistance / $hammingDistance) * 9) / $board->moveSoFar), 2) * 100;
        }else{
            $totalCost = 0;
        }

        return $totalCost;
    }

    /**
     * @param \App\Puzzle\board $board
     * @return number
     */
    public function getTileDistanceToGoal(board $board)
    {
        //every tile of the new board will have a value that shows the value distance
        //to the supposed position of that value to the goal

        //get the location of the value in the goal board
        $valueLocationInGoal = $this->getTileName($this->goalBoard);
        //get the location of $tileValue in the $board
        $valueLocationInBoard = $this->getTileName($board);

        $distance = new distance;

        //after we have the location of each value
//        echo print_r($valueLocationInGoal, 1);
//        echo print_r($valueLocationInBoard, 1);

        foreach($valueLocationInBoard as $value=>$location){
            //get the location of the $value from the valueLocationInGoal Array
            $locationOfValueInGoal = $valueLocationInGoal[$value];
            //get the location of the $value from the valueLocationInBoard Array
            $locationOfValueInBoard = $valueLocationInBoard[$value];
            //get the cost it needs to the goal
            $distanceCost[$value] = $distance->where('node1',$locationOfValueInBoard)
                                     ->where('node2',$locationOfValueInGoal)
                                     ->orWhere(function($q) use ($locationOfValueInBoard,$locationOfValueInGoal){
                                         $a = $locationOfValueInBoard;
                                         $b = $locationOfValueInGoal;
                                         $q->where('node1',$b)->where('node2',$a);
                                     })->pluck('cost')
            ;

        }
//        var_dump($distanceCost,$board->moveSoFar);
        $totalG = array_sum($distanceCost);
//        $totalG = array_sum($distanceCost);

        return $totalG;


    }

    /**
     * @param \App\Puzzle\board $board
     * @return number
     */
    public function getHammingValue(board $board){
        //this method will calculate the hamming value of the currently evaluated board
        $locationOfTheValueInBoard = $this->getTileName($board);
        $locationOfTheValueInGoal = $this->getTileName($this->goalBoard);

        foreach($locationOfTheValueInBoard as $value=>$location){
            if($location === $locationOfTheValueInGoal[$value]){
                $hammingValue[$location] = 0;
            }else{
                $hammingValue[$location] = 1;
            }
        }
//            dd($locationOfTheValueInBoard,$locationOfTheValueInGoal,$hammingValue);
        //now that we have the hamming value of each location,
        //we need to sum that value up.
        $totalHammingValue = array_sum($hammingValue);

        return $totalHammingValue;


    }

    /**
     * @param \App\Puzzle\board $board
     * @return mixed
     */
    public function getTileName(board $board){
        //this function will get the tile location of each value
        foreach($board->tiles as $row=>$col){
            foreach ($col as $column => $value) {
                if($value !== 'b') {
                    $location[$value] = $row . $column;
                }
            }
        }
        return $location;
    }

    /**
     * Only move the blank tiles
     * ---------------------------------
     * @param \App\Puzzle\board $board
     * @param $move
     * @return array
     */
    public function moveBlankTile(board $board, $move){
        if($move === 'U'){
            $tiles = $this->moveUp($board);
        }elseif($move === 'D'){
            $tiles = $this->moveDown($board);
        }elseif($move === 'L'){
            $tiles = $this->moveLeft($board);
        }elseif($move === 'R'){
            $tiles = $this->moveRight($board);
        }
        return $tiles;
    }

    /**
     * @param \App\Puzzle\board $board
     * @return array
     */
    private function moveUp(board $board){
        echo 'Moving UP <br />';

        $oldLocation = str_split($board->blankLoc);
        $row = $oldLocation[0];
        $col = $oldLocation[1];
        $target = chr(ord($row)-1);

        //getting the value of old positions
        $oldValue = [
            $board->tiles[$row][$col], //the blank location
            $board->tiles[$target][$col] //the target location
        ];

        //getting the tiles position to be swapped
        $newTiles = $board->tiles;

        //start swapping
        //filling where the blank were with the x value
        $newTiles[$row][$col] = $oldValue[1];
        //filling where the x were with the blank value
        $newTiles[$target][$col]= $oldValue[0];

        //returning the new tiles position
        return $newTiles;

    }

    private function moveDown(board $board){
        echo 'Moving Down <br />';

        $oldLocation = str_split($board->blankLoc);
        $row = $oldLocation[0];
        $col = $oldLocation[1];
        $target = chr(ord($row)+1);

        //getting the value of old positions
        $oldValue = [
            $board->tiles[$row][$col], //the blank location
            $board->tiles[$target][$col] //the target location
        ];

        //getting the tiles position to be swapped
        $newTiles = $board->tiles;

        //start swapping
        //filling where the blank were with the x value
        $newTiles[$row][$col] = $oldValue[1];
        //filling where the x were with the blank value
        $newTiles[$target][$col]= $oldValue[0];

        //returning the new tiles position
        return $newTiles;

    }

    private function moveLeft(board $board){
        echo 'Moving Left <br />';
        $oldLocation = str_split($board->blankLoc);
        $row = $oldLocation[0];
        $col = $oldLocation[1];
        $target = chr(ord($col)-1);

        //getting the value of old positions
        $oldValue = [
            $board->tiles[$row][$col], //the blank location
            $board->tiles[$row][$target] //the target location
        ];

        //getting the tiles position to be swapped
        $newTiles = $board->tiles;

        //start swapping
        //filling where the blank were with the x value
        $newTiles[$row][$col] = $oldValue[1];
        //filling where the x were with the blank value
        $newTiles[$row][$target]= $oldValue[0];

        //returning the new tiles position
        return $newTiles;

    }

    private function moveRight(board $board){
        echo 'Moving Right <br />';

        $oldLocation = str_split($board->blankLoc);
        $row = $oldLocation[0];
        $col = $oldLocation[1];
        $target = chr(ord($col)+1);

        //getting the value of old positions
        $oldValue = [
            $board->tiles[$row][$col], //the blank location
            $board->tiles[$row][$target] //the target location
        ];

        //getting the tiles position to be swapped
        $newTiles = $board->tiles;

        //start swapping
        //filling where the blank were with the x value
        $newTiles[$row][$col] = $oldValue[1];
        //filling where the x were with the blank value
        $newTiles[$row][$target]= $oldValue[0];

        //returning the new tiles position
        return $newTiles;

    }

    /**
     * @param \App\Puzzle\board $parentBoard
     * @param $tiles
     * @param $direction
     * @return string
     */
    private function createNewBoard(board $parentBoard, $tiles, $direction){
        //this function will return new board
        $oldLevels = $parentBoard->moveSoFar;
        $direction = $direction;

        //check if the parentboard is start board or not
        if(null === $parentBoard->parentBoard){
            //its the starting board, so assign parent of the will be created board
            //to 'start'
            $newParent = 'start';
        }else{
            //parent = 'start-1U'
            $newParent = $parentBoard->parentBoard . '-' . $oldLevels . $parentBoard->lastMovement;
        }

        //making the new board level
        $newLevel = $oldLevels + 1;

        //generate new name for the will be created board
        //new name will be like 'start-1U-2D' etc
        $newName = $newParent . '-' . $newLevel . $direction;

        $this->{$newName} = new board(
            $tiles['A'][1],
            $tiles['A'][2],
            $tiles['A'][3],
            $tiles['B'][1],
            $tiles['B'][2],
            $tiles['B'][3],
            $tiles['C'][1],
            $tiles['C'][2],
            $tiles['C'][3],
            $newLevel,
            $newParent,
            $direction,
            $newName
        );

        //calculate each newboard distance to goal
        $this->{$newName}->distanceFromGoal = $this->getTotalCost($this->{$newName});

        return $newName;
    }

    public function traceBack(board $finalBoard){
//        echo '<pre><h2><strong>The parent board first :</strong></h2>' . $finalBoard->parentBoard;
        for($i = $finalBoard->moveSoFar;$i > 0; $i--){
            $curBoard = $finalBoard->name; //the name of the final board
//            echo '<pre>' . print_r($curBoard,true);
            if($finalBoard->parentBoard !== 'start') {
                $parentBoard = $finalBoard->parentBoard;
//                echo '<pre>parent board = ' . print_r($parentBoard,true);
            }else{
                $parentBoard = 'startBoard';
//                echo '<pre>parent board = ' . print_r($parentBoard,true);
            }
            $tracer[] = $this->{$curBoard};
//            echo '<pre>Tracer Content = '. print_r($tracer,true);

            $finalBoard = $this->{$parentBoard};
//            echo '<pre> Final Board = ' . print_r($finalBoard);
        }
        return $tracer;
    }

}