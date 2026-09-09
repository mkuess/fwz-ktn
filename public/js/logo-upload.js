(function () {
    'use strict';

    var input = document.getElementById('logo');
    var form = document.getElementById('organisation-registration-step-one');
    var status = document.getElementById('logo-upload-status');

    if (!input || !form || !status) {
        return;
    }

    var maxOriginalBytes = 20 * 1024 * 1024;
    var targetUploadBytes = 700 * 1024;
    var maxDimension = 1600;
    var processing = false;
    var ready = true;

    function formatMegabytes(bytes) {
        return (bytes / 1024 / 1024).toFixed(1).replace('.', ',') + ' MB';
    }

    function setError(message) {
        ready = false;
        status.textContent = message;
        status.classList.add('form-error');
        input.classList.add('is-error');
    }

    function setStatus(message) {
        ready = true;
        status.textContent = message;
        status.classList.remove('form-error');
        input.classList.remove('is-error');
    }

    function loadImage(file) {
        return new Promise(function (resolve, reject) {
            var image = new Image();
            var url = URL.createObjectURL(file);

            image.onload = function () {
                URL.revokeObjectURL(url);
                resolve(image);
            };
            image.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Das Bild konnte nicht gelesen werden.'));
            };
            image.src = url;
        });
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(resolve, type, quality);
        });
    }

    async function compressImage(file) {
        var image = await loadImage(file);
        var scale = Math.min(1, maxDimension / Math.max(image.naturalWidth, image.naturalHeight));
        var canvas = document.createElement('canvas');
        var outputType = file.type === 'image/jpeg' ? 'image/jpeg' : 'image/webp';
        var blob = null;

        for (var attempt = 0; attempt < 9; attempt++) {
            canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
            canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));

            var context = canvas.getContext('2d');
            if (outputType === 'image/jpeg') {
                context.fillStyle = '#ffffff';
                context.fillRect(0, 0, canvas.width, canvas.height);
            }
            context.drawImage(image, 0, 0, canvas.width, canvas.height);

            var quality = Math.max(0.48, 0.84 - (attempt * 0.05));
            blob = await canvasToBlob(canvas, outputType, quality);

            if (blob && blob.size <= targetUploadBytes) {
                break;
            }

            scale *= 0.82;
        }

        if (!blob) {
            throw new Error('Das Bild konnte nicht verkleinert werden.');
        }

        var baseName = file.name.replace(/\.[^.]+$/, '');
        var extension = outputType === 'image/webp' ? 'webp' : 'jpg';

        return new File([blob], baseName + '.' + extension, {
            type: outputType,
            lastModified: Date.now()
        });
    }

    input.addEventListener('change', async function () {
        var file = input.files && input.files[0];
        setStatus('');

        if (!file) {
            return;
        }

        if (file.size > maxOriginalBytes) {
            input.value = '';
            setError('Das ausgewählte Logo ist größer als 20 MB. Bitte wähle eine kleinere Bilddatei.');
            return;
        }

        if (file.type === 'image/svg+xml') {
            if (file.size > targetUploadBytes) {
                input.value = '';
                setError('Diese SVG-Datei ist zu groß. Bitte wähle eine SVG-Datei unter 700 KB.');
            } else {
                setStatus('Logo ausgewählt (' + formatMegabytes(file.size) + ').');
            }
            return;
        }

        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            setError('Bitte wähle eine JPG-, PNG-, WebP- oder SVG-Datei aus.');
            return;
        }

        if (file.size <= targetUploadBytes) {
            setStatus('Logo ausgewählt (' + formatMegabytes(file.size) + ').');
            return;
        }

        processing = true;
        ready = false;
        status.textContent = 'Das Logo wird für den Upload verkleinert …';

        try {
            var compressedFile = await compressImage(file);

            if (compressedFile.size > targetUploadBytes) {
                input.value = '';
                setError('Das Bild konnte nicht ausreichend verkleinert werden. Bitte wähle eine kleinere Datei.');
                return;
            }

            var transfer = new DataTransfer();
            transfer.items.add(compressedFile);
            input.files = transfer.files;
            setStatus(
                'Logo automatisch von ' + formatMegabytes(file.size) +
                ' auf ' + formatMegabytes(compressedFile.size) + ' verkleinert.'
            );
        } catch (error) {
            setError(error.message || 'Das Bild konnte nicht verkleinert werden.');
        } finally {
            processing = false;
        }
    });

    form.addEventListener('submit', function (event) {
        var file = input.files && input.files[0];

        if (file && file.size > targetUploadBytes) {
            setError('Das Logo ist noch zu groß und kann nicht hochgeladen werden. Bitte wähle eine kleinere Datei.');
        }

        if (processing || !ready) {
            event.preventDefault();
            status.focus();
        }
    });
}());