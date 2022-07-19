<?php


class TetrisEngine
{


    public array $data;

    private $maxRows = 10;
    private $maxHeight = 100;

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


    public function addRow()
    {
        if (sizeOf($this->grid) > $this->maxHeight) {
            throw new Exception("You have reached the maximum height, Game Over!");
        }

        $row = [];
        for ($j = 0; $j < $this->maxRows; $j++) {
            $row[$j] = 0;
        }
        array_push($this->grid, $row);
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
        $result  = "";
        foreach ($data as $key => $value) {
            foreach ($value as $$key => $shape) {
                $shapeArray = str_split($shape);
                $shapeName  = $shapeArray[0];
                $shapeStartIndex  = $shapeArray[1];
                $this->fillTheGrid($this->shapes[$shapeName], $shapeStartIndex);
                foreach ($this->grid as $key => $row) {
                    if ($this->isCompletedRow($row)) {
                        $this->removeRowByIndex($key);
                    }
                }
            }

            $result .= $this->getGridHeight() . ",";
            $this->resetGrid();
        }

        print ($result) . PHP_EOL;
        $this->saveOutput($result);
    }


    public function saveOutput($result)
    {
        $output = fopen("output.txt", "w") or die("Unable to open file!");
        fwrite($output, $result . "\n");
        fclose($output);
    }


    public function resetGrid()
    {
        unset($this->grid);
        $this->grid = [];
    }


    public function fillTheGrid(array $shape, int $startIndex)
    {


        if (empty($this->grid)) {
            $this->addRow();
        }

        $formattedShape = [];
        for ($i = 0; $i < sizeof($shape); $i++) {
            $formattedShape[$i][0] =  $shape[$i][0];
            $formattedShape[$i][1] =  $shape[$i][1] + $startIndex;
        }

        foreach ($this->grid as $key => $row) {
            if ($row[$startIndex] == 1) {
                for ($i = 0; $i < sizeof($shape); $i++) {
                    $formattedShape[$i][0] =  $shape[$i][0] + $key;
                    $formattedShape[$i][1] =  $shape[$i][1] + $startIndex;
                }
            }
        }

        $gridRow = $formattedShape[0][0];
        $gridCol = $formattedShape[0][1];

        while (!isset($this->grid[$gridRow][$gridCol])) {
            $this->addRow();
        }

        while ($this->grid[$gridRow][$gridCol] == 1) {

            $gridRow++;
            if (!isset($this->grid[$gridRow][$gridCol])) {
                $this->addRow();
            }

            for ($j = 0; $j < sizeof($formattedShape); $j++) {
                $formattedShape[$j][0]++;
            }
        }


        for ($i = 0; $i < sizeof($formattedShape); $i++) {
            if (!isset($this->grid[$formattedShape[$i][0]][$formattedShape[$i][1]])) {
                $this->addRow();
            }
        }

        for ($k = 0; $k < count($formattedShape); $k++) {
            $this->grid[$formattedShape[$k][0]][$formattedShape[$k][1]] = 1;
        }
    }

    // public function fillTheGrid(array $shape, int $startIndex)
    // {


    //     if (empty($this->grid)) {
    //         $this->addRow();
    //     }

    //     $i = 0;
    //     $gridRow = 0;
    //     while ($i < sizeof($shape)) {

    //         $shapeRow = $shape[$i][0];
    //         $shapeCol = $shape[$i][1] + $startIndex;
    //         $gridRow = $shapeRow;
    //         $gridCol = $shapeCol;

    //         if ($gridCol >= $this->maxRows) {
    //             throw new Exception("Illegal shape start Index, Please enter valid start and try again!");
    //             die;
    //         }

    //         while (!isset($this->grid[$gridRow][$gridCol])) {
    //             $this->addRow();
    //         }

    //         $isAvailable = $this->grid[$gridRow][$gridCol] == 0;

    //         if ($isAvailable) {
    //             $this->grid[$gridRow][$gridCol] = 1;
    //         } else {

    //             if (!isset($this->grid[$gridRow][$gridCol])) {
    //                 $this->addRow();
    //             }

    //             while ($this->grid[$gridRow][$gridCol] == 1) {

    //                 $gridRow++;

    //                 if (!isset($this->grid[$gridRow][$gridCol])) {
    //                     $this->addRow();
    //                 }

    //                 if ($this->grid[$gridRow][$gridCol] == 0) {
    //                     $this->grid[$gridRow][$gridCol] = 1;
    //                     break;
    //                 }
    //             }
    //         }
    //         $i++;
    //     }

    //     if ($this->isCompletedRow($this->grid[0])) {
    //         $this->removeRowByIndex(0);
    //     }
    // }

    public function isCompletedRow($row)
    {
        return in_array(0, $row) ? false : true;
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
$tetrisEngine->formateFileInput();
$tetrisEngine->createGrid();
$result = $tetrisEngine->getGrid();
// print_r($result);
