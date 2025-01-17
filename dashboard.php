<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">
        <div class="dashboard-container">
            <h1 class="title">Google Calendar Dashboard</h1>

            <button id="addEventBtn" class="btn add-btn" onclick="showAddEventModal()">+ Add Event</button>

            <div id="eventList" class="event-list"></div>
        </div>

        <div id="addEventModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddEventModal()">&times;</span>
                <h2>Add Event</h2>
                <form id="addEventForm">
                    <input type="text" id="summary" name="summary" placeholder="Event Summary" required>
                    <input type="text" id="description" name="description" placeholder="Description" required>
                    <input type="text" id="location" name="location" placeholder="Location">
                    <input type="datetime-local" id="startTime" name="startTime" required>
                    <input type="datetime-local" id="endTime" name="endTime" required>
                    <button class="btn submit-btn" type="submit">Create Event</button>
                </form>
            </div>
        </div>

        <div id="editEventModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditEventModal()">&times;</span>
                <h2>Edit Event</h2>
                <form id="editEventForm">
                    <input type="hidden" id="editEventId" name="eventId">
                    <input type="text" id="editSummary" name="summary" placeholder="Event Summary" required>
                    <input type="text" id="editLocation" name="location" placeholder="Location" required>
                    <textarea id="editDescription" name="description" placeholder="Description" required></textarea>
                    <input type="datetime-local" id="editStartTime" name="startTime" required>
                    <input type="datetime-local" id="editEndTime" name="endTime" required>
                    <button class="btn submit-btn" type="submit">Save Changes</button>
                </form>
                <button class="btn cancel-btn" onclick="closeEditEventModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>