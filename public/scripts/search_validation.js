const query = document.getElementById("query");
const form = document.getElementById("form");


function containsSpecialChars(value) {
    const specialCharRegex = /[^a-zA-Z0-9\s._'-éàèùçâêîôûäëïöüÿÉÀÈÙÇÂÊÎÔÛÄËÏÖÜŸ-]/; //regex pour verifier les caracteres speciaux
    return specialCharRegex.test(value);
}

function validateForm() {
    let isValid = true;

    if (query && containsSpecialChars(query.value)) { //et regade les caracteres speciaux
        alert("Le champ de recherche ne doit pas contenir de caractères spéciaux.");
        isValid = false;
    }

    return isValid; 
}


document.addEventListener("DOMContentLoaded", function () {
    form.addEventListener("submit", function (event) {
        if (!validateForm()) {
            event.preventDefault(); //empeche la soumission
        }
    });
});
