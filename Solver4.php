<?php
/**
 * Created by PhpStorm.
 * User: gabriel
 * Date: 12/19/2015
 * Time: 20:22 PM
 */

namespace App\Puzzle;


use App\Model\distance;
use App\Puzzle\board as board;



class Solver4
{
    public $openedList;
    public $closedList;
    public $startNode;
    public $goalNode;

    public $evalBoard;


    /**
     * @param \App\Puzzle\board $startNode
     * @param \App\Puzzle\board $goalNode
     */
    public function __construct(board $startNode, board $goalNode)
    {
        $this->startNode = $startNode;
        $this->goalNode = $goalNode;
    }


    public function solvePuzzle(){

        //get initial distance
        $this->startNode->distanceFromGoal = $this->getTotalCost($this->startNode);
        $this->goalNode->distanceFromGoal = $this->getTotalCost($this->goalNode);
//        dd($this->startNode);
        //this solver will embrace the raw BFS Algorithm
        //THis solver will try to solve the puzzle by going node to node in the same level
        //We don't need distance here. we will just using the level of the node to pick which next node we would open
        $board  = $this->startNode;
        $goal = $this->goalNode;
        echo '<h2>StartBoard</h2>';
        $this->drawBoard($board);
        echo '<h2>Target Board</h2>';
        $this->drawBoard($goal);



        //initializing the start board
        $board->moveSoFar = 0;

        //openning the start board
        $this->addToOpenedList($board);


        //initializing level counter
        $levels = 0;

        while($board->tiles !== $this->goalNode->tiles){
//            if($levels > 200){
//                break;
//                dd($GLOBALS);
//            }

            //on each start of iteration, we raise a level.
            $levels++;
            echo '<h1>Starting Iteration No. '.$levels . ' ... </h1><br />';

            //on every start of new iteration, we close the board after it is
            //already being check as not the goal board.

            $this->addToClosedList($board);
            echo '<pre> OpenList Population :' . print_r(count($this->openedList),true) .'<br />';
            echo '<pre> ClosedList Population :' . print_r(count($this->closedList),true) .'<br />';

            //$board is safely closed.

            //moving blank position for every possible neighbor
            foreach($board->validMovement as $possibleMove){
                //this will produce an array of tiles
                $neighborTileSet = $this->moveBlankTile($board,$possibleMove);
                if($this->isBoardUnique($neighborTileSet)){
                    $newBoardNode= $this->createNewBoard($board,$neighborTileSet,$possibleMove);
                    $this->addToOpenedList($this->{$newBoardNode});
                    if($this->{$newBoardNode}->tiles === $this->goalNode->tiles){
                        break 2;
                    }
                }
            }

//            foreach ($neighbor as $movement => $tileSet) {
//                if($this->isBoardUnique($tileSet)){
//                    $newBoardNode = $this->createNewBoard(
//                        $board,
//                        $tileSet,
//                        $movement
//                    );
//                    $this->addToOpenedList($this->{$newBoardNode});
//                }
//            }

            //all the neighbor has been found, now moving to next search node
            //problem is how to find the next node?
            //are we going to blindly search the node according to level?
//            foreach($this->openedList as $index => $openedBoard){
//                $this->drawBoard($openedBoard);
//            }
            $nextBoard =  $this->determineNextNode($board);
            if(null !== $nextBoard) {
                $board = $this->openedList[$nextBoard];
                $this->evalBoard = $board;
            }else{break;}
            $this->drawBoard($board);

            if($board->tiles === $goal->tiles){
                break;
            }
//            dd($this->openedList, min($this->openedList->distanceFromGoal));

        }

        echo 'Solution Found : <pre>' . print_r($board,true);
        $this->drawBoard($board);

        $movementDone = array_reverse($this->traceBack($this->evalBoard));
//        dd($movementDone);

        echo 'Movement Done : <br />';
        echo '<div class="container">';
        echo '<table><tr>';
        echo '<td>Start Board</td><td>';
        echo $this->drawBoard($this->startNode);
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

    public function determineNextNode(board $currentBoard){
        $currentLevel = $currentBoard->moveSoFar;
//        $gotoNextLevel = true;
        $currentDistance = $currentBoard->distanceFromGoal;
        //check is there a node that has same level.
        // the rule for the next node is :
        // if there is a board with the same level and its distance is lower then the curent distance
        // stay in the same level and choose the lowest distance there is for the level
        foreach($this->openedList as $key=>$openBoard){
            if($openBoard->moveSoFar === $currentLevel && $openBoard->distanceFromGoal < $currentDistance){
                $nextNode = $key;
                return $nextNode;
            }

            if($openBoard->moveSoFar === $currentLevel + 1){
                $candidateForNextNode[$key] = $openBoard->distanceFromGoal;
            }
        }

        if(isset($candidateForNextNode)){
            echo '<pre>' . print_r($candidateForNextNode, true);
            $nextNode = array_keys($candidateForNextNode,min($candidateForNextNode));
        }else{
            $minOpen = array_keys($this->openedList, min($this->openedList));
            return $minOpen[0];
        }
        // otherwise, when there is same level board but higher distance, go to the next level and
        // select the lowest distance from that level

        //when there is no
        if(isset($nextNode)) {
            if (is_array($nextNode)) {
                return $nextNode[0];
            } else {
                return $nextNode;
            }
        }

    }

    public function addToOpenedList(board $boardToAdd){

        echo '<pre>Opening Board</pre><br />';
        //method to add a new board to the Opened List
        $this->openedList[] = $boardToAdd;

    }

    Public function addToClosedList(board $boardToAdd){
        echo '<pre>Closing Board</pre><br />';
        //method to add new board to the closed list
        //while also remove the same board from the opened List
        $this->closedList[] = $boardToAdd;
        $openedKey = array_search($boardToAdd, $this->openedList);
        unset($this->openedList[$openedKey]);
//        dd($openedKey, $this->openedList);

    }

    public function moveBlankTile(board $boardToMove, $movingDirection){
        $oldLocation = str_split($boardToMove->blankLoc);
        $oldRow = $oldLocation[0];
        $oldCol = $oldLocation[1];

        if($movingDirection === 'U'){
            echo '<pre>Moving Blank Tile Up from '. $boardToMove->blankLoc . '<br />';
            $targetRow = chr(ord($oldRow)-1);
            $targetCol = $oldCol;
//         $newTileSet = $this->moveUp($boardToMove);
        }elseif($movingDirection === 'D'){
            echo '<pre>Moving Blank Tile Down from '. $boardToMove->blankLoc . '<br />';
            $targetRow = chr(ord($oldRow)+1);
            $targetCol = $oldCol;
//         $newTileSet = $this->moveDown($boardToMove);
        }elseif($movingDirection === 'L'){
            echo '<pre>Moving Blank Tile Left from '. $boardToMove->blankLoc . '<br />';
            $targetRow = $oldRow;
            $targetCol = chr(ord($oldCol)-1);
//         $newTileSet = $this->moveLeft($boardToMove);
        }elseif($movingDirection === 'R'){
            echo '<pre>Moving Blank Tile Right from '. $boardToMove->blankLoc . '<br />';
            $targetRow = $oldRow;
            $targetCol = chr(ord($oldCol)+1);
//         $newTileSet = $this->moveRight($boardToMove);
        }


        //getting the old value from locations
        $oldValues = [
            $boardToMove->tiles[$oldRow][$oldCol], //the blank
            $boardToMove->tiles[$targetRow][$targetCol] // the not blank
        ];

        //geting tileSet
        $newTileSet = $boardToMove->tiles;

        //swapping values
        $newTileSet[$oldRow][$oldCol] = $oldValues[1];
        $newTileSet[$targetRow][$targetCol] = $oldValues[0];

        return $newTileSet;
    }

    public function isBoardUnique(array $tileSet){
        $isInTheOpenedList = false;
        $isInTheClosedList = false;
        $isUniqueBoard = false;

        if(null !== $this->openedList){ //not blank opened list
            foreach($this->openedList as $k=>$openedBoard){
                if($tileSet === $openedBoard->tiles){
                    //tileset has the same match in the opened board list
                    $isInTheOpenedList = true;
                    echo '<pre> New Board Found in Opened List. Not Adding<br/>';
                }
            }
        }

        if(null !== $this->closedList){ //not empty closed list
            foreach ($this->closedList as $k => $closedBoard) {
                if($tileSet === $closedBoard->tiles){
                    //tileset has the same match in the closed board list
                    $isInTheClosedList = true;
                    echo '<pre> New Board Found in Closed List. Not Adding<br/>';
                }
            }
        }

        if(!$isInTheClosedList && !$isInTheOpenedList){
            $isUniqueBoard = true;
                    echo '<pre> New Board is Unique, Adding<br/>';
        }

        return $isUniqueBoard;
    }

    public function drawBoard(board $board){
        echo '<pre><div style="font-size: larger; font-weight: bold;float: " >
            <p>Distance : '. $board->distanceFromGoal .'</p>
            <p>Level : ' . $board->moveSoFar . '</p>
            <table border="1px" cellpadding="0px" style="border: 1px solid;">';
        foreach($board->tiles as $row=>$col){
            echo '<tr>';
            foreach($col as $key=>$val){
                if($val === 'b'){
                    echo '<td bgcolor="black" width="30px" height="30px">&nbsp;</td>';
                }else {
                    $bl = $board->tiles;
                    $warna = $bl['B'][2].$bl['C'][1].$board->blankLoc.$bl['A'][3].'9';
                    echo '<td width="30px" height="30px" align="center" bgcolor="#' . $warna .
                        '" style="font-size: 14pt; font-weight: bold; color: white;">' . $val . '</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table></div></pre>';
    }

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

    public function getTileDistanceToGoal(board $board)
    {
        //every tile of the new board will have a value that shows the value distance
        //to the supposed position of that value to the goal

        //get the location of the value in the goal board
        $valueLocationInGoal = $this->getTileName($this->goalNode);
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
        $locationOfTheValueInGoal = $this->getTileName($this->goalNode);

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

    public function getTotalCost(board $board){
        //this is a simple method to calculate the total cost of each board passed
        $manhattanDistance = $this->getTileDistanceToGoal($board);
        $hammingDistance = $this->getHammingValue($board);
        if(!$manhattanDistance == 0 || !$hammingDistance == 0) {
//            $totalCost = round(((($manhattanDistance / $hammingDistance)) + $board->moveSoFar), 2) * 100;
//            $totalCost = (($manhattanDistance - $hammingDistance))+$board->moveSoFar;
            $totalCost = $manhattanDistance;
        }else{
            $totalCost = 0;
        }

        return $totalCost;
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
                $parentBoard = 'startNode';
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