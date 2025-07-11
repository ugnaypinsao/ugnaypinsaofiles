<?php
include '../php/conn.php';
$db = new DatabaseHandler();
require 'head.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Document Posting System</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <div class="container">
        <header>
            <h1><a href="index.php">Document Posting System - Admin</a></h1>
        </header>

        <main>
            <section class="upload-section">
                <h2>Upload New Document</h2>
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="docTitle">Document Title:</label>
                        <input type="text" name="docTitle" id="docTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="docDescription">Description:</label>
                        <textarea id="docDescription" name="docDescription" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="docFile">Select File (PDF or DOC):</label>
                        <input type="file" id="docFile" name="docFile" accept=".pdf,.doc,.docx" required>
                    </div>
                    <button type="submit">Upload Document</button>
                </form>
            </section>

            <section class="documents-section">
                <h2>Posted Documents</h2>
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

        // Upload Document
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = document.getElementById('uploadForm');
            const formData = new FormData(form);

            axios.post('../php/uploadDocument.php', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    // if (response.data.success) {
                    //     alert(response.data.message);
                    //     loadDocumentList(); // Reload document list after uploading
                    // } else {
                    //     alert('Error: ' + response.data.message);
                    // }
                        loadDocumentList(); // Reload document list after uploading
                })
                .catch(error => {
                    console.error('Error uploading document:', error);
                    alert('An error occurred. Please try again.');
                });
        });

        // Load documents list
        function loadDocumentList() {
            axios.get('../php/getDocuments.php')
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



        // Create document card
        function createDocumentCard(doc) {
            const card = document.createElement('div');
            card.className = 'document-card';

            card.innerHTML = `
        <h3>${doc.title}</h3>
        <p><strong>Description:</strong> ${doc.description}</p>
        <a href="${doc.file_url}" target="_blank">Download Document</a>
        <div class="admin-actions">
            <button class="delete-btn" data-id="${doc.id}">Delete</button>
        </div>
    `;

            card.querySelector('.delete-btn').addEventListener('click', () => deleteDocument(doc.id));

            return card;
        }

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

        // Delete document
        function deleteDocument(documentId) {
            const reason = prompt("Please enter the reason for deleting this document:");

            if (reason) {
                axios.post('../php/deleteDocument.php', {
                        'document-id': documentId,
                        'reason_for_delete': reason
                    })
                    .then(response => {
                        if (response.data.success) {
                            alert(response.data.message);
                            loadDocumentList(); // Reload document list after deletion
                        } else {
                            alert('Error: ' + response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting document:', error);
                        alert('An error occurred. Please try again.');
                    });
            } else {
                alert('Deletion reason is required.');
            }
        }


        // Initial load
        loadDocumentList();
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const filteredDocs = allDocuments.filter(doc =>
                doc.title.toLowerCase().includes(query) ||
                (doc.description && doc.description.toLowerCase().includes(query))
            );
            renderDocumentList(filteredDocs);
        });
    </script>

</body>

</html>