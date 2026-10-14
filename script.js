// Database API Configuration
const API_URL = 'database.php';

// State Management
let notes = [];
let currentEditingId = null;
let activeNoteColor = '#ffffff';
let modalNoteColor = '#ffffff';
let createNoteType = 'text'; // 'text' or 'checklist'
let modalNoteType = 'text';

// DOM Elements
const notesGrid = document.querySelector('.notes-grid');
const searchInput = document.querySelector('.search-container input');
const takeNoteContainer = document.getElementById('takeNoteContainer');
const takeNotePlaceholder = document.getElementById('takeNotePlaceholder');
const takeNoteExpanded = document.getElementById('takeNoteExpanded');
const createTitleInput = document.getElementById('createTitle');
const createBodyInput = document.getElementById('createBody');
const createChecklistContainer = document.getElementById('createChecklistContainer');
const addChecklistItemBtn = document.getElementById('addChecklistItemBtn');
const closeCreateBoxBtn = document.getElementById('closeCreateBoxBtn');
const textNoteBtn = document.getElementById('textNoteBtn');
const checklistNoteBtn = document.getElementById('checklistNoteBtn');
const colorPaletteBtn = document.getElementById('colorPaletteBtn');
const colorPaletteDropdown = document.getElementById('colorPaletteDropdown');

// Edit Modal Elements
const editModal = document.getElementById('editModal');
const editModalContent = document.getElementById('editModalContent');
const editTitleInput = document.getElementById('editTitle');
const editBodyInput = document.getElementById('editBody');
const editChecklistContainer = document.getElementById('editChecklistContainer');
const editAddChecklistItemBtn = document.getElementById('editAddChecklistItemBtn');
const closeEditModalBtn = document.getElementById('closeEditModalBtn');
const deleteNoteBtn = document.getElementById('deleteNoteBtn');
const editColorPaletteBtn = document.getElementById('editColorPaletteBtn');
const editColorPaletteDropdown = document.getElementById('editColorPaletteDropdown');

// Initialize App on DOM Load
document.addEventListener('DOMContentLoaded', () => {
    loadNotesFromDB();
    setupEventListeners();
});

// --- DATABASE API FUNCTIONS ---

async function loadNotesFromDB() {
    try {
        const response = await fetch(API_URL);
        if (!response.ok) throw new Error('Network response was not ok');
        notes = await response.json();
        renderNotes();
    } catch (error) {
        console.error('Error loading notes:', error);
    }
}

async function saveNoteToDB(noteData) {
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(noteData)
        });
        const result = await response.json();
        if (result.success) {
            loadNotesFromDB();
        }
    } catch (error) {
        console.error('Error saving note:', error);
    }
}

async function updateNoteInDB(noteData) {
    try {
        const response = await fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(noteData)
        });
        const result = await response.json();
        if (result.success) {
            loadNotesFromDB();
        }
    } catch (error) {
        console.error('Error updating note:', error);
    }
}

async function deleteNoteFromDB(noteId) {
    try {
        const response = await fetch(`${API_URL}?id=${noteId}`, {
            method: 'DELETE'
        });
        const result = await response.json();
        if (result.success) {
            loadNotesFromDB();
        }
    } catch (error) {
        console.error('Error deleting note:', error);
    }
}

// --- EVENT LISTENERS & UI INTERACTIONS ---

