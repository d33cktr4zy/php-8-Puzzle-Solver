<?php
/**
 * Created by PhpStorm.
 * User: gabriel
 * Date: 12/16/2015
 * Time: 12:42 PM
 */

namespace App\Puzzle;

use \App\Puzzle\board;


class Solver
{

    /*
     * This is the class that is called when we want to solve the puzzle
     *
     */

    public $startBoard; // the instance of the board object that is to be the start state
    public $goalBoard; //THe instance of the board that is to be the goal

    public $opened; // Array of the board instance that has already been generated but not yet processed to get all the possible moves
    public $closed;
    public $evalBoard;

    /**
     * Solver constructor.
     * @param $startBoard
     * @param $goalBoard
     */
    public function __construct(board $startBoard, board $goalBoard)
    {
        $this->startBoard = $startBoard;
        $this->goalBoard = $goalBoard;
    } // Array of the board instance that has already been generated and has been processed to get all the possible moves




    //Algorithm
    /**
     *
     */
    public function solvePuzzle(){
        //initialization
        $board = $this->startBoard; //for first itteration
        $goal = $this->goalBoard;
        echo 'Starting'.'<br />';
    //Open the Start Board
        $this->opened[] = $board;
//        echo 'Start Board = <pre>' . print_r($board,true);
        $levels = 0;
//        echo 'goal board  = <pre>' . print_r($goal, true);


        while($board->tiles !== $goal->tiles){
//            echo 'Current Opened Content : <br />';
//            echo '<div style="width: 100%;">';
//            foreach($this->opened as $opBoard) {
//                $this->drawBoard($opBoard);
//            }
//            echo '</div>';
            //house cleaning
            if(isset($newBoardTiles)){unset($newBoardTiles);}
            //increasing tree level
            $levels++;
            //closing the board
            $this->closed[] = $board;
            unset($this->opened[array_search($board,$this->opened)]);

            echo 'not Goal <br /> <h1><strong>so start iteration no. ' . $levels . '</strong></h1><br />';
//            echo '<h2>Current Board : ' . print_r($board,true) .'</h2><br />';
            $this->drawBoard($board);
            echo '<br />';
            //starting iteration
                if(isset($children)){unset($children);} //clearing children after each itteration
            //2. Retrieve a list of children and put it on the Opened List
            foreach($board->validMovement as $movement){
               $children[$movement] = $this->moveBlankTile($board, $movement);
//                echo 'children '.$movement .' : ' . print_r($children,true);
                //at this point $
            }

            //checking if the new children is the same as the master
            foreach($children as $key=>$newTiles){
                if($newTiles === $board->tiles){
//                    echo 'Children is the same as Master...'. '<br />';
//                    echo 'unsetting '.'<br />Children[' . $key . ']<br />'
//                        . 'From $Children';
                    unset($children[$key]);
                }else{
//                    echo '<strong>Children['. $key . '] is not the same as Master!</strong><br />';
                }
            }

            //checking if the new Children is in the open list
            foreach($children as $key=>$newTiles){
                $isInTheOpen = false;
                if(null !== $this->opened) {
//                    echo 'Opened Content : ' . print_r($this->opened,true) . '<br />';
                    foreach ($this->opened as $keys => $openBoard) {
//                        echo 'Checking '. $keys . ' against the Opened'. '<br />';
                        if ($newTiles === $openBoard->tiles) {
//                            echo '<h3><strong>Children is found in the Opened List</strong></h3>';
                            $isInTheOpen = true;
                        }
                    }
                    if($isInTheOpen === false) {
//                        echo '<h3><strong>Children is not in the Opened List. Continue to check for closed List</strong></h3>';
                    }
                }


                $isInTheClosed = false;
                if(null !== $this->closed) {
//                    echo 'Closed Content ; ' . print_r($this->closed,true). '<br />';
                    foreach ($this->closed as $keys => $closedBoard) {
                        if ($newTiles === $closedBoard) {
//                            echo '<h3><strong>Children is found in the Closed List</strong></h3>';
                            $isInTheClosed = true;
                        }
                    }
                    if($isInTheClosed === false){
//                        echo '<h3><strong>Children Is not in the Closed list.</strong></h3>';
                    }
                }

                if (!$isInTheOpen && !$isInTheClosed) {
                    //then its a new state and create a new board object
                    //preparation
//                    echo '<strong>Adding Children to the new tiles</strong><br/>';
//                    echo 'the New Tiles = '. print_r($newTiles,true) .'<br />';
                    /** @var array $newBoardTiles */
                    $newBoardTiles[$key] = $newTiles;
                }else{
//                    echo '<strong>Not Adding Children to the new tiles</strong><br />';
                }
                if(isset($newBoardTiles)) {
//                    echo '<strong>The New possible tiles is : </strong><br/>' . print_r($newBoardTiles, true) . '<br/>';
                }
//            echo 'the new Possible tiles = ' . print_r($newBoardTiles,true) . '<br />;
            }

//            return $children;
            if(isset($newBoardTiles)) {
                foreach ($newBoardTiles as $key => $tiles) {
                    //create a new Board Instance
                    //preparation
                    $lev = $board->moveSoFar;
                    /** @var string $dir */
                    $dir = $key;
                    if (null === $board->parentBoard) {
                        //its a start node
                        $parent = 'start';
                    } else {
                        //$parent = 'start-1U'
                        $parent = $board->parentBoard . '-' . $lev . $board->lastMovement;
                    }
                    $newLevel = $lev + 1;
                    //generate name for variable
                    //$name = 'start-1U-2D'
                    /** @var string $name */
                    $name = $parent . '-' . $newLevel . $dir;

                    $this->{$name} = new board(
                        $tiles['A'][1],
                        $tiles['A'][2],
                        $tiles['A'][3],
                        $tiles['B'][1],
                        $tiles['B'][2],
                        $tiles['B'][3],
                        $tiles['C'][1],
                        $tiles['C'][2],
                        $tiles['C'][3],
                        ($lev + 1),
                        $parent,
                        $key, $name
                    );

                    $this->opened[] = $this->{$name};


                    //and add it to the newList
                }
            }

            //open the lowest key
            $board = $this->opened[min(array_keys($this->opened))];

            $this->evalBoard = $board;
//            echo 'The New Board : <pre>'. print_r($board,true) . '<br>';
            //3. Move the $board to the closed list

            //4. check the $children against $opened and Closed

            if($levels === 100){

//            break;
            }
        }
        echo '<pre>Solution : <h3><red>' .print_r($this->evalBoard,true) . '</red></h3>';
        echo '<div class="container">';
        $this->drawBoard($this->evalBoard);
        echo '</div>';
        //generate the candidate for children

        $movementDone = array_reverse($this->traceBack($this->evalBoard));

        echo '<pre>Movement Done : <br />';
        echo'<div class="container">';
        $this->drawBoard($this->startBoard);

        foreach ($movementDone as $steps) {
            echo 'Steps : ' . $steps->lastMovement;
            $this->drawBoard($steps);
            echo ' => ';

        }
        echo '</div>';


        //check the generated children for possible new state

        //
    }

