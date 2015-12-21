<?php
/**
 * Created by PhpStorm.
 * User: gabriel
 * Date: 12/15/2015
 * Time: 14:39 PM
 */

namespace App\Puzzle;


class board
{
    public $moveSoFar; //number of move that make this state
    public $parentBoard; //the name of the parent board
    public $validMovement;
    public $tiles;
    public $blankLoc;
    public $lastMovement;//the last movement from parent to get this board
    public $name;
    public $distanceFromGoal;
    public $parentDistance;
    private $A1;
    private $A2;
    private $A3;
    private $B1;
    private $B2;
    private $B3;
    private $C1;
    private $C2;
    private $C3;


    /**
     * @param $A1
     * @param $A2
     * @param $A3
     * @param $B1
     * @param $B2
     * @param $B3
     * @param $C1
     * @param $C2
     * @param $C3
     * @param $movementNumber
     * @param $parent
     * @param $last
     * @param $name
     */
    public function __construct(
        $A1,
        $A2,
        $A3,
        $B1,
        $B2,
        $B3,
        $C1,
        $C2,
        $C3,
        $movementNumber,
        $parent,
        $last,
        $name){
        //note: this has no way to check if there is a tile thats blank. so do this in the other logic
        //note2: this also has no way to check for duplicate value when the object instantiated. so do
        //          the logic in other way like in the controller
        $this->tiles = [
            'A' => [
                '1' => $A1,
                '2' => $A2,
                '3' => $A3,
            ],
            'B' => [
                '1' => $B1,
                '2' => $B2,
                '3' => $B3,
            ],
            'C' => [
                '1' => $C1,
                '2' => $C2,
                '3' => $C3,
            ]
        ];
        $this->lastMovement = $last;
        $this->moveSoFar = $movementNumber;
        $this->parentBoard = $parent;
        $this->blankLoc = $this->getBlankTile();
        $this->getValidMovement();
        $this->name = $name;
        $this->A1 = $A1;
        $this->A2 = $A2;
        $this->A3 = $A3;
        $this->B1 = $B1;
        $this->B2 = $B2;
        $this->B3 = $B3;
        $this->C1 = $C1;
        $this->C2 = $C2;
        $this->C3 = $C3;
    }

    public function getBlankTile(){
        //get the location of the blank tile
        foreach($this->tiles as $row=>$col){
            foreach ($col as $kol => $val) {
                if($val === 'b'){
                    $location = $row.$kol;
                }
            }

        }

        return $location; //string
    }

    public function getValueLocation($value){
        //this will return the location of $value on the board
        foreach($this->tiles as $row=>$col){
            foreach($col as $kol => $val) {
                if($val === $value){
                    $location = $row.$kol;
                }
            }
        }
        return $location;
    }
    public function moveBlank($movement){
        //move the blank and create new board object

        //check if the new generated object is already in the opened or closed list
        //if true, don't create new board
        //else create new board object

        //return new board object
    }

    public function getValidMovement(){
        //find the possible movement of the blank tile
        //input getBlankTile()
        //$blankTile = $this->getBlankTile();
        $blankTile = $this->blankLoc;
//        echo $blankTile;
        switch($blankTile){
            case "A1":
                $this->validMovement = ['D','R'];
                break;
            case "A2":
                $this->validMovement = ['D','R','L'];
                break;
            case "A3":
                $this->validMovement = ['D','L'];
                break;
            case "B1":
                $this->validMovement = ['D','R','U'];
                break;
            case "B2":
                $this->validMovement = ['D','R','L','U'];
                break;
            case "B3":
                $this->validMovement = ['D','U','L'];
                break;
            case "C1":
                $this->validMovement = ['U','R'];
                break;
            case "C2":
                $this->validMovement = ['U','R','L'];
                break;
            case "C3":
                $this->validMovement = ['U','L'];
                break;

        }

        //return array
    }

    public function moveUp(){
        //move the blank tile up
        $oldLoc = str_split($this->blankLoc); //location of the blank befor moving
        $row = $oldLoc[0];
        $col = $oldLoc[1];
        $targetRow = $row;
        --$targetRow; //moving the row one but the column stay

        $newLoc = $targetRow.$col;

        //getting the value affected tile
        $oldValue = [$this->tiles[$row][$col], $this->tiles[$targetRow][$col]]; //$oldvalue[0]= 'b'; $oldValue[1] = 'x'

        //moving the data
        $result = $this->tiles;


        //moving up is just switching the value of the old blank location with the value of the new location
        $result[$row][$col] = $oldValue[1]; //move 'x' to where the blank were
        $result[$targetRow][$col] = $oldValue[0]; // move the blank where the 'x' were


        //check the $result against the Opened Array and the Closed Array
        //this should be done in the Solver Class

        /*
         *
         * Initialize the var
         * $isInTheOpened = false;
         * foreach(Global $opened as $children){
         *      if($result == $children->tiles){
         *      //meaning that the new result is in the closed list
         *      $isInTheOpened = true;
         *      }
         *      //since we already set the isInTheOpened to false in the begining,
         *      //if its not finding any match in each children, it will stay false
         *      //but if it find a match, it set the var to true,
         *      // the next iteration won't matter.
         * }
         *
         * if the result is not in the open state, it might be new one, but it might be present in the closed list
         * if its not in the open and its not in the closed then its a new state.
         * A = isitintheopen, B = isitintheclosed
         *
         * A    B   isitnewState?
         * 1    1   False
         * 1    0   False
         * 0    1   False
         * 0    0   True
         *
         *  Initialize the var
         * $isInTheClosed = false; //asuming the new result is not already in the closed list.
         * foreach(Global $closed as $children){
         *      if($children->tiles == $result{
         *          //meaning that the new result is in the currently checked closed list
         *          //so even if the other closed list is saying false it will be set true
         *          $isInTheClosed = true;
         *      }
         * }
         *
         * //comparing the isInTheOpened with isInTheClosed with AND
         * //the only way the new result considered a new State and added as the children of this object is
         * //if and only if it !isInTheOpened and it !isInTheClosed.
         *
         * if(!isInTheOpened && !isInTheClosed){
         *  //meaning its a new state
         *
         *  Global $opened[] =
         *
         */


        //return void

    }


}