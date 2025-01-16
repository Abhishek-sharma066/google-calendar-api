<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add custom CSS here -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Add jQuery -->
</head>
<body>
    <div class="container">
        <h1>Google Calendar Dashboard</h1>

        <!-- Button to Open Add Event Modal -->
        <button id="addEventBtn" onclick="showAddEventModal()">Add Event</button>

        <!-- Event List -->
        <div id="eventList">
            <!-- Events will be dynamically loaded here -->
        </div>
    </div>

    <!-- Add Event Modal -->
    <div id="addEventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddEventModal()">&times;</span>
            <h2>Add Event</h2>
            <form id="addEventForm">
                <label for="summary">Event Summary</label>
                <input type="text" id="summary" name="summary" required>
                <label for="description">Description</label>
                <input type="text" id="description" name="description" required>
                <label for="location">Location</label>
                <input type="text" id="location" name="location">
                <label for="startTime">Start Time</label>
                <input type="datetime-local" id="startTime" name="startTime" required>
                <label for="endTime">End Time</label>
                <input type="datetime-local" id="endTime" name="endTime" required>
                <button type="submit">Create Event</button>
            </form>
        </div>
    </div>

    <div id="editEventModal" style="display: none;" class="modal">
    
   
    <h2>Edit Event</h2>
    <form id="editEventForm">
        <input type="hidden" id="editEventId" name="eventId"> <!-- Hidden field for event ID -->

        <label for="editSummary">Event Summary:</label><br>
        <input type="text" id="editSummary" name="summary" required><br><br>

        <label for="editLocation">Location:</label><br>
        <input type="text" id="editLocation" name="location" required><br><br>

        <label for="editDescription">Description:</label><br>
        <textarea id="editDescription" name="description" required></textarea><br><br>

        <label for="editStartTime">Start Time:</label><br>
        <input type="datetime-local" id="editStartTime" name="startTime" required><br><br>

        <label for="editEndTime">End Time:</label><br>
        <input type="datetime-local" id="editEndTime" name="endTime" required><br><br>

        <button type="submit">Save Changes</button>
    </form>
    <button onclick="closeEditEventModal()">Cancel</button>
    
    
</div>
    <script src="scripts.js"></script> <!-- Add custom JS here -->
</body>
</html>
