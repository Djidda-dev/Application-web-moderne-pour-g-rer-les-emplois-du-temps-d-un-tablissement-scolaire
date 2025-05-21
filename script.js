document.getElementById('classeSelect').addEventListener('change', function () {
    const classe = this.value;
    fetch('get_emploi.php?classe=' + classe)
        .then(response => response.text())
        .then(data => {
            document.getElementById('emploiDuTemps').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
});