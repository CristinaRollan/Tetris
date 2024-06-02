class GameView {
    constructor() {
        this.canvas = document.getElementById('game');
        this.context = this.canvas.getContext('2d');
        this.grid = 32;
        this.nextPieceContainer = document.getElementById('nextPieceContainer');
        this.scoreElement = document.getElementById('score');
        this.savedPieceElement = document.getElementById('nextPiece');
    }

    drawPlayfield(playfield, colors) {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
        for (let row = 0; row < 20; row++) {
            for (let col = 0; col < 10; col++) {
                if (playfield[row][col]) {
                    const name = playfield[row][col];
                    this.context.fillStyle = colors[name];
                    this.context.fillRect(col * this.grid, row * this.grid, this.grid-1, this.grid-1);
                }
            }
        }
    }

    drawTetromino(tetromino, colors) {
        this.context.fillStyle = colors[tetromino.name];
        for (let row = 0; row < tetromino.matrix.length; row++) {
            for (let col = 0; col < tetromino.matrix[row].length; col++) {
                if (tetromino.matrix[row][col]) {
                    this.context.fillRect((tetromino.col + col) * this.grid, (tetromino.row + row) * this.grid, this.grid-1, this.grid-1);
                }
            }
        }
    }

    drawNextPieces(sequence, tetrominos, colors) {
        this.nextPieceContainer.innerHTML = '';
        for (let i = 0; i < 5; i++) {
            const nextPieceName = sequence[sequence.length - 1 - i];
            const nextPieceCanvas = document.createElement('canvas');
            nextPieceCanvas.width = 100;
            nextPieceCanvas.height = 100;
            nextPieceCanvas.className = "pie"
            const nextPieceContext = nextPieceCanvas.getContext('2d');
            nextPieceContext.fillStyle = colors[nextPieceName];
            for (let row = 0; row < tetrominos[nextPieceName].length; row++) {
                for (let col = 0; col < tetrominos[nextPieceName][row].length; col++) {
                    if (tetrominos[nextPieceName][row][col]) {
                        nextPieceContext.fillRect(col * this.grid, row * this.grid, this.grid, this.grid);
                    }
                }
            }
            this.nextPieceContainer.appendChild(nextPieceCanvas);
        }
    }

    drawSavedPiece(piece, tetrominos, colors) {
        this.savedPieceElement.innerHTML = '';
        const savedPieceCanvas = document.createElement('canvas');
        savedPieceCanvas.width = 100;
        savedPieceCanvas.height = 100;
        savedPieceCanvas.className = "pie";
        const savedPieceContext = savedPieceCanvas.getContext('2d');
        savedPieceContext.fillStyle = colors[piece];
        for (let row = 0; row < tetrominos[piece].length; row++) {
            for (let col = 0; col < tetrominos[piece][row].length; col++) {
                if (tetrominos[piece][row][col]) {
                    savedPieceContext.fillRect(col * this.grid, row * this.grid, this.grid, this.grid);
                }
            }
        }
        this.savedPieceElement.appendChild(savedPieceCanvas);
    }

    updateScore(score) {
        this.scoreElement.textContent = 'Score: ' + score;
    }
    
    drawShadow(tetromino, shadowRow, colors) {
        this.context.fillStyle = colors[tetromino.name];
        this.context.globalAlpha = 0.3; // Establece la transparencia de la sombra
        for (let row = 0; row < tetromino.matrix.length; row++) {
            for (let col = 0; col < tetromino.matrix[row].length; col++) {
                if (tetromino.matrix[row][col]) {
                    this.context.fillRect((tetromino.col + col) * this.grid, (shadowRow + row) * this.grid, this.grid - 1, this.grid - 1);
                }
            }
        }
        this.context.globalAlpha = 1; // Restablece la transparencia al valor normal
    }
}

export default GameView;