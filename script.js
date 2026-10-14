let notes = JSON.parse(localStorage.getItem('keep_notes')) || [
    {
        id: 1,
        title: "Welcome to Keep",
        content: "Click on any card to edit its contents, change colors, or manage checklist items!",
        isChecklist: false,
        items: [],
        color: "bg-amber-100",
        labels: ["Welcome", "Tips"]
    },
    {
        id: 2,
        title: "Groceries & To-dos",
        content: "",
        isChecklist: true,
        items: [
            { text: "Organic Milk", completed: false },
            { text: "Fresh Avocados", completed: true },
            { text: "Sourdough Bread", completed: false }
        ],
        color: "bg-white",
        labels: ["Shopping"]
    }
];

let isGridListView = true; // true for grid, false for list view
let isCreatingChecklist = false;
let creatorChecklistItems = ['', ''];
let activeEditingNoteId = null;
let editChecklistDraft = [];

// DOM Elements
const notesContainer = document.getElementById('notesContainer');
const emptyState = document.getElementById('emptyState');
const searchInput = document.getElementById('searchInput');
const clearSearch = document.getElementById('clearSearch');
const viewToggleBtn = document.getElementById('viewToggleBtn');
const viewIcon = document.getElementById('viewIcon');

// Creator form elements
const createNoteCard = document.getElementById('createNoteCard');
const createNoteTrigger = document.getElementById('createNoteTrigger');
const expandedForm = document.getElementById('expandedForm');
const newTitleInput = document.getElementById('newTitleInput');
const newContentInput = document.getElementById('newContentInput');
const closeNoteFormBtn = document.getElementById('closeNoteFormBtn');
const quickListBtn = document.getElementById('quickListBtn');
const toggleNoteTypeBtn = document.getElementById('toggleNoteTypeBtn');
const creatorChecklistContainer = document.getElementById('creatorChecklistContainer');
const addListItemBtnContainer = document.getElementById('addListItemBtnContainer');
const addListItemInputBtn = document.getElementById('addListItemInputBtn');
const floatingCreateBtn = document.getElementById('floatingCreateBtn');

// Edit Modal Elements
const editModal = document.getElementById('editModal');
const editModalContent = document.getElementById('editModalContent');
const editTitleInput = document.getElementById('editTitleInput');
const editContentInput = document.getElementById('editContentInput');
const editChecklistContainer = document.getElementById('editChecklistContainer');
const editAddListItemBtn = document.getElementById('editAddListItemBtn');
const closeEditModalBtn = document.getElementById('closeEditModalBtn');
const editPaletteBtn = document.getElementById('editPaletteBtn');
const colorPickerPopup = document.getElementById('colorPickerPopup');
const editLabelsContainer = document.getElementById('editLabelsContainer');

// Save to LocalStorage
function saveNotes() {
    localStorage.setItem('keep_notes', JSON.stringify(notes));
}

