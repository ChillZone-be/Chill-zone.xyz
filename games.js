class TerminalSnake {
  constructor(container) {
    this.container = container;
    this.grid = [];
    this.snake = [];
    this.food = null;
    this.direction = 'right';
    this.score = 0;
    this.gameLoop = null;
    this.init();
  }

  init() {
    // Snake-Spiel Implementierung
  }
}

// Spiel starten
function startSnake() {
  const game = new TerminalSnake(document.querySelector('.game-container'));
} 