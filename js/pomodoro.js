let timerInterval = null;
let timeRemaining = 25 * 60; // 25 minutes in seconds
let isRunning = false;


function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}


function updateDisplay() {
    const display = document.getElementById('timerDisplay');
    if (display) {
        display.textContent = formatTime(timeRemaining);
    }
}


function startTimer() {
    if (isRunning) return;
    
    isRunning = true;
    timerInterval = setInterval(() => {
        if (timeRemaining > 0) {
            timeRemaining--;
            updateDisplay();
        } else {
            pauseTimer();
            alert('Pomodoro session complete! Time for a break.');
            resetTimer();
        }
    }, 1000);
}


function pauseTimer() {
    if (!isRunning) return;
    
    isRunning = false;
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
}


function resetTimer() {
    pauseTimer();
    timeRemaining = 25 * 60;
    updateDisplay();
}

document.addEventListener('DOMContentLoaded', function() {
    updateDisplay();
});