// Render Notes function
function renderNotes(filter = '') {
    notesContainer.innerHTML = '';
    
    // Adjust container layout class based on view toggle
    if (isGridListView) {
        notesContainer.className = "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4";
        viewIcon.innerText = "view_agenda";
    } else {
        notesContainer.className = "max-w-xl mx-auto flex flex-col space-y-3";
        viewIcon.innerText = "grid_view";
    }

    const filtered = notes.filter(note => {
        const matchTitle = note.title.toLowerCase().includes(filter.toLowerCase());
        const matchContent = note.content && note.content.toLowerCase().includes(filter.toLowerCase());
        const matchItems = note.items && note.items.some(i => i.text.toLowerCase().includes(filter.toLowerCase()));
        const matchLabels = note.labels && note.labels.some(l => l.toLowerCase().includes(filter.toLowerCase()));
        return matchTitle || matchContent || matchItems || matchLabels;
    });

    if (filtered.length === 0) {
        emptyState.classList.remove('hidden');
    } else {
        emptyState.classList.add('hidden');
    }

    filtered.forEach(note => {
        const card = document.createElement('div');
        card.className = `note-card group relative ${note.color || 'bg-white'} border border-gray-200 hover:border-gray-300 rounded-xl p-4 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between cursor-pointer`;
        
        let bodyHtml = '';
        if (note.isChecklist) {
            bodyHtml = `<div class="space-y-1.5 my-2 max-h-48 overflow-y-auto">` + 
                note.items.map(item => `
                    <div class="flex items-center space-x-2 text-sm">
                        <input type="checkbox" ${item.completed ? 'checked' : ''} disabled class="w-4 h-4 text-amber-500 rounded border-gray-300">
                        <span class="${item.completed ? 'line-through text-gray-400' : 'text-gray-800'} truncate">${item.text}</span>
                    </div>
                `).join('') + 
            `</div>`;
        } else {
            bodyHtml = `<p class="text-sm text-gray-700 whitespace-pre-wrap mt-1 line-clamp-6">${note.content || ''}</p>`;
        }

        // Labels HTML
        let labelsHtml = '';
        if (note.labels && note.labels.length > 0) {
            labelsHtml = `<div class="flex flex-wrap gap-1 mt-3">` + 
                note.labels.map(l => `<span class="bg-black/5 text-gray-700 text-xs px-2 py-0.5 rounded-full">${l}</span>`).join('') +
            `</div>`;
        }

        card.innerHTML = `
            <div>
                <div class="flex items-start justify-between">
                    <h3 class="font-medium text-gray-900 text-base flex-1 pr-2">${note.title || 'Untitled'}</h3>
                    <button onclick="event.stopPropagation(); deleteNote(${note.id})" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1 rounded-full transition-opacity cursor-pointer" title="Delete note">
                        <span class="material-icons-outlined text-lg">delete</span>
                    </button>
                </div>
                ${bodyHtml}
                ${labelsHtml}
            </div>
            <div class="flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity pt-3 mt-3 border-t border-black/5 text-gray-500 text-sm">
                <button class="p-1.5 rounded-full hover:bg-black/5" title="Reminder"><span class="material-icons-outlined text-base">add_alert</span></button>
                <button class="p-1.5 rounded-full hover:bg-black/5" title="Collaborator"><span class="material-icons-outlined text-base">person_add</span></button>
                <button class="p-1.5 rounded-full hover:bg-black/5" title="Background color"><span class="material-icons-outlined text-base">palette</span></button>
                <button class="p-1.5 rounded-full hover:bg-black/5" title="Archive"><span class="material-icons-outlined text-base">archive</span></button>
                <button class="p-1.5 rounded-full hover:bg-black/5" title="More"><span class="material-icons-outlined text-base">more_vert</span></button>
            </div>
        `;

        // Click card to open edit modal
        card.addEventListener('click', () => openEditModal(note.id));
        notesContainer.appendChild(card);
    });
}

// Top Creator Form Handlers
function openExpandedForm(checklist = false) {
    isCreatingChecklist = checklist;
    expandedForm.classList.remove('hidden');
    createNoteTrigger.classList.add('hidden');
    if (checklist) {
        showCreatorChecklist();
    } else {
        hideCreatorChecklist();
    }
    newTitleInput.focus();
}

