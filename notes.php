<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keep Notes</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

</head>
<body>
    <!-- Top Navigation Bar -->
    <header>
        <div class="header-left">
            <button class="icon-btn">
                <span class="material-icons-outlined">menu</span>
            </button>
        </div>
        
        <div class="search-container">
            <span class="material-icons-outlined" style="color: #5e5e5e;">search</span>
            <input type="text" placeholder="Search Keep"[cite: 1]>
        </div>

        <div class="header-right">
            <button class="icon-btn">
                <span class="material-icons-outlined">view_agenda</span>
            </button>
            <button class="icon-btn">
                <span class="material-icons-outlined">swap_vert</span>
            </button>
            <div class="profile-avatar">M</div>
        </div>
    </header>

    <!-- Notes Grid Container -->
    <main>
        <div class="notes-grid">
            <!-- Note 1 -->
            <div class="note-card">
                <div class="note-title">Home page</div>
            </div>

            <!-- Note 2 -->
            <div class="note-card">
                <div class="note-title">To do items</div>
            </div>

            <!-- Note 3 -->
            <div class="note-card">
                <div class="note-text">forth</div>
            </div>

            <!-- Note 4 (Checklist) -->
            <div class="note-card">
                <div class="checklist-item">
                    <input type="checkbox" id="item1">
                    <label for="item1">Item1</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="item2">
                    <label for="item2">Item2</label>
                </div>
                <div class="checklist-item">
                    <input type="checkbox" id="item3">
                </div>
            </div>

            <!-- Note 5 -->
            <div class="note-card">
                <div class="note-title">Third</div>
            </div>

            <!-- Note 6 (Green) -->
            <div class="note-card green">
                <div class="note-text">second</div>
            </div>

            <!-- Note 7 (Orange) -->
            <div class="note-card orange">
                <div class="note-text">first</div>
            </div>
        </div>
    </main>

    <!-- Floating Action Button -->
    <div class="fab-container">
        <button class="fab">
            <span class="material-icons-outlined">add</span>
            Create a note
        </button>
    </div>

</body>
</html>