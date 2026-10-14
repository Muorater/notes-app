<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Keep Clone</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="min-h-screen flex flex-col bg-white">

    <!-- Top Navigation Bar -->
    <header class="flex items-center justify-between px-4 py-2 border-b border-gray-200 sticky top-0 bg-white z-40 h-16 shadow-xs">
        <div class="flex items-center space-x-3">
            <button class="p-2.5 rounded-full hover:bg-gray-100 text-gray-700 transition-colors cursor-pointer" title="Main menu">
                <span class="material-icons-outlined">menu</span>
            </button>
            <div class="flex items-center space-x-2 text-xl font-medium text-gray-700 cursor-pointer">
                <span class="material-icons-outlined text-amber-500 text-3xl">lightbulb</span>
                <span class="tracking-tight text-gray-600">Keep</span>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="flex-1 max-w-2xl mx-4">
            <div class="flex items-center bg-[#f1f3f4] hover:bg-[#e8eaed] focus-within:bg-white focus-within:shadow-md rounded-lg px-3 py-2.5 transition-all duration-200 border border-transparent focus-within:border-gray-200">
                <span class="material-icons-outlined text-gray-500 mr-3">search</span>
                <input type="text" id="searchInput" placeholder="Search Keep" class="bg-transparent border-none outline-none w-full text-sm text-gray-800 placeholder-gray-500">
                <button id="clearSearch" class="material-icons-outlined text-gray-400 hover:text-gray-600 text-sm hidden cursor-pointer">close</button>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center space-x-1 sm:space-x-2">
            <button id="viewToggleBtn" class="p-2.5 rounded-full hover:bg-gray-100 text-gray-700 transition-colors cursor-pointer" title="Toggle view">
                <span class="material-icons-outlined" id="viewIcon">view_agenda</span>
            </button>
            <button class="p-2.5 rounded-full hover:bg-gray-100 text-gray-700 transition-colors cursor-pointer" title="Refresh">
                <span class="material-icons-outlined">swap_vert</span>
            </button>
            <div class="w-9 h-9 rounded-full bg-purple-600 text-white font-medium flex items-center justify-center text-sm shadow-sm cursor-pointer ml-1 select-none">M</div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-8 mb-24">
        
        <!-- Top Create Note Expansion Box -->
        <div class="max-w-xl mx-auto mb-10 bg-white border border-gray-300 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200" id="createNoteCard">
            <div class="p-3 flex items-center justify-between cursor-pointer" id="createNoteTrigger">
                <span class="text-gray-500 text-sm font-medium pl-2">Take a note...</span>
                <div class="flex items-center space-x-1">
                    <button class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition-colors cursor-pointer" title="New list" id="quickListBtn">
                        <span class="material-icons-outlined">check_box</span>
                    </button>
                    <button class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition-colors cursor-pointer" title="New note with image">
                        <span class="material-icons-outlined">image</span>
                    </button>
                </div>
            </div>

            <!-- Expanded Creator Form -->
            <div id="expandedForm" class="p-3 flex flex-col space-y-3 hidden">
                <input type="text" id="newTitleInput" placeholder="Title" class="w-full border-none outline-none font-medium text-gray-800 px-2 py-1 placeholder-gray-500 bg-transparent">
                <textarea id="newContentInput" placeholder="Take a note..." rows="3" class="w-full border-none outline-none text-sm text-gray-800 px-2 py-1 resize-none placeholder-gray-500 bg-transparent"></textarea>
                
                <!-- Creator Checklist Container -->
                <div id="creatorChecklistContainer" class="space-y-2 px-2 hidden"></div>
                <div id="addListItemBtnContainer" class="hidden px-2">
                    <button type="button" id="addListItemInputBtn" class="text-xs text-blue-600 font-medium hover:underline flex items-center gap-1 cursor-pointer">
                        <span class="material-icons-outlined text-sm">add</span> Add list item
                    </button>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 px-1">
                    <div class="flex items-center space-x-1 text-gray-600">
                        <button class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Remind me"><span class="material-icons-outlined text-lg">add_alert</span></button>
                        <button class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Collaborator"><span class="material-icons-outlined text-lg">person_add</span></button>
                        <button class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Change color"><span class="material-icons-outlined text-lg">palette</span></button>
                        <button class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Add image"><span class="material-icons-outlined text-lg">image</span></button>
                        <button class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Archive"><span class="material-icons-outlined text-lg">archive</span></button>
                        <button id="toggleNoteTypeBtn" class="p-1.5 rounded-full hover:bg-gray-100 cursor-pointer" title="Toggle checklist"><span class="material-icons-outlined text-lg">checklist</span></button>
                    </div>
                    <button id="closeNoteFormBtn" class="px-5 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded cursor-pointer transition-colors">Close</button>
                </div>
            </div>
        </div>

        <!-- Notes Grid/List Container -->
        <div id="notesContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden flex flex-col items-center justify-center py-20 text-gray-400">
            <span class="material-icons-outlined text-7xl mb-4 text-gray-300">lightbulb_outline</span>
            <p class="text-lg">Notes you add appear here</p>
        </div>
    </main>

    <!-- Floating Action "Take a note..." / Create Note Section at Bottom-Right -->
    <div class="fixed bottom-6 right-6 z-30">
        <button id="floatingCreateBtn" class="bg-amber-400 hover:bg-amber-500 text-gray-900 font-medium px-5 py-3 rounded-2xl shadow-xl flex items-center space-x-2 transition-all transform active:scale-95 cursor-pointer border border-amber-300">
            <span class="material-icons-outlined">add</span>
            <span>Take a note...</span>
        </button>
    </div>

    <!-- Edit Note Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
        <div id="editModalContent" class="bg-white w-full max-w-lg rounded-2xl p-6 shadow-2xl relative transition-colors duration-200 flex flex-col space-y-4">
            <input type="text" id="editTitleInput" placeholder="Title" class="w-full border-none outline-none font-medium text-xl text-gray-900 bg-transparent placeholder-gray-500">
            
            <div id="editBodyContainer">
                <textarea id="editContentInput" placeholder="Take a note..." rows="5" class="w-full border-none outline-none text-sm text-gray-800 resize-none bg-transparent placeholder-gray-500"></textarea>
                <div id="editChecklistContainer" class="space-y-2 max-h-60 overflow-y-auto hidden my-2"></div>
                <button type="button" id="editAddListItemBtn" class="text-xs text-blue-600 font-medium hover:underline flex items-center gap-1 mt-2 hidden cursor-pointer">
                    <span class="material-icons-outlined text-sm">add</span> Add list item
                </button>
            </div>

            <!-- Labels / Tags view -->
            <div class="flex flex-wrap gap-2" id="editLabelsContainer"></div>

            <!-- Modal Bottom Toolbar -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-200/60">
                <div class="flex items-center space-x-2 text-gray-600 relative">
                    <button class="p-1.5 rounded-full hover:bg-black/5 cursor-pointer" title="Remind me"><span class="material-icons-outlined text-lg">add_alert</span></button>
                    <button class="p-1.5 rounded-full hover:bg-black/5 cursor-pointer" title="Collaborator"><span class="material-icons-outlined text-lg">person_add</span></button>
                    
                    <!-- Color Palette Trigger -->
                    <div class="relative">
                        <button id="editPaletteBtn" class="p-1.5 rounded-full hover:bg-black/5 cursor-pointer" title="Background color"><span class="material-icons-outlined text-lg">palette</span></button>
                        <!-- Color Picker Popup -->
                        <div id="colorPickerPopup" class="absolute bottom-10 left-0 bg-white shadow-xl rounded-xl p-2 grid grid-cols-4 gap-2 border border-gray-200 hidden z-50">
                            <div onclick="setEditColor('bg-white')" class="w-6 h-6 rounded-full bg-white border border-gray-300 cursor-pointer hover:scale-110 transition-transform"></div>
                            <div onclick="setEditColor('bg-red-100')" class="w-6 h-6 rounded-full bg-red-100 cursor-pointer hover:scale-110 transition-transform"></div>
                            <div onclick="setEditColor('bg-orange-100')" class="w-6 h-6 rounded-full bg-orange-100 cursor-pointer hover:scale-110 transition-transform"></div>
                            <div onclick="setEditColor('bg-amber-100')" class="w-6 h-6 rounded-full bg-amber-100 cursor-pointer hover:scale-110 transition-transform"></div>
                            <div onclick="setEditColor('bg-yellow-100')" class="w-6 h-6 rounded-full bg-yellow-100 cursor-pointer hover:scale-110 transition-transform"></div>
                            <div onclick="setEditColor('bg-green-100')" class="w-6 h-6 rounded-full bg-green-100 cursor-pointer hover:scale-110 transition-transform"></div>
                            <div onclick="setEditColor('bg-cyan-100')" class="w-6 h-6 rounded-full bg-cyan-100 cursor-pointer hover:scale-110 transition-transform"></div>
                            <div onclick="setEditColor('bg-purple-100')" class="w-6 h-6 rounded-full bg-purple-100 cursor-pointer hover:scale-110 transition-transform"></div>
                        </div>
                    </div>

                    <button class="p-1.5 rounded-full hover:bg-black/5 cursor-pointer" title="Add image"><span class="material-icons-outlined text-lg">image</span></button>
                    <button class="p-1.5 rounded-full hover:bg-black/5 cursor-pointer" title="Archive"><span class="material-icons-outlined text-lg">archive</span></button>
                </div>
                <button id="closeEditModalBtn" class="px-6 py-2 text-sm font-medium text-gray-800 hover:bg-black/5 rounded-lg cursor-pointer transition-colors">Close</button>
            </div>
        </div>
    </div>
</body>
<script src="script.js"></script>
</html>