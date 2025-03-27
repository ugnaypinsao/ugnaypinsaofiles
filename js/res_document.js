const fileList = JSON.parse(localStorage.getItem('uploadedFiles')) || [];

function displayFiles() {
    const fileListContainer = document.getElementById('file-list');
    fileListContainer.innerHTML = '';

    if (fileList.length === 0) {
        fileListContainer.innerHTML = '<p>No files available.</p>';
    } else {
        fileList.forEach((file) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                        <p>${file.name}</p>
                        <a href="${file.url}" target="_blank" download>Download</a>
                        <button onclick="printFile('${file.url}')">Print</button>
                    `;
            fileListContainer.appendChild(fileItem);
        });
    }
}

function printFile(url) {
    const printWindow = window.open(url, '_blank');
    printWindow.addEventListener('load', () => {
        printWindow.print();
    });
}

// Display files on page load
displayFiles();
