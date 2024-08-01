const video = document.getElementById('video');

// Load models and start video
Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri('./models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('./models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('./models'),
    faceapi.nets.ageGenderNet.loadFromUri('./models')
]).then(startVideo);

function startVideo() {
    navigator.mediaDevices.getUserMedia({ video: {} })
        .then(stream => video.srcObject = stream)
        .catch(err => console.error(err));
}

video.addEventListener('play', () => {
    const canvas = faceapi.createCanvasFromMedia(video);
    document.getElementById('container').append(canvas);
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    setInterval(async() => {
        const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors().withAgeAndGender();
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

        faceapi.draw.drawDetections(canvas, resizedDetections);
        faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);

        resizedDetections.forEach(face => {
            const { age, gender, genderProbability } = face;
            const genderText = `${gender} (${(genderProbability * 100).toFixed(2)}%)`;
            const ageText = `${Math.round(age)} years`;
            const textField = new faceapi.draw.DrawTextField([genderText, ageText], face.detection.box.bottomLeft);
            textField.draw(canvas);
        });
    }, 100);
});