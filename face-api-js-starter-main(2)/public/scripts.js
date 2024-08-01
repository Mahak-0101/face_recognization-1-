const video = document.getElementById('video');
const attendanceTable = document.getElementById('attendanceTable').getElementsByTagName('tbody')[0];
const recognizedFaces = new Set();
const attendanceData = [];

// Load models and start video
Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri('./models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('./models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('./models'),
    faceapi.nets.ageGenderNet.loadFromUri('./models')
]).then(startVideo);

async function loadLabeledImages() {
    const labels = ['Mahak Saxena', 'Saniya Parihar', 'Khadija Farhat'];
    return Promise.all(
        labels.map(async (label) => {
            const imgUrl = `static/images/${encodeURIComponent(label)}.jpeg`; // Adjust the image path if necessary
            const img = await faceapi.fetchImage(imgUrl);
            const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
            if (!detections) {
                console.warn(`No face detected for ${label}`);
                return null; // Return null for labels with no detected face
            }
            const { descriptor } = detections;
            return new faceapi.LabeledFaceDescriptors(label, [descriptor]);
        })
    ).then(descriptors => descriptors.filter(descriptor => descriptor !== null));
}

function startVideo() {
    // Check for getUserMedia support
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error('Error accessing webcam: ', err);
                alert('Error accessing webcam. Please ensure the webcam is connected and permissions are granted.');
            });
    } else {
        console.error('getUserMedia not supported in this browser.');
        alert('getUserMedia not supported in this browser. Please use a modern browser.');
    }
}

video.addEventListener('play', async () => {
    const canvas = faceapi.createCanvasFromMedia(video);
    document.body.append(canvas);
    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    const labeledFaceDescriptors = await loadLabeledImages();
    const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

    setInterval(async () => {
        const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
        const resizedDetections = faceapi.resizeResults(detections, displaySize);

        // Clear the previous canvas content
        const context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);

        // Draw bounding boxes for detected faces
        resizedDetections.forEach(detection => {
            const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
            const { box } = detection.detection;

            // Draw the bounding box with correct scaling and color
            const drawBox = new faceapi.draw.DrawBox(box, { label: bestMatch.toString(), boxColor: 'green' });
            drawBox.draw(canvas);

            if (bestMatch.label !== 'unknown' && !recognizedFaces.has(bestMatch.label)) {
                recognizedFaces.add(bestMatch.label);
                const time = new Date().toLocaleTimeString();
                markAttendance(bestMatch.label, time);
            }
        });
    }, 100);
});

function markAttendance(label, time) {
    const rollNumber = getRollNumber(label);
    const branch = getBranch(label);

    attendanceData.push({ name: label, rollNumber, branch, time });

    const row = attendanceTable.insertRow();
    row.insertCell(0).textContent = label;
    row.insertCell(1).textContent = rollNumber;
    row.insertCell(2).textContent = branch;
    row.insertCell(3).textContent = time;

    row.style.animation = 'fadeIn 1s ease-in-out';
}

document.getElementById('submitAttendance').addEventListener('click', () => {
    console.log('Submitting attendance data:', attendanceData); // Log the data being submitted
    fetch('mark_attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(attendanceData),
    })
    .then(response => response.text()) // Get the raw text response
    .then(text => {
        console.log('Raw response:', text); // Log the raw response text
        try {
            const data = JSON.parse(text); // Attempt to parse the JSON
            if (data.status === 'success') {
                console.log('Attendance recorded successfully');
                alert('Attendance recorded successfully');
            } else {
                console.error('Error recording attendance:', data.message);
                alert('Error recording attendance: ' + data.message);
            }
        } catch (error) {
            console.error('Error parsing JSON:', error, 'Response text:', text);
            alert('Error recording attendance');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error recording attendance');
    });
});

function getRollNumber(label) {
    const rollNumbers = {
        'Mahak Saxena': '22049c04034',
        'Saniya Parihar': '22049c04048',
        'Khadija Farhat': '22049c04028'
    };
    return rollNumbers[label];
}

function getBranch(label) {
    const branches = {
        'Mahak Saxena': 'Computer Science',
        'Saniya Parihar': 'Computer Science',
        'Khadija Farhat': 'Computer Science'
    };
    return branches[label];
}
