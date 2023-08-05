<form method="POST" action="/project-generator">
    @csrf
    <label for="project_name">Module Name:</label>
        <input type="text" name="project_name" id="project_name" required>
        <br>
        <label for="inputs">Input Names (comma-separated):</label>
        <input type="text" name="inputs" id="inputs" required>
        <br>

    <!-- Add other form fields for module selection, configurations, etc. -->

    <button type="submit">Generate Project</button>
</form>