function setupEventListeners() {
    // Expand Take a Note box
    if (takeNotePlaceholder) {
        takeNotePlaceholder.addEventListener('click', (e) => {
            e.stopPropagation();
            takeNotePlaceholder.classList.add('hidden');
            takeNoteExpanded.classList.remove('hidden');
            createTitleInput.focus();
        });
    }

    // Close Create box when clicking outside
    document.addEventListener('click', (e) => {
        if (takeNoteContainer && takeNoteExpanded && !takeNoteExpanded.classList.contains('hidden')) {
            if (!takeNoteContainer.contains(e.target)) {
                submitNewNote();
            }
        }
        // Hide color dropdowns if clicked outside
        if (colorPaletteDropdown && !colorPaletteBtn.contains(e.target)) {
            colorPaletteDropdown.classList.add('hidden');
        }
        if (editColorPaletteDropdown && !editColorPaletteBtn.contains(e.target)) {
            editColorPaletteDropdown.classList.add('hidden');
        }
    });

    if (closeCreateBoxBtn) {
        closeCreateBoxBtn.addEventListener('click', () => {
            submitNewNote();
        });
    }

    // Switch note types in creator
    if (textNoteBtn && checklistNoteBtn) {
        textNoteBtn.addEventListener('click', () => {
            createNoteType = 'text';
            createBodyInput.classList.remove('hidden');
            createChecklistContainer.classList.add('hidden');
            addChecklistItemBtn.classList.add('hidden');
        });

        checklistNoteBtn.addEventListener('click', () => {
            createNoteType = 'checklist';
            createBodyInput.classList.add('hidden');
            createChecklistContainer.classList.remove('hidden');
            addChecklistItemBtn.classList.remove('hidden');
            if (createChecklistContainer.children.length === 0) {
                addChecklistItemRow(createChecklistContainer);
            }
        });
    }

    if (addChecklistItemBtn) {
        addChecklistItemBtn.addEventListener('click', () => {
            addChecklistItemRow(createChecklistContainer);
        });
    }

    if (editAddChecklistItemBtn) {
        editAddChecklistItemBtn.addEventListener('click', () => {
            addChecklistItemRow(editChecklistContainer);
        });
    }

    // Color Palettes
    if (colorPaletteBtn) {
        colorPaletteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            colorPaletteDropdown.classList.toggle('hidden');
        });
    }

    if (editColorPaletteBtn) {
        editColorPaletteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            editColorPaletteDropdown.classList.toggle('hidden');
        });
    }

    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', (e) => {
            activeNoteColor = e.target.getAttribute('data-color');
            takeNoteContainer.style.backgroundColor = activeNoteColor;
            colorPaletteDropdown.classList.add('hidden');
        });
    });

    document.querySelectorAll('.edit-color-option').forEach(option => {
        option.addEventListener('click', (e) => {
            modalNoteColor = e.target.getAttribute('data-color');
            editModalContent.style.backgroundColor = modalNoteColor;
            editColorPaletteDropdown.classList.add('hidden');
        });
    });

    // Search filtering
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            renderNotes(query);
        });
    }

    // Close Edit Modal
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', closeModal);
    }
    
    if (editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) closeModal();
        });
    }

    if (deleteNoteBtn) {
        deleteNoteBtn.addEventListener('click', () => {
            if (currentEditingId !== null) {
                deleteNoteFromDB(currentEditingId);
                editModal.classList.add('hidden');
                currentEditingId = null;
            }
        });
    }
}

// --- RENDER & UI LOGIC ---

function renderNotes(searchQuery = '') {
    if (!notesGrid) return;
    notesGrid.innerHTML = '';

    const filteredNotes = notes.filter(note => {
        const titleMatch = (note.title || '').toLowerCase().includes(searchQuery);
        const bodyMatch = (note.body || '').toLowerCase().includes(searchQuery);
        const itemMatch = note.items && note.items.some(i => (i.item_text || '').toLowerCase().includes(searchQuery));
        return titleMatch || bodyMatch || itemMatch;
    });

    if (filteredNotes.length === 0) {
        notesGrid.innerHTML = `<div class="col-span-full text-center text-gray-400 py-12">No notes found</div>`;
        return;
    }

    filteredNotes.forEach(note => {
        const card = document.createElement('div');
        card.className = `note-card rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition cursor-pointer relative group flex flex-col justify-between`;
        card.style.backgroundColor = note.color || '#ffffff';

        let contentHTML = '';
        if (note.title) {
            contentHTML += `<h3 class="font-medium text-gray-800 text-base mb-2">${escapeHTML(note.title)}</h3>`;
        }

        if (note.type === 'checklist' && note.items && note.items.length > 0) {
            contentHTML += `<div class="space-y-1 mb-2">`;
            note.items.forEach(item => {
                const checkedClass = item.is_completed == 1 ? 'line-through text-gray-400' : 'text-gray-700';
                contentHTML += `
                    <div class="flex items-center space-x-2 text-sm pointer-events-none">
                        <input type="checkbox" ${item.is_completed == 1 ? 'checked' : ''} class="rounded text-amber-500 focus:ring-0">
                        <span class="${checkedClass}">${escapeHTML(item.item_text)}</span>
                    </div>`;
            });
            contentHTML += `</div>`;
        } else if (note.body) {
            contentHTML += `<p class="text-gray-700 text-sm whitespace-pre-wrap mb-2">${escapeHTML(note.body)}</p>`;
        }

        card.innerHTML = `
            <div>${contentHTML}</div>
            <div class="flex items-center justify-end space-x-1 opacity-0 group-hover:opacity-100 transition mt-2">
                <button class="p-1.5 hover:bg-black hover:bg-opacity-10 rounded-full text-gray-600 delete-btn" title="Delete">
                    <span class="material-icons-outlined text-sm">delete</span>
                </button>
            </div>
        `;

        // Delete button listener
        const deleteBtn = card.querySelector('.delete-btn');
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            deleteNoteFromDB(note.id);
        });

        // Open modal on card click
        card.addEventListener('click', () => openEditModal(note));
        notesGrid.appendChild(card);
    });
}

