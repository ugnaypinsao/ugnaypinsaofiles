const fileList = JSON.parse(localStorage.getItem('uploadedFiles')) || [];

function displayFiles() {
    const fileListContainer = document.getElementById('file-list');
    fileListContainer.innerHTML = '';

    fileList.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        if (file.type.startsWith('image/')) {
            fileItem.innerHTML = `
                        <img src="${file.url}" alt="${file.name}" class="file-preview">
                        <p>${file.name}</p>
                        <button onclick="deleteFile(${index})">Delete</button>
                    `;
        } else {
            fileItem.innerHTML = `
                        <p>${file.name}</p>
                        <a href="${file.url}" download>Download</a>
                        <button onclick="deleteFile(${index})">Delete</button>
                    `;
        }
        fileListContainer.appendChild(fileItem);
    });
}

document.getElementById('file-upload-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const fileInput = document.getElementById('file');
    const file = fileInput.files[0];

    if (file) {
        const fileURL = URL.createObjectURL(file);
        fileList.push({ name: file.name, url: fileURL, type: file.type });
        localStorage.setItem('uploadedFiles', JSON.stringify(fileList));
        fileInput.value = ''; // Reset input
        displayFiles();
    } else {
        alert('Please select a file to upload.');
    }
});

function deleteFile(index) {
    fileList.splice(index, 1);
    localStorage.setItem('uploadedFiles', JSON.stringify(fileList));
    displayFiles();
}

// Display existing files on page load
displayFiles();
