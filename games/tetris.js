class TerminalTetris {
    constructor(container) {
        this.container = container;
        this.grid = Array(20).fill().map(() => Array(10).fill(0));
        this.currentPiece = null;
        this.score = 0;
        this.gameLoop = null;
        this.pieces = [
            [[1,1,1,1]],  // I
            [[1,1],[1,1]], // O
            [[1,1,1],[0,1,0]], // T
            [[1,1,1],[1,0,0]], // L
            [[1,1,1],[0,0,1]], // J
            [[1,1,0],[0,1,1]], // S
            [[0,1,1],[1,1,0]]  // Z
        ];
        this.init();
    }

    init() {
        this.drawUI();
        this.spawnPiece();
        this.bindControls();
        this.startGameLoop();
    }

    drawUI() {
        this.container.innerHTML = `
            <div style="text-align: left; font-family: monospace; line-height: 1;">
                <div>Score: ${this.score}</div>
                <div>${this.getGridDisplay()}</div>
                <div>Controls: ←↓→ | Space: Drop | Q: Quit</div>
            </div>
        `;
    }

    getGridDisplay() {
        let display = '┌' + '──'.repeat(10) + '┐\n';
        let displayGrid = this.grid.map(row => [...row]);
        
        if (this.currentPiece) {
            for (let y = 0; y < this.currentPiece.shape.length; y++) {
                for (let x = 0; x < this.currentPiece.shape[y].length; x++) {
                    if (this.currentPiece.shape[y][x]) {
                        const pieceY = this.currentPiece.y + y;
                        const pieceX = this.currentPiece.x + x;
                        if (pieceY >= 0 && pieceY < 20 && pieceX >= 0 && pieceX < 10) {
                            displayGrid[pieceY][pieceX] = 2;
                        }
                    }
                }
            }
        }

        for (let row of displayGrid) {
            display += '│' + row.map(cell => {
                if (cell === 2) return '▓▓';
                if (cell === 1) return '██';
                return '  ';
            }).join('') + '│\n';
        }
        display += '└' + '──'.repeat(10) + '┘';
        return display;
    }

    spawnPiece() {
        this.currentPiece = {
            shape: this.pieces[Math.floor(Math.random() * this.pieces.length)],
            x: 3,
            y: 0
        };
        if (this.checkCollision()) {
            this.gameOver();
        }
    }

    bindControls() {
        const handler = (e) => {
            switch(e.key) {
                case 'ArrowLeft':
                    this.movePiece(-1);
                    break;
                case 'ArrowRight':
                    this.movePiece(1);
                    break;
                case 'ArrowDown':
                    this.dropPiece();
                    break;
                case ' ':
                    this.hardDrop();
                    break;
                case 'ArrowUp':
                    this.rotatePiece();
                    break;
                case 'q':
                    this.gameOver();
                    document.removeEventListener('keydown', handler);
                    break;
            }
            e.preventDefault();
        };
        document.addEventListener('keydown', handler);
    }

    movePiece(dx) {
        this.currentPiece.x += dx;
        if (this.checkCollision()) {
            this.currentPiece.x -= dx;
        } else {
            this.drawUI();
        }
    }

    dropPiece() {
        this.currentPiece.y++;
        if (this.checkCollision()) {
            this.currentPiece.y--;
            this.lockPiece();
            this.clearLines();
            this.spawnPiece();
        }
        this.drawUI();
    }

    hardDrop() {
        while (!this.checkCollision()) {
            this.currentPiece.y++;
        }
        this.currentPiece.y--;
        this.lockPiece();
        this.clearLines();
        this.spawnPiece();
        this.drawUI();
    }

    rotatePiece() {
        const rotated = this.currentPiece.shape[0].map((_, i) =>
            this.currentPiece.shape.map(row => row[row.length - 1 - i])
        );
        const originalShape = this.currentPiece.shape;
        this.currentPiece.shape = rotated;
        if (this.checkCollision()) {
            this.currentPiece.shape = originalShape;
        } else {
            this.drawUI();
        }
    }

    checkCollision() {
        for (let y = 0; y < this.currentPiece.shape.length; y++) {
            for (let x = 0; x < this.currentPiece.shape[y].length; x++) {
                if (this.currentPiece.shape[y][x]) {
                    const newX = this.currentPiece.x + x;
                    const newY = this.currentPiece.y + y;
                    if (newX < 0 || newX >= 10 || newY >= 20 ||
                        (newY >= 0 && this.grid[newY][newX])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    lockPiece() {
        for (let y = 0; y < this.currentPiece.shape.length; y++) {
            for (let x = 0; x < this.currentPiece.shape[y].length; x++) {
                if (this.currentPiece.shape[y][x]) {
                    const newY = this.currentPiece.y + y;
                    if (newY >= 0) {
                        this.grid[newY][this.currentPiece.x + x] = 1;
                    }
                }
            }
        }
    }

    clearLines() {
        for (let y = this.grid.length - 1; y >= 0; y--) {
            if (this.grid[y].every(cell => cell)) {
                this.grid.splice(y, 1);
                this.grid.unshift(Array(10).fill(0));
                this.score += 100;
                y++;
            }
        }
    }

    startGameLoop() {
        this.gameLoop = setInterval(() => {
            this.dropPiece();
        }, 1000);
    }

    gameOver() {
        clearInterval(this.gameLoop);
        this.container.innerHTML = `
            <div style="text-align: center;">
                <div>Game Over!</div>
                <div>Score: ${this.score}</div>
                <div style="margin-top: 10px;">Drücke 'tetris' für ein neues Spiel</div>
            </div>
        `;
        if (this.score > 1000) {
            unlockAchievement('tetrisMaster');
        }
    }
} 