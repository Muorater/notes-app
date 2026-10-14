 <?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $color = $_POST['color'] ?? 'default';

    if (!empty($title) || !empty($content)) {
        $stmt = $pdo->prepare(
            "INSERT INTO notes (title, content, color) VALUES (?, ?, ?)"
        );

        $stmt->execute([$title, $content, $color]);
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Note</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background: #ffffff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: background 0.3s;
        }

        header {
            display: flex;
            justify-content: space-between;
            padding: 12px 16px;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #333;
            text-decoration: none;
            padding: 8px;
            border-radius: 50%;
        }

        .editor {
            flex: 1;
            padding: 16px 24px;
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .title-input {
            font-size: 22px;
            font-weight: 500;
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
        }

        .body-input {
            font-size: 16px;
            border: none;
            outline: none;
            resize: none;
            flex: 1;
            min-height: 250px;
            background: transparent;
            width: 100%;
        }

        footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 24px;
            border-top: 1px solid #f1f3f4;
            background: inherit;
        }

        .color-picker {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .color-btn {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .create-btn {
            background: #1a73e8;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        .create-btn:hover {
            background: #1557b0;
        }

        @media (max-width: 480px) {
            .editor {
                padding: 16px;
            }

            footer {
                padding: 12px 16px;
            }
        }
    </style>
</head>

<body id="bodyTag">

    <form
        action="create.php"
        method="POST"
        style="display: flex; flex-direction: column; flex: 1;"
        id="noteForm"
    >

        <header>
            <a href="index.php" class="icon-btn" aria-label="Go back">
                <span class="material-icons-outlined">
                    arrow_back
                </span>
            </a>
        </header>

        <main class="editor">

            <input
                type="text"
                name="title"
                class="title-input"
                placeholder="Title"
                aria-label="Note title"
            >

            <textarea
                name="content"
                class="body-input"
                placeholder="Note"
                aria-label="Note content"
            ></textarea>

            <input
                type="hidden"
                name="color"
                id="colorInput"
                value="default"
            >

        </main>

        <footer>

            <div class="color-picker">
                <span style="font-size: 12px; color: #666;">
                    Color:
                </span>

                <button
                    type="button"
                    class="color-btn"
                    style="background: #ffffff;"
                    aria-label="White background"
                    onclick="setTheme('default', '#ffffff')"
                ></button>

                <button
                    type="button"
                    class="color-btn"
                    style="background: #cce8e6;"
                    aria-label="Green background"
                    onclick="setTheme('green', '#cce8e6')"
                ></button>

                <button
                    type="button"
                    class="color-btn"
                    style="background: #fadcce;"
                    aria-label="Orange background"
                    onclick="setTheme('orange', '#fadcce')"
                ></button>
            </div>

            <button type="submit" class="create-btn">
                Create
            </button>

        </footer>

    </form>

    <script>
        function setTheme(colorName, hexCode) {
            document.getElementById('colorInput').value = colorName;

            document.getElementById('bodyTag').style.backgroundColor =
                hexCode;
        }
    </script>

</body>
</html>