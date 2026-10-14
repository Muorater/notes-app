<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keep Notes</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-white min-h-screen font-['Roboto']">

    <!-- Top Navigation Bar -->
    <header class="flex items-center justify-between px-4 py-2 border-b border-gray-200 sticky top-0 bg-white z-10">
        <div class="flex items-center space-x-4">
            <button class="p-2 hover:bg-gray-100 rounded-full">
                <span class="material-icons-outlined text-gray-600">menu</span>
            </button>
            <div class="flex items-center space-x-2">
                <span class="material-icons-outlined text-amber-500 text-3xl">lightbulb</span>
                <span class="text-xl text-gray-700 font-normal">Keep</span>
            </div>
        </div>
        
        <div class="flex-1 max-w-2xl mx-4">
            <div class="flex items-center bg-gray-100 px-4 py-2 rounded-lg focus-within:bg-white focus-within:shadow-md transition">
                <span class="material-icons-outlined text-gray-500 mr-3">search</span>
                <input type="text" placeholder="Search" class="w-full bg-transparent focus:outline-none text-gray-700">
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button class="p-2 hover:bg-gray-100 rounded-full">
                <span class="material-icons-outlined text-gray-600">view_agenda</span>
            </button>
            <button class="p-2 hover:bg-gray-100 rounded-full">
                <span class="material-icons-outlined text-gray-600">settings</span>
            </button>
            <div class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center font-medium">M</div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <!-- Take a Note Box -->
        <div id="takeNoteContainer" class="max-w-xl mx-auto mb-8 bg-white border border-gray-300 rounded-lg shadow-sm transition-all duration-200">
            <!-- Placeholder State -->
            <div id="takeNotePlaceholder" class="p-3 flex items-center justify-between cursor-text text-gray-500 text-sm">
                <span>Take a note...</span>
                <div class="flex space-x-1">
                    <button class="p-2 hover:bg-gray-100 rounded-full" id="checklistNoteBtn"><span class="material-icons-outlined text-gray-600">check_box</span></button>
                    <button class="p-2 hover:bg-gray-100 rounded-full" id="textNoteBtn"><span class="material-icons-outlined text-gray-600">brush</span></button>
                </div>
            </div>

            <!-- Expanded State -->
            <div id="takeNoteExpanded" class="p-3 hidden">
                <input type="text" id="createTitle" placeholder="Title" class="w-full font-medium text-gray-800 bg-transparent focus:outline-none mb-2">
                <textarea id="createBody" placeholder="Take a note..." rows="3" class="w-full text-sm text-gray-700 bg-transparent focus:outline-none resize-none"></textarea>
                
                <!-- Dynamic Checklist Container for Creator -->
                <div id="createChecklistContainer" class="space-y-1 mb-2 hidden"></div>
                <button type="button" id="addChecklistItemBtn" class="text-xs text-amber-600 hover:underline mb-2 hidden">+ Add item</button>

                <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center space-x-1 relative">
                        <button type="button" id="colorPaletteBtn" class="p-1.5 hover:bg-gray-100 rounded-full text-gray-600" title="Background options">
                            <span class="material-icons-outlined text-sm">palette</span>
                        </button>
                        <!-- Color Palette Dropdown -->
                        <div id="colorPaletteDropdown" class="hidden absolute left-0 bottom-full mb-2 bg-white border border-gray-200 shadow-lg rounded-lg p-2 flex space-x-2 z-20">
                            <div class="w-6 h-6 rounded-full bg-white border border-gray-300 cursor-pointer color-option" data-color="#ffffff"></div>
                            <div class="w-6 h-6 rounded-full bg-red-100 cursor-pointer color-option" data-color="#f28b82"></div>
                            <div class="w-6 h-6 rounded-full bg-orange-100 cursor-pointer color-option" data-color="#fbbc04"></div>
                            <div class="w-6 h-6 rounded-full bg-yellow-100 cursor-pointer color-option" data-color="#fff475"></div>
                            <div class="w-6 h-6 rounded-full bg-green-100 cursor-pointer color-option" data-color="#ccff90"></div>
                            <div class="w-6 h-6 rounded-full bg-blue-100 cursor-pointer color-option" data-color="#cbf0f8"></div>
                        </div>
                    </div>
                    <button type="button" id="closeCreateBoxBtn" class="px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded">Close</button>
                </div>
            </div>
        </div>

        <!-- Notes Grid -->
        <div class="notes-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <!-- Dynamically rendered note cards will appear here -->
        </div>
    </main>

    <!-- Edit Note Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
        <div id="editModalContent" class="bg-white w-full max-w-lg rounded-xl p-4 shadow-xl border border-gray-200 transition-all">
            <input type="text" id="editTitle" placeholder="Title" class="w-full font-medium text-gray-800 bg-transparent focus:outline-none mb-2 text-base">
            <textarea id="editBody" placeholder="Take a note..." rows="4" class="w-full text-sm text-gray-700 bg-transparent focus:outline-none resize-none"></textarea>
            
            <div id="editChecklistContainer" class="space-y-1 mb-2 hidden"></div>
            <button type="button" id="editAddChecklistItemBtn" class="text-xs text-amber-600 hover:underline mb-2 hidden">+ Add item</button>

            <div class="flex items-center justify-between mt-4 pt-2 border-t border-gray-100">
                <div class="flex items-center space-x-1 relative">
                    <button type="button" id="editColorPaletteBtn" class="p-1.5 hover:bg-black hover:bg-opacity-5 rounded-full text-gray-600" title="Background options">
                        <span class="material-icons-outlined text-sm">palette</span>
                    </button>
                    <!-- Edit Color Dropdown -->
                    <div id="editColorPaletteDropdown" class="hidden absolute left-0 bottom-full mb-2 bg-white border border-gray-200 shadow-lg rounded-lg p-2 flex space-x-2 z-20">
                        <div class="w-6 h-6 rounded-full bg-white border border-gray-300 cursor-pointer edit-color-option" data-color="#ffffff"></div>
                        <div class="w-6 h-6 rounded-full bg-red-100 cursor-pointer edit-color-option" data-color="#f28b82"></div>
                        <div class="w-6 h-6 rounded-full bg-orange-100 cursor-pointer edit-color-option" data-color="#fbbc04"></div>
                        <div class="w-6 h-6 rounded-full bg-yellow-100 cursor-pointer edit-color-option" data-color="#fff475"></div>
                        <div class="w-6 h-6 rounded-full bg-green-100 cursor-pointer edit-color-option" data-color="#ccff90"></div>
                        <div class="w-6 h-6 rounded-full bg-blue-100 cursor-pointer edit-color-option" data-color="#cbf0f8"></div>
                    </div>
                    <button type="button" id="deleteNoteBtn" class="p-1.5 hover:bg-black hover:bg-opacity-5 rounded-full text-gray-600" title="Delete note">
                        <span class="material-icons-outlined text-sm">delete</span>
                    </button>
                </div>
                <button type="button" id="closeEditModalBtn" class="px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded">Done</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>