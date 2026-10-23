<form method="POST" action="backend/project_code.php">
    <div class="form-row">
        <input type="text" name="title" placeholder="Project Title" required>
        <select name="status" required>
            <option value="" disabled selected>Select Status...</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Completed">Completed</option>
            <option value="Archived">Archived</option>
        </select>
    </div>
    <div class="form-row">
        <input type="text" name="description" placeholder="Short Description" required>
    </div>
    <button type="submit" name="add_project" class="btn-submit">Initialize Project</button>
</form>