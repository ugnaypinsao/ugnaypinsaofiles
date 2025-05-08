document.addEventListener('DOMContentLoaded', function() {
    // Get documents from localStorage (shared with admin)
    const documents = JSON.parse(localStorage.getItem('documents')) || [];
    
    // DOM Elements
    const documentsList = document.getElementById('documentsList');
    const searchInput = document.getElementById('searchInput');
    const adminLoginBtn = document.getElementById('adminLoginBtn');
    
    // Render documents
    function renderDocuments(docs = documents) {
        documentsList.innerHTML = '';
        
        if (docs.length === 0) {
            documentsList.innerHTML = '<p>No documents available yet.</p>';
            return;
        }
        
        docs.forEach((doc, index) => {
            const docCard = document.createElement('div');
            docCard.className = 'document-card';
            docCard.innerHTML = `
                <h3>${doc.title}</h3>
                <p>${doc.description || 'No description'}</p>
                <p><strong>Posted:</strong> ${new Date(doc.date).toLocaleString()}</p>
                <div class="document-actions">
                    <button class="view-btn" data-id="${index}">View</button>
                    <button class="download-btn" data-id="${index}">Download</button>
                    <button class="print-btn" data-id="${index}">Print</button>
                </div>
            `;
            documentsList.appendChild(docCard);
        });
        
        // Add event listeners to action buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                viewDocument(id);
            });
        });
        
        document.querySelectorAll('.download-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                downloadDocument(id);
            });
        });
        
        document.querySelectorAll('.print-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                printDocument(id);
            });
        });
    }
    
    // View document in new tab
    function viewDocument(id) {
        const doc = documents[id];
        const fileType = doc.fileType.includes('pdf') ? 'pdf' : 'doc';
        
        if (fileType === 'pdf') {
            // For PDF, we can open directly
            const win = window.open();
            win.document.write(`
                <html>
                    <head>
                        <title>${doc.title}</title>
                        <style>
                            body, html { margin: 0; padding: 0; height: 100%; }
                            iframe { width: 100%; height: 100%; border: none; }
                        </style>
                    </head>
                    <body>
                        <iframe src="${doc.fileContent}"></iframe>
                    </body>
                </html>
            `);
        } else {
            // For Word docs, we need to download first
            alert('Word documents need to be downloaded to view');
            downloadDocument(id);
        }
    }
    
    // Download document
    function downloadDocument(id) {
        const doc = documents[id];
        const link = document.createElement('a');
        link.href = doc.fileContent;
        link.download = doc.fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Print document
    function printDocument(id) {
        const doc = documents[id];
        
        if (doc.fileType.includes('pdf')) {
            // For PDF, open in new window and print
            const win = window.open();
            win.document.write(`
                <html>
                    <head>
                        <title>${doc.title}</title>
                        <style>
                            body, html { margin: 0; padding: 0; height: 100%; }
                            iframe { width: 100%; height: 100%; border: none; }
                        </style>
                        <script>
                            window.onload = function() {
                                setTimeout(function() {
                                    window.print();
                                }, 1000);
                            };
                        </script>
                    </head>
                    <body>
                        <iframe src="${doc.fileContent}"></iframe>
                    </body>
                </html>
            `);
        } else {
            // For Word docs, download first
            alert('Word documents need to be downloaded to print');
            downloadDocument(id);
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
    
    // Admin login
    adminLoginBtn.addEventListener('click', function() {
        // In a real app, this would show a login form
        // For demo, we'll just redirect with a password prompt
        const password = prompt('Enter admin password:');
        if (password === 'admin123') { // Demo password
            window.location.href = 'admin.html';
        } else {
            alert('Incorrect password');
        }
    });
    
    // Initial render
    renderDocuments();
});