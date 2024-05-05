let endDate = new Date('2024-12-31T23:59:59'); // Date de fin (année-mois-jourTheure:minute:seconde)

function updateCountdown() {
    let currentDate = new Date();
    let timeDifference = endDate - currentDate;

    if (timeDifference <= 0) {
        document.getElementById('counter').textContent = 'Temps écoulé';
    } else {
        let days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
        let hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

        document.getElementById('counter').textContent = `Temps restant: ${days} jours, ${hours} heures, ${minutes} minutes, ${seconds} secondes`;
    }
}

setInterval(updateCountdown, 1000); // Met 