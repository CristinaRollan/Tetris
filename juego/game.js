function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function generateSequence() {
    const sequence = ['I', 'J', 'L', 'O', 'S', 'T', 'Z'];

    while (tetrominoSequence.length < 300) { // Genera más piezas de las necesarias
        const rand = getRandomInt(0, sequence.length - 1);
        const name = sequence[rand];
        tetrominoSequence.push(name);
    }
}

function getNextTetromino() {
    if (tetrominoSequence.length === 0) {
        generateSequence();
    }

    const name = tetrominoSequence.pop();
    const matrix = tetrominos[name];

    const col = playfield[0].length / 2 - Math.ceil(matrix[0].length / 2);
    const row = name === 'I' ? -1 : -2;

    return {
        name: name,
        matrix: matrix,
        row: row,
        col: col
    };
}

//5 PIEZAS POR SALIR
function showNextPieces() {
    const nextPieceContainer = document.getElementById('nextPieceContainer');
    nextPieceContainer.innerHTML = '';

    for (let i = 0; i < 5; i++) {
        const nextPieceName = tetrominoSequence[tetrominoSequence.length - 1 - i];
        const nextPieceCanvas = document.createElement('canvas');
        nextPieceCanvas.width = 100;
        nextPieceCanvas.height = 100;
        nextPieceCanvas.className = "pie"
        const nextPieceContext = nextPieceCanvas.getContext('2d');

        nextPieceContext.fillStyle = colors[nextPieceName];
        for (let row = 0; row < tetrominos[nextPieceName].length; row++) {
            for (let col = 0; col < tetrominos[nextPieceName][row].length; col++) {
                if (tetrominos[nextPieceName][row][col]) {
                    nextPieceContext.fillRect(col * grid, row * grid, grid, grid);
                }
            }
        }

        nextPieceContainer.appendChild(nextPieceCanvas);
    }
}

function rotate(matrix) {
    const N = matrix.length - 1;
    const result = matrix.map((row, i) =>
        row.map((val, j) => matrix[N - j][i])
    );

    return result;
}

function isValidMove(matrix, cellRow, cellCol) {
    for (let row = 0; row < matrix.length; row++) {
        for (let col = 0; col < matrix[row].length; col++) {
            if (matrix[row][col] && (
                cellCol + col < 0 ||
                cellCol + col >= playfield[0].length ||
                cellRow + row >= playfield.length ||
                playfield[cellRow + row][cellCol + col])
            ) {
                return false;
            }
        }
    }

    return true;
}

// Paso 1: Definir variables para la puntuación
let score = 0;
const lineScores = [100, 200, 400, 800];
let linesClearedInMove = 0;

// Paso 2: Calcular la puntuación
function calculateScore() {
    score += lineScores[linesClearedInMove - 1];
    linesClearedInMove = 0;
}

function placeTetromino() {
    for (let row = 0; row < tetromino.matrix.length; row++) {
        for (let col = 0; col < tetromino.matrix[row].length; col++) {
            if (tetromino.matrix[row][col]) {
                if (tetromino.row + row < 0) {
                    return showGameOver();
                }
                playfield[tetromino.row + row][tetromino.col + col] = tetromino.name;
            }
        }
    }

    for (let row = playfield.length - 1; row >= 0; ) {
        if (playfield[row].every(cell => !!cell)) {
            for (let r = row; r >= 0; r--) {
                for (let c = 0; c < playfield[r].length; c++) {
                    playfield[r][c] = playfield[r-1][c];
                }
            }
            linesClearedInMove++; // Incrementa la cantidad de líneas eliminadas en este movimiento
        }
        else {
            row--;
        }
    }

    if (linesClearedInMove > 0) {
        calculateScore(); // Calcular la puntuación después de eliminar líneas
        updateScoreUI(); // Actualizar la interfaz de usuario con la nueva puntuación
    }

    tetromino = getNextTetromino();
    isSaved = false;
    tetrominoPlacedCount++; // Incrementa el contador de piezas colocadas
}

