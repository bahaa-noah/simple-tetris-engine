<?php

declare(strict_types=1);
require_once 'TetrisEngine.php';


use PHPUnit\Framework\TestCase;

final class TetrisEngineTest extends TestCase
{


    /**
     * test case for Example 1 A line in the input file contains “I0,I4,Q8”
     *
     * @return void
     */
    public function testItDropsTheShapesCorrectlyForLineContains_I0_I4_Q8()
    {
        $formattedLine = [['I0', 'I4', 'Q8']];
        $tetrisEngine = new TetrisEngine;
        $tetrisEngine->setFileContent($formattedLine);
        $tetrisEngine->createGrid();


        $finalHeight  = $tetrisEngine->getGridHeight();
        $this->assertSame($finalHeight, 1);

        $finalGrid = $tetrisEngine->getGrid();
        $expectedGrid = [0 => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1]];
        $this->assertSame($finalGrid, $expectedGrid);
    }


    /**
     * test case for example 2 A line in the input file contains “T1,Z3,I4”
     *
     * @return void
     */
    public function testItDropsTheShapesCorrectlyForLineContains_T1_Z3_I4()
    {
        $formattedLine = [['T1', 'Z3', 'I4']];
        $tetrisEngine = new TetrisEngine;
        $tetrisEngine->setFileContent($formattedLine);
        $tetrisEngine->createGrid();


        $finalHeight  = $tetrisEngine->getGridHeight();
        $this->assertSame($finalHeight, 4);


        $expectedGrid = [
            3 => [0, 0, 0, 0, 1, 1, 1, 1, 0, 0],
            2 => [0, 0, 0, 1, 1, 0, 0, 0, 0, 0],
            1 => [0, 1, 1, 1, 1, 1, 0, 0, 0, 0],
            0 => [0, 0, 1, 0, 0, 0, 0, 0, 0, 0]
        ];
        $finalGrid = $tetrisEngine->getGrid();
        foreach ($finalGrid as $key => $row) {
            $this->assertSame($row, $expectedGrid[$key]);
        }
    }

    /**
     * test case for example 3 A line in the input file contains “Q0,I2,I6,I0,I6,I6,Q2,Q4”
     *
     * @return void
     */
    public function testItDropsTheShapesCorrectlyForLineContains_Q0_I2_I6_I0_I6_I6_Q2_Q4()
    {
        $formattedLine = [['Q0', 'I2', 'I6', 'I0', 'I6', 'I6', 'Q2', 'Q4']];
        $tetrisEngine = new TetrisEngine;
        $tetrisEngine->setFileContent($formattedLine);
        $tetrisEngine->createGrid();


        $finalHeight  = $tetrisEngine->getGridHeight();
        $this->assertSame($finalHeight, 3);

        $expectedGrid = [
            2 => [0, 0, 1, 1, 0, 0, 0, 0, 0, 0],
            1 => [0, 0, 1, 1, 0, 0, 0, 0, 0, 0],
            0 => [1, 1, 0, 0, 1, 1, 1, 1, 1, 1]
        ];
        $finalGrid = $tetrisEngine->getGrid();

        foreach ($finalGrid as $key => $row) {
            $this->assertSame($row, $expectedGrid[$key]);
        }
    }
}