    /**
     * @param \App\Puzzle\board $board
     * @param $movement
     * @return array
     */
    public function moveBlankTile(board $board, $movement){
        echo 'Moving ' . $movement . ' -ward' . '<br />';
        if($movement === 'D'){
            $tiles = $this->moveDown($board);
        }
        if($movement === 'U'){
            $tiles = $this->moveUp($board);
        }
        if($movement === 'L'){
            $tiles = $this->moveLeft($board);
        }
        if($movement === 'R'){
            $tiles = $this->moveRight($board);
        }

        //return a new tiles position
//        echo 'New Tiles : '. print_r($tiles, true) . '<br />';
        return $tiles;

    }

    /**
     * @param \App\Puzzle\board $board
     * @return array
     */
    public function moveDown(board $board){
        $oldLoc = str_split($board->blankLoc);
        $row = $oldLoc[0];
        $col = $oldLoc[1];
        $targetRow = chr(ord($row)+1);

//        ++$targetRow; // move the row down

//        $newLoc = $targetRow.$col; // new location is row +1 while col is the same

        //getting the value of old position
        $oldValue = [$board->tiles[$row][$col], $board->tiles[$targetRow][$col]];
        //$oldValue[0] will be the blank tiles
        //$oldValue[1] will be the 'x'

        $newTiles = $board->tiles;

        //swapping the value of each tiles affected
        //placing the 'x' value to blank tile
        $newTiles[$row][$col] = $oldValue[1];
        //placing the blank tile to where the 'x' were
        $newTiles[$targetRow][$col] = $oldValue[0];

        //returning the new tiles position
        return $newTiles;

    }

