const video = document.getElementById('video');

// Load models and start video
Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri('./models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('./models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('./models'),
    faceapi.nets.ageGenderNet.loadFromUri('./models')
]).then(startVideo);

async function loadLabeledImages() {
    const labels = ['Mahak Saxena', 'Saniya Parihar', 'Khadija Farhat', ]; // Add more names as needed
    return Promise.all(
        labels.map(async(label) => {
            const imgUrl = `static/images/${encodeURIComponent(label)}.jpeg`; // Adjust the image path
            const img = await faceapi.fetchImage(imgUrl);
            const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
            if (!detections) {
                throw new Error(`No face detected for ${label}`);
            }
            const { descriptor } = detections;
            return new faceapi.LabeledFaceDescriptors(label, [descriptor]);
        })
    );
}

function startVideo() {
    navigator.mediaDevices.getUserMedia({
        video: {}
    }).then(stream => {
        video.srcObject = stream;
    }).catch(err => console.error(err));
}

video.addEventListener('play', async() => {
    const canvas = faceapi.createCanvasFromMedia(video);
    document.body.append(canvas);
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    const labeledFaceDescriptors = await loadLabeledImages();
    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

    setInterval(async() => {
        const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

        resizedDetections.forEach(detection => {
            const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
            const box = detection.detection.box;
            const drawBox = new faceapi.draw.DrawBox(box, { label: bestMatch.toString() });
            drawBox.draw(canvas);
        });
    }, 100);
});