function closeExpandedForm() {
    const title = newTitleInput.value.trim();
    const content = newContentInput.value.trim();
    
    if (isCreatingChecklist) {
        const activeItems = creatorChecklistItems.map(i => i.trim()).filter(i => i !== '');
        if (title || activeItems.length > 0) {
            notes.unshift({
                id: Date.now(),
                title: title || 'Untitled',
                content: '',
                isChecklist: true,
                items: activeItems.length > 0 ? activeItems.map(txt => ({ text: txt, completed: false })) : [{ text: '', completed: false }],
                color: 'bg-white',
                labels: []
            });
        }
    } else {
        if (title || content) {
            notes.unshift({
                id: Date.now(),
                title: title || 'Untitled',
                content: content,
                isChecklist: false,
                items: [],
                color: 'bg-white',
                labels: []
            });
        }
    }

    saveNotes();
    newTitleInput.value = '';
    newContentInput.value = '';
    creatorChecklistItems = ['', ''];
    isCreatingChecklist = false;
    hideCreatorChecklist();

    expandedForm.classList.add('hidden');
    createNoteTrigger.classList.remove('hidden');
    renderNotes(searchInput.value);
}

function showCreatorChecklist() {
    newContentInput.classList.add('hidden');
    creatorChecklistContainer.classList.remove('hidden');
    addListItemBtnContainer.classList.remove('hidden');
    renderCreatorChecklistInputs();
}

function hideCreatorChecklist() {
    newContentInput.classList.remove('hidden');
    creatorChecklistContainer.classList.add('hidden');
    addListItemBtnContainer.classList.add('hidden');
}

function renderCreatorChecklistInputs() {
    creatorChecklistContainer.innerHTML = creatorChecklistItems.map((val, idx) => `
        <div class="flex items-center space-x-2 text-sm">
            <span class="material-icons-outlined text-gray-400 text-base">drag_indicator</span>
            <input type="checkbox" disabled class="w-4 h-4 text-gray-300 rounded border-gray-300">
            <input type="text" value="${val}" placeholder="List item" oninput="updateCreatorItem(${idx}, this.value)" class="flex-1 border-none outline-none text-gray-800 text-sm bg-transparent placeholder-gray-400">
            <button type="button" onclick="removeCreatorItem(${idx})" class="text-gray-400 hover:text-gray-600"><span class="material-icons-outlined text-sm">close</span></button>
        </div>
    `).join('');
}

window.updateCreatorItem = function(idx, val) {
    creatorChecklistItems[idx] = val;
}

window.removeCreatorItem = function(idx) {
    creatorChecklistItems.splice(idx, 1);
    if (creatorChecklistItems.length === 0) creatorChecklistItems.push('');
    renderCreatorChecklistInputs();
}

addListItemInputBtn.addEventListener('click', () => {
    creatorChecklistItems.push('');
    renderCreatorChecklistInputs();
});

quickListBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    openExpandedForm(true);
});

toggleNoteTypeBtn.addEventListener('click', () => {
    isCreatingChecklist = !isCreatingChecklist;
    if (isCreatingChecklist) {
        showCreatorChecklist();
    } else {
        hideCreatorChecklist();
    }
});

createNoteTrigger.addEventListener('click', () => openExpandedForm(false));
floatingCreateBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    openExpandedForm(false);
});
closeNoteFormBtn.addEventListener('click', closeExpandedForm);

// Click outside creator form to save & close
document.addEventListener('click', (e) => {
    if (!createNoteCard.contains(e.target) && !expandedForm.classList.contains('hidden') && !editModal.contains(e.target)) {
        closeExpandedForm();
    }
});

// Edit Modal Logic
function openEditModal(id) {
    activeEditingNoteId = id;
    const note = notes.find(n => n.id === id);
    if (!note) return;

    editTitleInput.value = note.title;
    editModalContent.className = `bg-white w-full max-w-lg rounded-2xl p-6 shadow-2xl relative transition-colors duration-200 flex flex-col space-y-4 ${note.color || 'bg-white'}`;

    if (note.isChecklist) {
        editContentInput.classList.add('hidden');
        editChecklistContainer.classList.remove('hidden');
        editAddListItemBtn.classList.remove('hidden');
        editChecklistDraft = JSON.parse(JSON.stringify(note.items || []));
        renderEditChecklistDraft();
    } else {
        editContentInput.classList.remove('hidden');
        editChecklistContainer.classList.add('hidden');
        editAddListItemBtn.classList.add('hidden');
        editContentInput.value = note.content || '';
    }

    // Render labels
    editLabelsContainer.innerHTML = (note.labels || []).map(l => `
        <span class="bg-black/10 text-gray-800 text-xs px-2.5 py-1 rounded-full flex items-center gap-1">
            ${l}
        </span>
    `).join('');

    editModal.classList.remove('hidden');
}

