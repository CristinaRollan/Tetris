import GameModel from './model.js';
import GameView from './view.js';

class GameController {
    constructor() {
        this.model = new GameModel();
        this.view = new GameView();

        document.addEventListener('keydown', this.handleKeyPress.bind(this));
        this.setupTouchControls();

        this.gameLoop = this.gameLoop.bind(this);
        this.isPaused = false;
        this.lastFrameTime = 0;

        requestAnimationFrame(this.gameLoop);
    }

    setupTouchControls() {
        document.getElementById('save-piece').addEventListener('touchstart', () => this.handleKeyPress({ key: 'c' }));
        document.getElementById('move-left').addEventListener('touchstart', () => this.handleKeyPress({ key: 'ArrowLeft' }));
        document.getElementById('move-right').addEventListener('touchstart', () => this.handleKeyPress({ key: 'ArrowRight' }));
        document.getElementById('rotate-piece').addEventListener('touchstart', () => this.handleKeyPress({ key: 'ArrowUp' }));
        document.getElementById('drop-piece').addEventListener('touchstart', () => this.handleKeyPress({ key: ' ' }));
    }

    handleKeyPress(e) {
        if (this.model.gameOver) return;
        switch (e.key) {
            case 'ArrowLeft':
                const leftPos = this.model.tetromino.col - 1;
                if (this.model.isValidMove(this.model.tetromino.matrix, this.model.tetromino.row, leftPos)) {
                    this.model.tetromino.col = leftPos;
                }
                break;
            case 'ArrowRight':
                const rightPos = this.model.tetromino.col + 1;
                if (this.model.isValidMove(this.model.tetromino.matrix, this.model.tetromino.row, rightPos)) {
                    this.model.tetromino.col = rightPos;
                }
                break;
            case 'ArrowDown':
                const downPos = this.model.tetromino.row + 1;
                if (this.model.isValidMove(this.model.tetromino.matrix, downPos, this.model.tetromino.col)) {
                    this.model.tetromino.row = downPos;
                }
                break;
            case 'ArrowUp':
                const rotatedMatrix = this.model.rotate(this.model.tetromino.matrix);
                if (this.model.isValidMove(rotatedMatrix, this.model.tetromino.row, this.model.tetromino.col)) {
                    this.model.tetromino.matrix = rotatedMatrix;
                }
                break;
            case ' ':
                while (this.model.isValidMove(this.model.tetromino.matrix, this.model.tetromino.row + 1, this.model.tetromino.col)) {
                    this.model.tetromino.row++;
                }
                this.model.placeTetromino();
                break;
            case 'c':
                this.handleSavePiece();
                break;
        }
    }

    handleSavePiece() {
        if (!this.model.isSaved) {
            const savedPiece = this.model.savedPiece;
            this.model.savedPiece = this.model.tetromino.name;
            if (savedPiece) {
                this.model.tetromino = {
                    name: savedPiece,
                    matrix: this.model.tetrominos[savedPiece],
                    row: -2,
                    col: this.model.playfield[0].length / 2 - Math.ceil(this.model.tetrominos[savedPiece][0].length / 2)
                };
            } else {
                this.model.tetromino = this.model.getNextTetromino();
            }
            this.model.isSaved = true;
        }
    }

    sendScore(score) {
        fetch('procesar_puntuacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ score: score })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = 'ranking_global.php';
            } else {
                console.error('Error al enviar la puntuación:', data);
            }
        })
        .catch(error => console.error('Error en la solicitud:', error));
    }

    gameLoop() {
        if (this.isPaused) return;
        if (this.model.gameOver) {
            this.sendScore(this.model.score);
            return;
        }

        const now = Date.now();

        // Llamar al método increaseSpeedWithTime
        this.model.increaseSpeedWithTime();

        if (now - this.model.lastDropTime > this.model.speed) {
            const nextRow = this.model.tetromino.row + 1;
            if (!this.model.isValidMove(this.model.tetromino.matrix, nextRow, this.model.tetromino.col)) {
                if (!this.model.placeTetromino()) {
                    this.model.gameOver = true;
                }
            } else {
                this.model.tetromino.row = nextRow;
            }
            this.model.lastDropTime = now;
        }

        const shadowRow = this.model.calculateShadowPosition();

        this.view.drawPlayfield(this.model.playfield, this.model.colors);
        this.view.drawShadow(this.model.tetromino, shadowRow, this.model.colors);
        this.view.drawTetromino(this.model.tetromino, this.model.colors);
        this.view.updateScore(this.model.score);
        this.view.drawNextPieces(this.model.tetrominoSequence, this.model.tetrominos, this.model.colors);
        if (this.model.savedPiece) {
            this.view.drawSavedPiece(this.model.savedPiece, this.model.tetrominos, this.model.colors);
        }

        requestAnimationFrame(this.gameLoop);
    }
}

export default GameController;

const gameController = new GameController();