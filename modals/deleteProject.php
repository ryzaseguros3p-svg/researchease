<form method="POST" action="backend/project_code.php">
    <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($_GET['delete']); ?>">
    
    <div style="margin-bottom: 25px;">
        <p style="color: var(--text-main); font-size: 16px; margin-bottom: 5px;">⚠️ Are you sure you want to delete this project?</p>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 0;">This action cannot be undone.</p>
    </div>

    <button type="submit" name="delete_project_confirm" class="btn-submit" style="background: linear-gradient(45deg, #ff004c, #c5003b); box-shadow: 0 5px 15px rgba(255, 0, 76, 0.4);">
        Yes, Delete Project
    </button>
    <a href="index.php" class="btn-clear" style="margin-left: 10px;">Cancel</a>
</form>