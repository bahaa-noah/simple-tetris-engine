<?php


class TetrisEngine
{


    public array $data;

    private $maxRows = 10;
    private $maxHeight = 5;
    private $height = 0;

    private $shapes = [
        'Q' => [
            [0, 0], [0, 1], [1, 0], [1, 1],
        ],
        'Z' => [
            [1, 0], [1, 1], [0, 1], [0, 2],
        ],
        'S' => [
            [0, 0], [0, 1], [1, 1], [2, 2],
        ],
        'I' => [
            [0, 0], [0, 1], [0, 2], [0, 3]
        ],
        'T' => [
            [1, 0], [1, 1], [1, 2], [0, 1]
        ],
        'L' => [
            [2, 0], [1, 0], [0, 0], [0, 1]
        ],
        'J' => [
            [2, 1], [1, 1], [0, 1], [0, 0]
        ]
    ];

    private $grid = [];

    public function initializePlayGround()
    {
        for ($i = 0; $i < $this->maxHeight; $i++) {
            for ($j = 0; $j < $this->maxRows; $j++) {
                $this->grid[$i][$j] = 0;
            }
        }
    }

    public function formateFileInput()
    {
        $inputFile = fopen("input2.txt", "r");

        while (($line = fgetcsv($inputFile)) !== FALSE) {
            $this->data[] = $line;
        }

        fclose($inputFile);
    }



    public function createGrid()
    {
        $data =  $this->data;

        $this->gridHeight = 0;
        $this->gridWidth = 0;

        foreach ($data as $key => $value) {
            foreach ($value as $$key => $shape) {
                $shapeArray = str_split($shape);
                $shapeName  = $shapeArray[0];
                $shapeStartIndex  = $shapeArray[1];
                $this->fillTheGrid($this->shapes[$shapeName], $shapeStartIndex);
            }

            $result = $this->height;
            print ($result) . PHP_EOL;
            $this->resetGrid();
        }
    }


    public function resetGrid()
    {
        $this->height = 0;
        unset($this->grid);
        $this->initializePlayGround();
    }


    public function fillTheGrid(array $shape, int $startIndex)
    {

        $i = 0;
        $gridRow = 0;
        while ($i < sizeof($shape)) {

            $shapeRow = $shape[$i][0];
            $shapeCol = $shape[$i][1] + $startIndex;
            $gridRow = $shapeRow;
            $gridCol = $shapeCol;
            $isAvailable = $this->grid[$gridRow][$gridCol] == 0;

            if ($isAvailable) {
                $this->grid[$gridRow][$gridCol] = 1;
            } else {

                while ($this->grid[$gridRow][$gridCol] == 1) {
                    $gridRow++;
                    if ($this->grid[$gridRow][$gridCol] == 0) {
                        $this->grid[$gridRow][$gridCol] = 1;
                        break;
                    }
                }
            }
            $this->height = $gridRow + 1;
            $i++;

            if ($this->isCompletedRow($this->grid[0])) {
                $this->height--;
                $this->removeRowByIndex(0);
            }
        }
        print_r($this->grid);
    }

    public function isCompletedRow($row)
    {
        foreach ($row as $value) {
            if ($value !== 1) {
                return false;
            }
        }
        return true;
    }

    public function removeRowByIndex($index)
    {
        unset($this->grid[$index]);
        $this->grid = array_values($this->grid);
    }

    public function getGrid()
    {
        return $this->grid;
    }

    public function getGridHeight()
    {
        return sizeof($this->getGrid());
    }
}


$tetrisEngine =  new TetrisEngine;
$tetrisEngine->initializePlayGround();
$tetrisEngine->formateFileInput();
$tetrisEngine->createGrid();
$tetrisEngine->getGridHeight();
