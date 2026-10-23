<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/classes/ResearchProject.php';

$project = new ResearchProject($db);


$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
if ($search_keyword) {
    $stmt = $project->searchResearchProjects($search_keyword);
} else {
    $stmt = $project->getResearchProjects();
}

$all_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$edit_state = false;
$edit_id = 0;
$edit_title = '';
$edit_desc = '';
$edit_status = '';

if (isset($_GET['edit'])) {
    $edit_state = true;
    $edit_id = $_GET['edit'];

    foreach ($all_projects as $p) {
        if ($p['ProjectID'] == $edit_id) {
            $edit_title = $p['ProjectTitle'];
            $edit_desc = $p['Description'];
            $edit_status = $p['Status'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResearchEase Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #3f1278;
            --sidebar-active: rgba(255, 255, 255, 0.16);
            --hero-start: #6a1fc2;
            --hero-end: #a45ae6;
            --accent: #7a2fd1;
            --accent-hover: #8f45e6;
            --page-bg: #f5f2fa;
            --card-bg: #ffffff;
            --text-dark: #26183d;
            --text-muted: #7b6f91;
            --text-light: #f5eeff;
            --border-color: #e4dcf0;
            --danger: #d6246e;
            --border-radius: 18px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, sans-serif;
            background-color: var(--page-bg);
            color: var(--text-dark);
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: 240px;
            flex-shrink: 0;
            background-color: var(--sidebar-bg);
            padding: 28px 18px;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-light);
        }
        .brand img { width: 48px; height: 48px; object-fit: contain; filter: drop-shadow(0 0 6px rgba(255,255,255,0.9)); }

        .nav-links { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .nav-links li {
            padding: 12px 16px;
            border-radius: 10px;
            color: #e3d2fb;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .nav-links li.active,
        .nav-links li:hover { background-color: var(--sidebar-active); color: #fff; }

        /* ---------- Main area ---------- */
        .main-wrapper { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }

        .hero {
            background: linear-gradient(120deg, var(--hero-start) 0%, var(--hero-end) 100%);
            padding: 44px 48px 76px;
            color: #fff;
        }
        .hero h1 { margin: 0 0 8px; font-size: 30px; font-weight: 600; }
        .hero p { margin: 0 0 26px; color: #eadbff; font-size: 15px; max-width: 520px; }

        .search-form { display: flex; gap: 12px; max-width: 680px; margin: 0; }
        .search-form input[type="text"] {
            flex: 1;
            background: #fff;
            border: none;
            color: var(--text-dark);
            padding: 14px 22px;
            border-radius: 30px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }
        .search-form input[type="text"]:focus { box-shadow: 0 0 0 3px rgba(255,255,255,0.45); }

        .btn-search {
            background: var(--sidebar-bg);
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 30px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-search:hover { background: #3f2a63; }

        .btn-clear {
            color: #fff;
            text-decoration: none;
            padding: 14px 22px;
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 30px;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-clear:hover { background: rgba(255,255,255,0.15); }

        .content-grid {
            display: flex;
            flex-direction: column;
            gap: 28px;
            padding: 0 48px 48px;
            margin-top: -44px; /* cards overlap the purple banner */
        }

        .card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 30px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(74, 20, 140, 0.10);
        }

        .card-title { margin: 0 0 20px; font-size: 18px; font-weight: 600; color: var(--text-dark); }

        .card-subtitle {
            font-size: 17px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 18px;
            padding-left: 12px;
            border-left: 4px solid var(--accent);
        }

        /* ---------- Forms (also used by modals/*.php) ---------- */
        .form-row { display: flex; gap: 18px; margin-bottom: 18px; align-items: center; }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            flex: 1;
            background-color: #faf8fd;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 13px 18px;
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(122, 47, 209, 0.15);
        }

        .btn-submit {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 13px 30px;
            border-radius: 30px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-block;
        }
        .btn-submit:hover { background: var(--accent-hover); }

        .btn-update { background: #00a6c7; }
        .btn-update:hover { background: #12bde0; }

        /* ---------- Table ---------- */
        table { width: 100%; border-collapse: collapse; }
        th {
            color: #fff;
            background: var(--accent);
            font-weight: 500;
            text-align: left;
            padding: 14px 16px;
            font-size: 13px;
        }
        th:first-child { border-radius: 12px 0 0 12px; }
        th:last-child { border-radius: 0 12px 12px 0; }
        td { padding: 14px 16px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
        tr:hover td { background-color: #f8f4fd; }

        .status-badge {
            background: #efe4fb;
            color: var(--accent);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .action-link { text-decoration: none; font-weight: 600; font-size: 13px; }
        .action-edit { color: var(--accent); margin-right: 14px; }
        .action-edit:hover { text-decoration: underline; }
        .action-delete { color: var(--danger); }
        .action-delete:hover { text-decoration: underline; }

        .user-box {
            margin-top: -12;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 14px;
            color: #ffffff;
        }
        .user-box span { font-weight: 500; }
        .user-box a {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
            padding: 9px 14px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }
        .user-box a:hover { background: rgba(255, 255, 255, 0.28); }

        @media (max-width: 800px) {
            body { flex-direction: column; height: auto; overflow: auto; }
            .sidebar { width: 100%; padding: 18px; gap: 14px; }
            .hero { padding: 30px 20px 64px; }
            .content-grid { padding: 0 20px 30px; }
            .search-form, .form-row { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="brand">
            <img src="logo.png" alt="Logo">
            ResearchEase
        </div>
        <ul class="nav-links">
            <li class="active">Dashboard</li>
        </ul>

        <div class="user-box">
            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php">Sign out</a>
        </div>
    </aside>

    <main class="main-wrapper">

        <header class="hero">
            <h1>Project Management</h1>
            <p>Manage and track your research data in one place.</p>
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="Search projects by title, description, or status..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                <button type="submit" class="btn-search">Search</button>
                <a href="index.php" class="btn-clear">Clear</a>
            </form>
        </header>

        <div class="content-grid">

            <div class="card">
                <?php 
                    if ($edit_state) {
                        echo '<div class="card-subtitle">Update project details</div>';
                        include 'modals/updateProject.php';
                    } elseif (isset($_GET['delete'])) {
                        echo '<div class="card-subtitle" style="color: var(--danger); border-color: var(--danger);">Delete project</div>';
                        include 'modals/deleteProject.php';
                    } else {
                        echo '<div class="card-subtitle">Add new project</div>';
                        include 'modals/insertProject.php';
                    }
                ?>
            </div>

            <div class="card">
                <h3 class="card-title">Project Database</h3>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($all_projects) > 0) {
                            foreach ($all_projects as $row) {
                                echo "<tr>";
                                echo "<td>{$row['ProjectID']}</td>";
                                echo "<td><strong>{$row['ProjectTitle']}</strong></td>";
                                echo "<td><span style='color: var(--text-muted);'>{$row['Description']}</span></td>";
                                echo "<td><span class='status-badge'>{$row['Status']}</span></td>";
                                echo "<td>{$row['DateCreated']}</td>";
                                echo "<td>
                                    <a class='action-link action-edit' href='index.php?edit={$row['ProjectID']}'>Edit</a>
                                    <a class='action-link action-delete' href='index.php?delete={$row['ProjectID']}'>Delete</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center; color: var(--text-muted); padding: 30px;'>No projects found in database.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>