function showGameOver() {
    cancelAnimationFrame(rAF);
    gameOver = true;

    // Enviar el score al servidor
    fetch('procesar_puntuacion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ score: score })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Score saved:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });

    // Mostrar mensaje de GAME OVER
    context.fillStyle = 'black';
    context.globalAlpha = 0.75;
    context.fillRect(0, canvas.height / 2 - 30, canvas.width, 60);

    context.globalAlpha = 1;
    context.fillStyle = 'white';
    context.font = '36px monospace';
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    context.fillText('GAME OVER!', canvas.width / 2, canvas.height / 2);
    context.fillText('Score: ' + score, canvas.width / 2, canvas.height / 2 + 40);

    // Esperar 1 segundos antes de redirigir
    setTimeout(() => {
        window.location.href = 'ranking_global.php';
    }, 1000);
}

//PIEZA GUARDADA
function drawSavedPiece(piece) {
    const savedPieceCanvas = document.createElement('canvas');
    savedPieceCanvas.width = 100;
    savedPieceCanvas.height = 100;
    savedPieceCanvas.className = "pie";
    const savedPieceContext = savedPieceCanvas.getContext('2d');

    savedPieceContext.fillStyle = colors[piece];
    for (let row = 0; row < tetrominos[piece].length; row++) {
        for (let col = 0; col < tetrominos[piece][row].length; col++) {
            if (tetrominos[piece][row][col]) {
                savedPieceContext.fillRect(col * grid, row * grid, grid, grid);
            }
        }
    }

    const nextPieceElement = document.getElementById('nextPiece');
    nextPieceElement.innerHTML = '';
    nextPieceElement.appendChild(savedPieceCanvas);
}

const canvas = document.getElementById('game');
const context = canvas.getContext('2d');
const grid = 32;
const tetrominoSequence = [];
let savedPiece = null;
let isSaved = false;
let speed = 50; // Velocidad inicial
let speedIncreaseInterval = 10000; // Incremento de velocidad cada 10 segundos
let lastSpeedIncreaseTime = Date.now(); // Último tiempo de incremento de velocidad

const playfield = [];
for (let row = -2; row < 20; row++) {
    playfield[row] = [];
    for (let col = 0; col < 10; col++) {
        playfield[row][col] = 0;
    }
}

const tetrominos = {
    'I': [
        [0,0,0,0],
        [1,1,1,1],
        [0,0,0,0],
        [0,0,0,0]
    ],
    'J': [
        [1,0,0],
        [1,1,1],
        [0,0,0],
    ],
    'L': [
        [0,0,1],
        [1,1,1],
        [0,0,0],
    ],
    'O': [
        [1,1],
        [1,1],
    ],
    'S': [
        [0,1,1],
        [1,1,0],
        [0,0,0],
    ],
    'Z': [
        [1,1,0],
        [0,1,1],
        [0,0,0],
    ],
    'T': [
        [0,1,0],
        [1,1,1],
        [0,0,0],
    ]
};

const colors = {
    'I': 'cyan',
    'O': 'yellow',
    'T': 'purple',
    'S': 'green',
    'Z': 'red',
    'J': 'blue',
    'L': 'orange'
};

let count = 0;
let tetromino = getNextTetromino();
let rAF = null;
let gameOver = false;
let tetrominoPlacedCount = 0;


function loop() {
    rAF = requestAnimationFrame(loop);
    context.clearRect(0,0,canvas.width,canvas.height);

    for (let row = 0; row < 20; row++) {
        for (let col = 0; col < 10; col++) {
            if (playfield[row][col]) {
                const name = playfield[row][col];
                context.fillStyle = colors[name];
                context.fillRect(col * grid, row * grid, grid-1, grid-1);
            }
        }
    }

    if (tetromino) {
        if (++count > speed) {
            tetromino.row++;
            count = 0;

            if (!isValidMove(tetromino.matrix, tetromino.row, tetromino.col)) {
                tetromino.row--;
                placeTetromino();
            }
        }

        context.fillStyle = colors[tetromino.name];

        for (let row = 0; row < tetromino.matrix.length; row++) {
            for (let col = 0; col < tetromino.matrix[row].length; col++) {
                if (tetromino.matrix[row][col]) {
                    context.fillRect((tetromino.col + col) * grid, (tetromino.row + row) * grid, grid-1, grid-1);
                }
            }
        }
    }
    showNextPieces(); // Mostrar los próximos tetrominos en cada iteración del bucle de juego
}

// Función para aumentar la velocidad con el tiempo
function increaseSpeedWithTime() {
    const currentTime = Date.now();
    if (currentTime - lastSpeedIncreaseTime > speedIncreaseInterval && speed > 1) {
        speed--;
        lastSpeedIncreaseTime = currentTime;
    }
    setTimeout(increaseSpeedWithTime, 1000); // Revisa cada segundo si debe aumentar la velocidad
}

document.addEventListener('keydown', function(e) {
    if (gameOver) return;

    if (e.which === 37 || e.which === 39) {
        const col = e.which === 37 ? tetromino.col - 1 : tetromino.col + 1;
        if (isValidMove(tetromino.matrix, tetromino.row, col)) {
            tetromino.col = col;
        }
    }

    if (e.which === 38) {
        const matrix = rotate(tetromino.matrix);
        if (isValidMove(matrix, tetromino.row, tetromino.col)) {
            tetromino.matrix = matrix;
        }
    }

    if (e.which === 40) {
        const row = tetromino.row + 1;
        if (!isValidMove(tetromino.matrix, row, tetromino.col)) {
            tetromino.row = row - 1;
            placeTetromino();
            return;
        }
        tetromino.row = row;
    }

    if (e.which === 67 && !isSaved) {
        if (savedPiece !== null) {
            const temp = tetromino.name;
            tetromino = { name: savedPiece, matrix: tetrominos[savedPiece], row: -2, col: 3 }; // Set position to top center
            savedPiece = temp;
        } else {
            savedPiece = tetromino.name;
            tetromino = getNextTetromino();
        }
        drawSavedPiece(savedPiece);
        isSaved = true;
    }
});

document.addEventListener('keydown', function(e) {
    if (gameOver) return;

    if (e.which === 32) {
        while (isValidMove(tetromino.matrix, tetromino.row + 1, tetromino.col)) {
            tetromino.row++;
        }
        placeTetromino();
    }

    if (e.which === 67) {
        if (!isSaved) {
            isSaved = true;
            placeTetromino(); // Coloca la pieza en la posición inicial de caída
        }
    }
});

// Paso 3: Mostrar la puntuación en pantalla
function updateScoreUI() {
    const scoreElement = document.getElementById('score');
    scoreElement.textContent = 'Score: ' + score;
}


// Eventos de los botones para controles móviles
document.getElementById('save-piece').addEventListener('click', function() {
    if (gameOver) return;

    if (!isSaved) {
        if (savedPiece !== null) {
            const temp = tetromino.name;
            tetromino = { name: savedPiece, matrix: tetrominos[savedPiece], row: -2, col: 3 }; // Set position to top center
            savedPiece = temp;
        } else {
            savedPiece = tetromino.name;
            tetromino = getNextTetromino();
        }
        drawSavedPiece(savedPiece);
        isSaved = true;
    }
});

document.getElementById('move-left').addEventListener('click', function() {
    if (gameOver) return;

    const col = tetromino.col - 1;
    if (isValidMove(tetromino.matrix, tetromino.row, col)) {
        tetromino.col = col;
    }
});

document.getElementById('move-right').addEventListener('click', function() {
    if (gameOver) return;

    const col = tetromino.col + 1;
    if (isValidMove(tetromino.matrix, tetromino.row, col)) {
        tetromino.col = col;
    }
});

document.getElementById('rotate-piece').addEventListener('click', function() {
    if (gameOver) return;

    const rotatedMatrix = rotate(tetromino.matrix);
    if (isValidMove(rotatedMatrix, tetromino.row, tetromino.col)) {
        tetromino.matrix = rotatedMatrix;
    }
});

document.getElementById('drop-piece').addEventListener('click', function() {
    if (gameOver) return;

    while (isValidMove(tetromino.matrix, tetromino.row + 1, tetromino.col)) {
        tetromino.row++;
    }
    placeTetromino();
});


// Llama a updateScoreUI() después de iniciar el juego para mostrar la puntuación inicial
updateScoreUI();
increaseSpeedWithTime(); // Inicia el aumento de velocidad con el tiempo
rAF = requestAnimationFrame(loop);
showNextPieces(); // Mostrar los próximos tetrominos al inicio