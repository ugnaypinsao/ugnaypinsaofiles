document.addEventListener('DOMContentLoaded', function() {
    // Initialize the application
    initFileManager();
    
    // Listen for storage changes from admin panel
    window.addEventListener('storage', handleStorageChange);
});

function initFileManager() {
    // Load files from localStorage
    const fileList = getFilesFromStorage();
    
    // Display files on page load
    displayFiles(fileList);
    updateFileCounter(fileList.length);
    
    // Set up event listeners
    setupEventListeners();
}

function getFilesFromStorage() {
    return JSON.parse(localStorage.getItem('uploadedFiles')) || [];
}

function handleStorageChange() {
    // When storage changes (from admin panel), reload files
    const fileList = getFilesFromStorage();
    displayFiles(fileList);
    updateFileCounter(fileList.length);
}

function displayFiles(files = []) {
    const fileListContainer = document.getElementById('file-list');
    const emptyState = document.getElementById('emptyState');
    
    // Clear current display
    fileListContainer.innerHTML = '';
    
    // Show empty state if no files
    if (files.length === 0) {
        fileListContainer.style.display = 'none';
        emptyState.style.display = 'flex';
        return;
    }
    
    // Show file grid if files exist
    fileListContainer.style.display = 'grid';
    emptyState.style.display = 'none';
    
    // Create file cards for each file
    files.forEach((file) => {
        const fileItem = createFileCard(file);
        fileListContainer.appendChild(fileItem);
    });
}

function createFileCard(file) {
    // Create file card element
    const fileItem = document.createElement('div');
    fileItem.className = 'file-item';
    
    // Determine file type icon
    const fileIcon = getFileIcon(file.name);
    
    // Format date if available
    const uploadDate = file.uploadDate ? new Date(file.uploadDate).toLocaleDateString() : 'Date not available';
    
    // Create file card HTML
    fileItem.innerHTML = `
        <div class="file-header">
            <i class="fas ${fileIcon} file-icon"></i>
            <h3 class="file-title" title="${file.title || 'Untitled Document'}">${file.title || 'Untitled Document'}</h3>
        </div>
        <div class="file-body">
            <p class="file-name" title="${file.name}">${file.name}</p>
            <div class="file-actions">
                <a href="${file.url}" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download
                </a>
                <button class="btn btn-secondary" onclick="printFile('${file.url}')">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
            <div class="file-meta">
                <span>${file.size || formatFileSize(0)}</span>
                <span>${uploadDate}</span>
            </div>
        </div>
    `;
    
    return fileItem;
}

function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    
    switch(extension) {
        case 'pdf':
            return 'fa-file-pdf';
        case 'doc':
        case 'docx':
            return 'fa-file-word';
        case 'xls':
        case 'xlsx':
            return 'fa-file-excel';
        case 'ppt':
        case 'pptx':
            return 'fa-file-powerpoint';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            return 'fa-file-image';
        case 'txt':
            return 'fa-file-alt';
        case 'zip':
        case 'rar':
            return 'fa-file-archive';
        default:
            return 'fa-file';
    }
}

function formatFileSize(bytes) {
    if (!bytes) return 'Size unknown';
    
    const units = ['bytes', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;
    
    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    
    return `${size.toFixed(1)} ${units[unitIndex]}`;
}

function filterFiles() {
    const searchInput = document.getElementById('searchBar').value.toLowerCase();
    const fileList = getFilesFromStorage();
    
    const filteredFiles = fileList.filter(file => 
        file.name.toLowerCase().includes(searchInput) || 
        (file.title && file.title.toLowerCase().includes(searchInput))
    );
    
    displayFiles(filteredFiles);
    updateFileCounter(filteredFiles.length);
}

function clearSearch() {
    document.getElementById('searchBar').value = '';
    filterFiles();
}

function sortFiles() {
    const sortValue = document.getElementById('sortSelect').value;
    const fileList = getFilesFromStorage();
    let sortedFiles = [...fileList];
    
    switch(sortValue) {
        case 'name-asc':
            sortedFiles.sort((a, b) => a.name.localeCompare(b.name));
            break;
        case 'name-desc':
            sortedFiles.sort((a, b) => b.name.localeCompare(a.name));
            break;
        case 'date-new':
            sortedFiles.sort((a, b) => new Date(b.uploadDate || 0) - new Date(a.uploadDate || 0));
            break;
        case 'date-old':
            sortedFiles.sort((a, b) => new Date(a.uploadDate || 0) - new Date(b.uploadDate || 0));
            break;
        default:
            break;
    }
    
    displayFiles(sortedFiles);
}

function printFile(url) {
    const printWindow = window.open(url, '_blank');
    
    setTimeout(() => {
        if (printWindow) {
            printWindow.print();
        } else {
            alert('Popup was blocked. Please allow popups for this site to print documents.');
        }
    }, 500);
}

function updateFileCounter(count) {
    const counterElement = document.getElementById('fileCounter');
    counterElement.textContent = `${count} document${count !== 1 ? 's' : ''}`;
}

function setupEventListeners() {
    // Add any additional event listeners here
}