    /**
     * @param \App\Puzzle\board $board
     * @return array
     */
    public function moveUp(board $board){
        $oldLoc = str_split($board->blankLoc);
        $row = $oldLoc[0];
        $col = $oldLoc[1];
//        $targetRow = $row;
//        echo $targetRow. ' - ' . $row . '<br />';
        $targetRow = chr(ord($row)-1);
//        --$targetRow; // move the row down
//        echo $targetRow .'<br />';

//        $newLoc = $targetRow.$col; // new location is row -1 while col is the same

        //getting the value of old position
        $oldValue = [$board->tiles[$row][$col], $board->tiles[$targetRow][$col]];
        //$oldValue[0] will be the blank tiles
        //$oldValue[1] will be the 'x'

        $newTiles = $board->tiles;

        //swapping the value of each tiles affected
        //placing the 'x' value to blank tile
        $newTiles[$row][$col] = $oldValue[1];
        //placing the blank tile to where the 'x' were
        $newTiles[$targetRow][$col] = $oldValue[0];

        //returning the new tiles position
        return $newTiles;


    }

    /**
     * @param \App\Puzzle\board $board
     * @return array
     */
    public function moveLeft(board $board){
        $oldLoc = str_split($board->blankLoc);
        $row = $oldLoc[0];
        $col = $oldLoc[1];
        $targetCol = $col;
        --$targetCol; // move the row down

//        $newLoc = $targetRow.$col; // new location is row +1 while col is the same

        //getting the value of old position
        $oldValue = [$board->tiles[$row][$col], $board->tiles[$row][$targetCol]];
        //$oldValue[0] will be the blank tiles
        //$oldValue[1] will be the 'x'

        $newTiles = $board->tiles;

        //swapping the value of each tiles affected
        //placing the 'x' value to blank tile
        $newTiles[$row][$col] = $oldValue[1];
        //placing the blank tile to where the 'x' were
        $newTiles[$row][$targetCol] = $oldValue[0];

        //returning the new tiles position
        return $newTiles;


    }

    /**
     * @param \App\Puzzle\board $board
     * @return array
     */
    public function moveRight(board $board){
        $oldLoc = str_split($board->blankLoc);
        $row = $oldLoc[0];
        $col = $oldLoc[1];
        $targetCol = $col;
        ++$targetCol; // move the row down

//        $newLoc = $targetRow.$col; // new location is row +1 while col is the same

        //getting the value of old position
        $oldValue = [$board->tiles[$row][$col], $board->tiles[$row][$targetCol]];
        //$oldValue[0] will be the blank tiles
        //$oldValue[1] will be the 'x'

        $newTiles = $board->tiles;

        //swapping the value of each tiles affected
        //placing the 'x' value to blank tile
        $newTiles[$row][$col] = $oldValue[1];
        //placing the blank tile to where the 'x' were
        $newTiles[$row][$targetCol] = $oldValue[0];

        //returning the new tiles position
        return $newTiles;


    }

    public function drawBoard(board $board){
        echo '<div style="font-size: larger; font-weight: bold;float: left;" >
            <p> Name :'. $board->name .'</p>
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