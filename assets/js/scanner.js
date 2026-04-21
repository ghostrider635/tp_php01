// scanner.js — Lecture de codes-barres via ZXing
// CDN ZXing chargé dans les pages qui utilisent ce script

const CDN_ZXING = 'https://unpkg.com/@zxing/library@latest/umd/index.min.js';

function chargerZXing(callback) {
    if (window.ZXing) { callback(); return; }
    const script = document.createElement('script');
    script.src = CDN_ZXING;
    script.onload = callback;
    document.head.appendChild(script);
}

document.addEventListener('DOMContentLoaded', () => {
    const btnScanner = document.getElementById('btn-scanner');
    const btnArreter = document.getElementById('btn-arreter');
    const video      = document.getElementById('video');
    const resultat   = document.getElementById('scanner-resultat');
    const inputCode  = document.getElementById('code_barre');

    if (!btnScanner || !video) return;

    let codeReader = null;

    btnScanner.addEventListener('click', () => {
        chargerZXing(() => {
            codeReader = new ZXing.BrowserMultiFormatReader();

            codeReader.listVideoInputDevices().then(devices => {
                if (!devices.length) {
                    resultat.textContent = 'Aucune caméra détectée.';
                    return;
                }

                const deviceId = devices[devices.length - 1].deviceId; // caméra arrière si dispo
                btnScanner.style.display = 'none';
                btnArreter.style.display = 'inline-block';

                codeReader.decodeFromVideoDevice(deviceId, video, (result, err) => {
                    if (result) {
                        const code = result.getText();
                        if (inputCode) inputCode.value = code;
                        if (resultat) resultat.textContent = '✔ Code détecté : ' + code;
                        arreterScanner();
                    }
                });
            }).catch(err => {
                resultat.textContent = 'Erreur caméra : ' + err.message;
            });
        });
    });

    function arreterScanner() {
        if (codeReader) codeReader.reset();
        btnScanner.style.display = 'inline-block';
        btnArreter.style.display = 'none';
    }

    btnArreter.addEventListener('click', arreterScanner);
});
