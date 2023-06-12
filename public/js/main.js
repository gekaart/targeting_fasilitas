function komoSel() {
    let a = document.getElementById("");
    alert("test");
}

// Modal manual js
var openModalBtn = document.getElementById("openModal");
var modal = document.getElementById("myModal");
var closeModal = document.getElementsByClassName("close")[0];

openModalBtn.onclick = function () {
    modal.style.display = "block";
};

closeModal.onclick = function () {
    modal.style.display = "none";
};

window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};
