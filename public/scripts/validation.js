const firstname = document.getElementById("firstname");
const lastname = document.getElementById("lastname");
const email = document.getElementById("email");
const password = document.getElementById("password");
const passwordConfirm = document.getElementById("confirm_password");
const form = document.getElementById("form");

function validateEmail(email) { 
    let atSymbol = email.indexOf("@"); //regarde la position de @ 
    let dot = email.lastIndexOf("."); //regarde la position de .
    //si l'@ est au debut ou le . est avant le @ ou si il n'y a pas de nom de domaine
    if (atSymbol < 1 || dot <1 || dot <= atSymbol + 2 || dot + 2 >= email.length) {
        return false;
    }
    return true;
}

function containsSpecialChars(value) {
    const specialCharRegex = /[^a-zA-Z0-9\s._éàèùçâêîôûäëïöüÿÉÀÈÙÇÂÊÎÔÛÄËÏÖÜŸ-]/; //regex pour verifier les caracteres speciaux (y'en a bcp mais des noms ou prénoms peuvent en contenir)
    return specialCharRegex.test(value);
}

function validateForm() {
    let isValid = true;

    if (firstname && firstname.value.trim() === "") { //verifie si le prenom est vide, utilisation d etrim si espace + on regarde si le prenom est init car inscription et connexion sont sur le meme form
        alert("Le prénom est requis.");
        isValid = false;
    }
    else if (firstname && containsSpecialChars(firstname.value)) { //et regade les caracteres speciaux
        alert("Le prénom ne doit pas contenir de caractères spéciaux.");
        isValid = false;
    }

    if (lastname && lastname.value.trim() === "") { 
        alert("Le nom est requis.");
        isValid = false;
    }
    else if (lastname && containsSpecialChars(lastname.value)) {
        alert("Le nom ne doit pas contenir de caractères spéciaux.");
        isValid = false;
    }

    if (email && email.value.trim() === "") {
        alert("L'email est requis.");
        isValid = false;
    }
    if (email && !validateEmail(email.value)) { //verifie si l email est valide
        alert("L'email n'est pas valide.");
        isValid = false;
    }
    if (password && password.value.trim() === "") {
        alert("Le mot de passe est requis.");
        isValid = false;
    }
    if (passwordConfirm && passwordConfirm.value.trim() === "") {
        alert("La confirmation du mot de passe est requise.");
        isValid = false;
    }
    if (password && passwordConfirm && password.value !== passwordConfirm.value) {//verifie que les deux sont pareils
        alert("Les mots de passe ne correspondent pas.");
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
