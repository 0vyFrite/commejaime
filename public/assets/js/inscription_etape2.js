
function calculerIMC(){

    const taille = parseFloat(document.getElementById('taille_cm').value);
    const poids = parseFloat(document.getElementById('poids_kg').value);
    const box = document.getElementById('imc-box');

    if(!taille || !poids){
        box.style.display = 'none';
        return;
    }

    const imc = (poids / ((taille / 100) ** 2)).toFixed(2);

    let cat = '';

    // Trouver la catégorie correspondante
    for(let categorie of categoriesIMC) {
        if(imc >= categorie.seuil_min && imc < categorie.seuil_max) {
            cat = categorie.label;
            break;
        }
    }

    box.style.display = 'block';

    box.innerHTML =
        '<strong>IMC estimé :</strong> ' +
        imc +
        ' — ' +
        cat;
}


