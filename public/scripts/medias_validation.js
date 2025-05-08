const title = document.getElementById("title");
const author = document.getElementById("author");
const type = document.getElementById("type");
const description = document.getElementById("description");
const image = document.getElementById("image");
const form = document.getElementById("form");


function containsSpecialChars(value) {
    const specialCharRegex = /[^a-zA-Z0-9\s._'-éàèùçâêîôûäëïöüÿÉÀÈÙÇÂÊÎÔÛÄËÏÖÜŸ-]/; //regex pour verifier les caracteres speciaux
    return specialCharRegex.test(value);
}

function validateForm() {
    let isValid = true;

    if (title && title.value.trim() === "") { // pareil qu'avec les logs
        alert("Le titre est requis.");
        isValid = false;
    }
    else if (title && containsSpecialChars(title.value)) { //et regade les caracteres speciaux
        alert("Le titre ne doit pas contenir de caractères spéciaux.");
        isValid = false;
    }

    if (author && author.value.trim() === "") { 
        alert("L'auteur est requis.");
        isValid = false;
    }
    else if (author && containsSpecialChars(author.value)) {
        alert("L'auteur ne doit pas contenir de caractères spéciaux.");
        isValid = false;
    }

    if (type && type.value.trim() === "") {
        alert("Le type est requis.");
        isValid = false;
    }
    else if (type && containsSpecialChars(type.value)) {
        alert("Le type ne doit pas contenir de caractères spéciaux.");
        isValid = false;
    }

    if (description && description.value.trim() === "") {
        alert("La description est requise.");
        isValid = false;
    }
    else if (description && containsSpecialChars(description.value)) {
        alert("La description ne doit pas contenir de caractères spéciaux.");
        isValid = false;
    }

    if (image && image.value.trim() === "") {
        alert("Le chemin de l'image est requise.");
        isValid = false;
    }
    else if (image && containsSpecialChars(image.value)) {
        alert("Le chemin de l'image ne doit pas contenir de caractères spéciaux.");
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