function renderEditChecklistDraft() {
    editChecklistContainer.innerHTML = editChecklistDraft.map((item, idx) => `
        <div class="flex items-center space-x-2 text-sm group/edititem">
            <input type="checkbox" ${item.completed ? 'checked' : ''} onchange="toggleEditItemCheck(${idx})" class="w-4 h-4 text-amber-500 rounded border-gray-300 cursor-pointer">
            <input type="text" value="${item.text}" oninput="updateEditItemText(${idx}, this.value)" placeholder="List item" class="flex-1 border-none outline-none text-gray-800 bg-transparent">
            <button type="button" onclick="removeEditItem(${idx})" class="text-gray-400 hover:text-gray-700"><span class="material-icons-outlined text-sm">close</span></button>
        </div>
    `).join('');
}

window.toggleEditItemCheck = function(idx) {
    editChecklistDraft[idx].completed = !editChecklistDraft[idx].completed;
}

window.updateEditItemText = function(idx, val) {
    editChecklistDraft[idx].text = val;
}

window.removeEditItem = function(idx) {
    editChecklistDraft.splice(idx, 1);
    renderEditChecklistDraft();
}

editAddListItemBtn.addEventListener('click', () => {
    editChecklistDraft.push({ text: '', completed: false });
    renderEditChecklistDraft();
});

function closeEditModal() {
    if (!activeEditingNoteId) return;
    const note = notes.find(n => n.id === activeEditingNoteId);
    if (note) {
        note.title = editTitleInput.value.trim() || 'Untitled';
        if (note.isChecklist) {
            note.items = editChecklistDraft.filter(i => i.text.trim() !== '');
        } else {
            note.content = editContentInput.value;
        }
        saveNotes();
        renderNotes(searchInput.value);
    }
    editModal.classList.add('hidden');
    activeEditingNoteId = null;
    colorPickerPopup.classList.add('hidden');
}

closeEditModalBtn.addEventListener('click', closeEditModal);
editModal.addEventListener('click', (e) => {
    if (e.target === editModal) closeEditModal();
});

// Color palette toggle in edit modal
editPaletteBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    colorPickerPopup.classList.toggle('hidden');
});

document.addEventListener('click', () => {
    colorPickerPopup.classList.add('hidden');
});

window.setEditColor = function(colorClass) {
    const note = notes.find(n => n.id === activeEditingNoteId);
    if (note) {
        note.color = colorClass;
        editModalContent.className = `bg-white w-full max-w-lg rounded-2xl p-6 shadow-2xl relative transition-colors duration-200 flex flex-col space-y-4 ${colorClass}`;
        saveNotes();
        renderNotes(searchInput.value);
    }
    colorPickerPopup.classList.add('hidden');
}

window.deleteNote = function(id) {
    notes = notes.filter(n => n.id !== id);
    saveNotes();
    renderNotes(searchInput.value);
}

// Search & View Toggle
searchInput.addEventListener('input', (e) => {
    const val = e.target.value;
    if (val.length > 0) {
        clearSearch.classList.remove('hidden');
    } else {
        clearSearch.classList.add('hidden');
    }
    renderNotes(val);
});

clearSearch.addEventListener('click', () => {
    searchInput.value = '';
    clearSearch.classList.add('hidden');
    renderNotes('');
});

viewToggleBtn.addEventListener('click', () => {
    isGridListView = !isGridListView;
    renderNotes(searchInput.value);
});

// Initialize App on Window Load
window.onload = function() {
    renderNotes();
}