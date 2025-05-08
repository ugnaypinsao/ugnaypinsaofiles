document.addEventListener('DOMContentLoaded', function() {
    // Simulated database
    let documents = JSON.parse(localStorage.getItem('documents')) || [];
    
    // DOM Elements
    const uploadForm = document.getElementById('uploadForm');
    const documentsList = document.getElementById('documentsList');
    const searchInput = document.getElementById('searchInput');
    const logoutBtn = document.getElementById('logoutBtn');
    
    // Render documents
    function renderDocuments(docs = documents) {
        documentsList.innerHTML = '';
        
        if (docs.length === 0) {
            documentsList.innerHTML = '<p>No documents posted yet.</p>';
            return;
        }
        
        docs.forEach((doc, index) => {
            const docCard = document.createElement('div');
            docCard.className = 'document-card';
            docCard.innerHTML = `
                <h3>${doc.title}</h3>
                <p>${doc.description || 'No description'}</p>
                <p><strong>Uploaded:</strong> ${new Date(doc.date).toLocaleString()}</p>
                <div class="document-actions">
                    <button class="delete-btn" data-id="${index}">Delete</button>
                </div>
            `;
            documentsList.appendChild(docCard);
        });
        
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                deleteDocument(id);
            });
        });
    }
    
    // Upload new document
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const title = document.getElementById('docTitle').value;
        const description = document.getElementById('docDescription').value;
        const fileInput = document.getElementById('docFile');
        
        if (fileInput.files.length === 0) {
            alert('Please select a file to upload');
            return;
        }
        
        const file = fileInput.files[0];
        const fileName = file.name;
        const fileType = file.type;
        const fileSize = file.size;
        
        // Check file type
        if (!fileType.includes('pdf') && !fileType.includes('msword') && !fileType.includes('wordprocessingml')) {
            alert('Please upload only PDF or Word documents');
            return;
        }
        
        // Check file size (max 5MB)
        if (fileSize > 5 * 1024 * 1024) {
            alert('File size should be less than 5MB');
            return;
        }
        
        // Read file as base64
        const reader = new FileReader();
        reader.onload = function(e) {
            const fileContent = e.target.result;
            
            // Add to documents array
            const newDoc = {
                title,
                description,
                fileName,
                fileType,
                fileContent,
                date: new Date().toISOString()
            };
            
            documents.unshift(newDoc);
            localStorage.setItem('documents', JSON.stringify(documents));
            
            // Reset form and render documents
            uploadForm.reset();
            renderDocuments();
            
            alert('Document uploaded successfully!');
        };
        reader.readAsDataURL(file);
    });
    
    // Delete document
    function deleteDocument(id) {
        if (confirm('Are you sure you want to delete this document?')) {
            documents.splice(id, 1);
            localStorage.setItem('documents', JSON.stringify(documents));
            renderDocuments();
        }
    }
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filteredDocs = documents.filter(doc => 
            doc.title.toLowerCase().includes(searchTerm) || 
            (doc.description && doc.description.toLowerCase().includes(searchTerm))
        );
        renderDocuments(filteredDocs);
    });
    
    // Logout
    logoutBtn.addEventListener('click', function() {
        // In a real app, you would clear the session/token
        alert('Logged out successfully');
        window.location.href = 'resident.html'; // Redirect to resident view
    });
    
    // Initial render
    renderDocuments();
});