<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident - Document Posting System</title>
    <link rel="stylesheet" href="assets/css/resident.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Document Posting System</h1>
        </header>

        <main>
            <section class="documents-section">
                <h2>Available Documents</h2>
                <div class="filter-controls">
                    <input type="text" id="searchInput" placeholder="Search documents...">
                </div>
                <div class="documents-list" id="documentsList">
                    <!-- Documents will be loaded here -->
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let allDocuments = [];

        // Load documents when the page loads
        loadDocumentList();

        // Function to fetch and load the documents from the server
        function loadDocumentList() {
            axios.get('php/getDocuments.php')
                .then(response => {
                    const documentsList = document.getElementById('documentsList');
                    documentsList.innerHTML = '';

                    if (response.data.success) {
                        allDocuments = response.data.documents.filter(doc => doc.status !== 'deleted');
                        renderDocumentList(allDocuments);
                    } else {
                        documentsList.innerHTML = '<p>No documents found.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading documents:', error);
                });
        }

        // Create the document card element
        function createDocumentCard(doc) {
            const card = document.createElement('div');
            card.className = 'document-card';

            card.innerHTML = `
                <h3>${doc.title}</h3>
                <p><strong>Description:</strong> ${doc.description}</p>
                <a href="uploads/${doc.file_url}" target="_blank">🔍 View PDF</a><br>
                <a href="uploads/${doc.file_url}" download>⬇️ Download PDF</a><br>
                <button onclick="printPDF('uploads/${doc.file_url}')">🖨️ Print PDF</button>
            `;

            return card;
        }

        // Render the document list on the page
        function renderDocumentList(documents) {
            const documentsList = document.getElementById('documentsList');
            documentsList.innerHTML = '';

            if (documents.length > 0) {
                documents.forEach(doc => {
                    const card = createDocumentCard(doc);
                    documentsList.appendChild(card);
                });
            } else {
                documentsList.innerHTML = '<p>No documents found.</p>';
            }
        }

        function printPDF(url) {
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = url;
            document.body.appendChild(iframe);
            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        }

        // Event listener for search input
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchQuery = this.value.toLowerCase();

            // Filter documents based on search query
            const filteredDocuments = allDocuments.filter(doc => {
                return doc.title.toLowerCase().includes(searchQuery) ||
                    doc.description.toLowerCase().includes(searchQuery);
            });

            renderDocumentList(filteredDocuments); // Re-render the filtered documents
        });
    </script>
</body>

</html>