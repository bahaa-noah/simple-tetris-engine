# Simple Tetris Engine
Simple Tetris Game Engine that take the Tetromino shapes as input from a file a drop them accordingly to the grid, then write the output to another file.



# Assumptions

- [PHP Installed](https://www.php.net/downloads.php)
- [Composer Installed](https://getcomposer.org/download/)


# Start the Game
 ```sh
  php index.php
```
 then provide the input/output file names/paths

# Run the tests
```sh
composer install
composer dump-autoload --optimize
vendor/bin/phpunit TetrisEngineTest.php --testdox
```