function addChecklistItemRow(container, text = '', completed = false) {
    const row = document.createElement('div');
    row.className = 'flex items-center space-x-2 mt-2 checklist-row';
    row.innerHTML = `
        <input type="checkbox" ${completed ? 'checked' : ''} class="rounded text-amber-500 focus:ring-0 item-checkbox">
        <input type="text" value="${escapeHTML(text)}" placeholder="List item" class="flex-grow bg-transparent text-sm focus:outline-none item-text-input">
        <button type="button" class="text-gray-400 hover:text-gray-600 remove-row-btn">
            <span class="material-icons-outlined text-sm">close</span>
        </button>
    `;
    
    const textInput = row.querySelector('.item-text-input');

    // Pressing Enter creates a new row directly below and focuses it
    textInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const newRow = addChecklistItemRow(container, '', false);
            newRow.querySelector('.item-text-input').focus();
        } else if (e.key === 'Backspace' && textInput.value === '') {
            // If Backspace is pressed on an empty row, remove it and focus previous if available
            e.preventDefault();
            const prevRow = row.previousElementSibling;
            row.remove();
            if (prevRow) {
                const prevInput = prevRow.querySelector('.item-text-input');
                if (prevInput) prevInput.focus();
            }
        }
    });

    row.querySelector('.remove-row-btn').addEventListener('click', () => {
        row.remove();
    });

    container.appendChild(row);
    return row;
}

function submitNewNote() {
    const title = createTitleInput.value.trim();
    const body = createBodyInput.value.trim();
    let items = [];

    if (createNoteType === 'checklist') {
        const rows = createChecklistContainer.querySelectorAll('.checklist-row');
        rows.forEach(row => {
            const textInput = row.querySelector('.item-text-input');
            const checkbox = row.querySelector('.item-checkbox');
            if (textInput.value.trim() !== '') {
                items.push({
                    text: textInput.value.trim(),
                    completed: checkbox.checked ? 1 : 0
                });
            }
        });
    }

    if (title || body || items.length > 0) {
        const newNote = {
            title: title,
            body: body,
            type: createNoteType,
            color: activeNoteColor,
            items: items
        };
        saveNoteToDB(newNote);
    }

    // Reset Form
    createTitleInput.value = '';
    createBodyInput.value = '';
    createChecklistContainer.innerHTML = '';
    activeNoteColor = '#ffffff';
    takeNoteContainer.style.backgroundColor = '#ffffff';
    createNoteType = 'text';
    createBodyInput.classList.remove('hidden');
    createChecklistContainer.classList.add('hidden');
    addChecklistItemBtn.classList.add('hidden');
    takeNoteExpanded.classList.add('hidden');
    takeNotePlaceholder.classList.remove('hidden');
}

function openEditModal(note) {
    currentEditingId = note.id;
    modalNoteColor = note.color || '#ffffff';
    modalNoteType = note.type || 'text';

    editModalContent.style.backgroundColor = modalNoteColor;
    editTitleInput.value = note.title || '';
    editBodyInput.value = note.body || '';
    editChecklistContainer.innerHTML = '';

    if (modalNoteType === 'checklist') {
        editBodyInput.classList.add('hidden');
        editChecklistContainer.classList.remove('hidden');
        editAddChecklistItemBtn.classList.remove('hidden');
        if (note.items && note.items.length > 0) {
            note.items.forEach(item => {
                addChecklistItemRow(editChecklistContainer, item.item_text, item.is_completed == 1);
            });
        } else {
            addChecklistItemRow(editChecklistContainer);
        }
    } else {
        editBodyInput.classList.remove('hidden');
        editChecklistContainer.classList.add('hidden');
        editAddChecklistItemBtn.classList.add('hidden');
    }

    editModal.classList.remove('hidden');
}

function closeModal() {
    if (currentEditingId !== null) {
        const title = editTitleInput.value.trim();
        const body = editBodyInput.value.trim();
        let items = [];

        if (modalNoteType === 'checklist') {
            const rows = editChecklistContainer.querySelectorAll('.checklist-row');
            rows.forEach(row => {
                const textInput = row.querySelector('.item-text-input');
                const checkbox = row.querySelector('.item-checkbox');
                if (textInput.value.trim() !== '') {
                    items.push({
                        text: textInput.value.trim(),
                        completed: checkbox.checked ? 1 : 0
                    });
                }
            });
        }

        const updatedNote = {
            id: currentEditingId,
            title: title,
            body: body,
            color: modalNoteColor,
            items: items
        };

        updateNoteInDB(updatedNote);
    }

    editModal.classList.add('hidden');
    currentEditingId = null;
}

function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>'"]/g, 
        tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
    );
}