

const camera = document.getElementById("camera");
const canvas = document.getElementById("photoCanvas");
const photo = document.getElementById("visitorPhoto");
const message = document.getElementById("cameraMessage");

const start = document.getElementById("startCamera");
const take = document.getElementById("takePhoto");
const retake = document.getElementById("retakePhoto");

let stream;

// Start camera
start.onclick = async function () {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });

        camera.srcObject = stream;
        await camera.play();

        camera.classList.remove("hidden");
        canvas.classList.add("hidden");
        message.classList.add("hidden");
        start.classList.add("hidden");
        take.classList.remove("hidden");
    } catch (error) {
        alert("Camera failed: " + error.name);
        console.log(error);
    }
};

// Take photo
take.onclick = function () {
    if (!camera.videoWidth) {
        alert("Wait for the camera to load.");
        return;
    }

    canvas.width = camera.videoWidth;
    canvas.height = camera.videoHeight;
    canvas.getContext("2d").drawImage(camera, 0, 0);

    photo.value = canvas.toDataURL("image/png");

    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }

    camera.classList.add("hidden");
    canvas.classList.remove("hidden");
    take.classList.add("hidden");
    retake.classList.remove("hidden");
};

// Retake photo
retake.onclick = function () {
    photo.value = "";
    retake.classList.add("hidden");
    start.classList.remove("hidden");
    message.classList.remove("hidden");
    canvas.classList.add("hidden");
};
