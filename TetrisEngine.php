<?php

/**
 * Simple Tetris Engine
 */
class TetrisEngine
{

    /**
     * @var array
     */
    private array $fileContent;

    /**
     * the playground grid
     *
     * @var array
     */
    private array $grid = [];

    /**
     * maximum allow columns in the playground grid
     */
    const  MAX_COLS = 10;

    /**
     * maximum allowed rows in the playground grid
     */
    const  MAX_HEIGHT = 100;

    /**
     * output string
     *
     * @var string
     */
    private string $output = '';


    /**
     * The Tetromino shapes represented by 2 dimensions array
     * 
     * eg : Q
     * [
     *  [Q,Q],
     *  [Q,Q]
     * ]
     * 
     * eg : L
     * [ 
     *  [L,0],
     *  [L,0]
     *  [L,L] 
     * ]
     * 
     * eg : T 
     *  [
     *   [T,T,T],
     *   [0,T,0]
     * ]
     * ..etc
     * 
     * each cell of the array that filled with the shape name is represented by it's index in the shapes array
     */
    const SHAPES = [
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



    /**
     * add new row to the playground grid
     *
     * @return void
     */
    private function addRow(): void
    {
        if ($this->getGridHeight() > self::MAX_HEIGHT) {
            throw new Exception("You have reached the maximum height, Game Over!");
        }

        $row = [];
        for ($j = 0; $j < self::MAX_COLS; $j++) {
            $row[$j] = 0;
        }
        array_push($this->grid, $row);
    }

    /**
     * Create the playground grid
     *
     * @return void
     */
    public function createGrid(): void
    {

        $result  = "";
        // loop through file lines -> Q0,Q1
        foreach ($this->getFileContent() as  $key => $line) {

            // loop through line shapes -> Q0
            foreach ($line as $shape) {

                $shapeArray = str_split($shape);
                $shapeName  = $shapeArray[0];
                $shapeStartIndex  = $shapeArray[1];

                $this->dropShapeIntoTheGrid(self::SHAPES[$shapeName], $shapeStartIndex);
                $this->shouldRemoveRow();
            }

            $result .= sprintf("%s => %s" . PHP_EOL, implode(',', $line), $this->getGridHeight());

            if ($key ==   array_key_last($this->getFileContent())) {
                break;
            }
            $this->resetGrid();
        }

        print($result);
        $this->saveOutputToFile($result);
    }


    /**
     * Drop Tetromino shape into the right place in the playground grid
     *
     * @param array $shape
     * @param integer $startIndex
     * @return void
     */
    private function dropShapeIntoTheGrid(array $shape, int $startIndex): void
    {


        if (empty($this->grid)) {
            $this->addRow();
        }
        //adjust the shape indexes based on the start column 
        $formattedShape = [];
        for ($i = 0; $i < sizeof($shape); $i++) {
            $formattedShape[$i][0] =  $shape[$i][0];
            $formattedShape[$i][1] =  $shape[$i][1] + $startIndex;
        }

        // check for collision and adjust the shape values in case there is any
        foreach ($this->getGrid() as $key => $row) {
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

        if ($gridCol >= self::MAX_COLS) {
            throw new Exception("Illegal shape start Index, Please enter valid start and try again!");
            die;
        }

        $this->shouldAddNewRows($formattedShape);
        $this->insertShape($formattedShape);
    }


    /**
     * Check if the playground grid needs new rows before inserting new shape
     *
     * @param array $shape
     * @return void
     */
    public function shouldAddNewRows(array $shape): void
    {
        for ($i = 0; $i < sizeof($shape); $i++) {
            if (!isset($this->grid[$shape[$i][0]][$shape[$i][1]])) {
                $this->addRow();
            }
        }
    }

    /**
     * Insert new shape into the playground grid
     *
     * @param array $shape
     * @return void
     */
    public function insertShape(array $shape): void
    {
        for ($k = 0; $k < sizeof($shape); $k++) {
            $this->grid[$shape[$k][0]][$shape[$k][1]] = 1;
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function shouldRemoveRow(): void
    {
        foreach ($this->getGrid() as $key => $row) {
            if ($this->isCompletedRow($row)) {
                $this->removeRowByIndex($key);
            }
        }
    }

    /**
     * Reinitialize the playground grid
     *
     * @return void
     */
    private function resetGrid(): void
    {
        unset($this->grid);
        $this->grid = [];
    }

    /**
     * Check if a row is completed
     * Completed row is a row that doesn't contain any zeros and all of it's values are ones
     *
     * @param array $row
     * @return boolean
     */
    private function isCompletedRow(array $row): bool
    {
        return in_array(0, $row) ? false : true;
    }


    /**
     * Remove row from the playground grid by the row index
     *
     * @param integer $index
     * @return void
     */
    private function removeRowByIndex(int $index): void
    {
        unset($this->grid[$index]);
        $this->grid = array_values($this->getGrid());
    }


    /**
     * return the playground grid
     *
     * @return array
     */
    public function getGrid(): array
    {
        return $this->grid;
    }

    /**
     * Return current grid height
     *
     * @return integer
     */
    public function getGridHeight(): int
    {
        return sizeof($this->getGrid());
    }

    /**
     * Get input/output file names/paths from STDIN
     *
     * @return void
     */
    private function getInputAndOutputFiles(): void
    {
        $input = (string)readline("please enter the input file name/path: ");
        $this->output = (string)readline("please enter the output file name/path: ");
        $inputFile = fopen($input, "r") or die("Unable to open file!");

        while (($line = fgetcsv($inputFile)) !== FALSE) {
            $this->fileContent[] = $line;
        }

        fclose($inputFile);
    }

    /**
     * Save the result to output file
     *
     * @param string $result
     * @return void
     */
    private function saveOutputToFile(string $result): void
    {
        if (empty($this->output)) {
            return;
        }
        $output = fopen($this->output, "w") or die("Unable to open file!");
        fwrite($output, $result . "\n");
        fclose($output);
    }

    /**
     *
     * @param array $fileContentArray
     * @return self
     */
    public function setFileContent(array $fileContentArray): self
    {
        $this->fileContent = $fileContentArray;
        return $this;
    }

    /**
     * @return array
     */
    public function getFileContent(): array
    {
        return $this->fileContent;
    }

    /**
     * Run the game
     *
     * @return void
     */
    public function run(): void
    {
        $this->getInputAndOutputFiles();
        $this->createGrid();
    }
}
