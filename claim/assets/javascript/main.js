document.addEventListener('DOMContentLoaded', () => {
    // Countdown Timers
    document.querySelectorAll('.countdown-timer').forEach(timer => {
        let totalSeconds = parseInt(timer.getAttribute('data-seconds'));
        const update = () => {
            if (totalSeconds <= 0) return timer.textContent = "00:00:00";
            const h = Math.floor(totalSeconds / 3600), m = Math.floor((totalSeconds % 3600) / 60), s = totalSeconds % 60;
            timer.textContent = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
            totalSeconds--;
        };
        update(); setInterval(update, 1000);
    });
});