class GameModel {
    constructor() {
        this.grid = 32;
        this.playfield = [];
        this.tetrominoSequence = [];
        this.savedPiece = null;
        this.isSaved = false;
        this.speed = 1000; //Velocidad de caída
        this.lastDropTime = Date.now();
        this.score = 0;
        this.linesClearedInMove = 0;
        this.tetrominoPlacedCount = 0;
        this.gameOver = false;
        this.speedIncreaseInterval = 10000; // Incrementar la velocidad cada 10 segundos
        this.lastSpeedIncreaseTime = Date.now(); // Última vez que se incrementó la velocidad


        for (let row = -2; row < 20; row++) {
            this.playfield[row] = [];
            for (let col = 0; col < 10; col++) {
                this.playfield[row][col] = 0;
            }
        }

        this.tetrominos = {
            'I': [[0,0,0,0],[1,1,1,1],[0,0,0,0],[0,0,0,0]],
            'J': [[1,0,0],[1,1,1],[0,0,0]],
            'L': [[0,0,1],[1,1,1],[0,0,0]],
            'O': [[1,1],[1,1]],
            'S': [[0,1,1],[1,1,0],[0,0,0]],
            'Z': [[1,1,0],[0,1,1],[0,0,0]],
            'T': [[0,1,0],[1,1,1],[0,0,0]]
        };

        this.colors = {
            'I': 'cyan', 'O': 'yellow', 'T': 'purple', 'S': 'green',
            'Z': 'red', 'J': 'blue', 'L': 'orange'
        };

        this.tetromino = this.getNextTetromino();
    }

    getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    generateSequence() {
        const sequence = ['I', 'J', 'L', 'O', 'S', 'T', 'Z'];
        while (this.tetrominoSequence.length < 500) {
            const rand = this.getRandomInt(0, sequence.length - 1);
            const name = sequence[rand];
            this.tetrominoSequence.push(name);
        }
    }

    getNextTetromino() {
        if (this.tetrominoSequence.length === 0) {
            this.generateSequence();
        }
        const name = this.tetrominoSequence.pop();
        const matrix = this.tetrominos[name];
        const col = this.playfield[0].length / 2 - Math.ceil(matrix[0].length / 2);
        const row = name === 'I' ? -1 : -2;

        return { name: name, matrix: matrix, row: row, col: col };
    }

    rotate(matrix) {
        const N = matrix.length - 1;
        return matrix.map((row, i) => row.map((val, j) => matrix[N - j][i]));
    }

    isValidMove(matrix, cellRow, cellCol) {
        for (let row = 0; row < matrix.length; row++) {
            for (let col = 0; col < matrix[row].length; col++) {
                if (matrix[row][col] && (
                    cellCol + col < 0 ||
                    cellCol + col >= this.playfield[0].length ||
                    cellRow + row >= this.playfield.length ||
                    this.playfield[cellRow + row][cellCol + col]
                )) {
                    return false;
                }
            }
        }
        return true;
    }

    placeTetromino() {
        for (let row = 0; row < this.tetromino.matrix.length; row++) {
            for (let col = 0; col < this.tetromino.matrix[row].length; col++) {
                if (this.tetromino.matrix[row][col]) {
                    if (this.tetromino.row + row < 0) {
                        return false;
                    }
                    this.playfield[this.tetromino.row + row][this.tetromino.col + col] = this.tetromino.name;
                }
            }
        }
        for (let row = this.playfield.length - 1; row >= 0;) {
            if (this.playfield[row].every(cell => !!cell)) {
                for (let r = row; r >= 0; r--) {
                    for (let c = 0; c < this.playfield[r].length; c++) {
                        this.playfield[r][c] = this.playfield[r-1][c];
                    }
                }
                this.linesClearedInMove++;
            } else {
                row--;
            }
        }

        if (this.linesClearedInMove > 0) {
            this.calculateScore();
            this.linesClearedInMove = 0;
        }

        this.tetromino = this.getNextTetromino();
        this.isSaved = false;
        this.tetrominoPlacedCount++;
        return true;
    }

    calculateScore() {
        const lineScores = [100, 200, 400, 800];
        this.score += lineScores[this.linesClearedInMove - 1] || 0;
    }

    increaseSpeedWithTime() {
        const currentTime = Date.now();
        if (currentTime - this.lastSpeedIncreaseTime > this.speedIncreaseInterval && this.speed > 50) {
            this.speed -= 25; // Incrementa la velocidad reduciendo el tiempo de espera
            this.lastSpeedIncreaseTime = currentTime;
        }
    }
    calculateShadowPosition() {
        let shadowRow = this.tetromino.row;
        while (this.isValidMove(this.tetromino.matrix, shadowRow + 1, this.tetromino.col)) {
            shadowRow++;
        }
        return shadowRow;
    }
}

export default GameModel;