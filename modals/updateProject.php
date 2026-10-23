<form method="POST" action="backend/project_code.php">
    <div class="form-row">
        <input type="text" name="title" placeholder="Project Title" value="<?php echo htmlspecialchars($edit_title); ?>" required>
        <select name="status" required>
            <option value="Ongoing" <?php echo ($edit_status == 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
            <option value="Completed" <?php echo ($edit_status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
            <option value="Archived" <?php echo ($edit_status == 'Archived') ? 'selected' : ''; ?>>Archived</option>
        </select>
    </div>
    <div class="form-row">
        <input type="text" name="description" placeholder="Short Description" value="<?php echo htmlspecialchars($edit_desc); ?>" required>
    </div>
    <input type="hidden" name="project_id" value="<?php echo $edit_id; ?>">
    <button type="submit" name="update_project" class="btn-submit btn-update">Update Project</button>
    <a href="index.php" class="btn-clear" style="margin-left: 10px;">Cancel Edit</a>
</form>