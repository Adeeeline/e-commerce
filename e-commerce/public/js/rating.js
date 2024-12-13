document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.rating-stars input');
    stars.forEach((star) => {
        star.addEventListener('change', (e) => {
            const selectedValue = e.target.value;

            // Mettre en surbrillance les étoiles sélectionnées
            stars.forEach((otherStar) => {
                if (parseInt(otherStar.value) <= parseInt(selectedValue)) {
                    otherStar.parentElement.style.color = 'gold'; // Étoile sélectionnée
                } else {
                    otherStar.parentElement.style.color = 'gray'; // Non sélectionnée
                }
            });
        });
    });
});
