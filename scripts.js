// Fetch events when the page loads
$(document).ready(function() {
    fetchEvents();
});

// Show Add Event Modal
function showAddEventModal() {
    $('#addEventModal').show();
}

// Close Add Event Modal
function closeAddEventModal() {
    $('#addEventModal').hide();
}

// Show Edit Event Modal
function showEditEventModal(eventDetails) {
    // Pre-fill the Edit Event Form with current event details
    $('#editEventId').val(eventDetails.id);
    $('#editSummary').val(eventDetails.summary);
    $('#editLocation').val(eventDetails.location);
    $('#editDescription').val(eventDetails.description);
    $('#editStartTime').val(eventDetails.start);
    $('#editEndTime').val(eventDetails.end);

    // Show the Edit Event Modal
    $('#editEventModal').show();
}

// Close Edit Event Modal
function closeEditEventModal() {
    $('#editEventModal').hide();
}

// Fetch events from backend using AJAX (POST method)
function fetchEvents() {
    $.ajax({
        url: 'backend.php',
        method: 'POST',
        data: { action: 'getEvents' },
        success: function(response) {
            const events = JSON.parse(response);
            let eventListHtml = '';

            events.forEach(event => {
                eventListHtml += `
                    <div class="event-item">
                        <h3>${event.summary}</h3>
                        <p><strong>Location:</strong> ${event.location}</p>
                        <p><strong>Start:</strong> ${event.start}</p>
                        <p><strong>End:</strong> ${event.end}</p>
                        <button onclick="editEvent('${event.id}')">Edit</button>
                        <button onclick="deleteEvent('${event.id}')">Delete</button>
                    </div>
                `;
            });

            $('#eventList').html(eventListHtml);
        }
    });
}

// Handle Add Event Form Submission (POST method)
$('#addEventForm').on('submit', function(event) {
    event.preventDefault();

    const formData = {
        summary: $('#summary').val(),
        description: $('#description').val(),
        location: $('#location').val(),
        startTime: $('#startTime').val(),
        endTime: $('#endTime').val()
    };

    $.ajax({
        url: 'backend.php',
        method: 'POST',
        data: {
            action: 'addEvent',
            data: JSON.stringify(formData)
        },
        success: function(response) {
            fetchEvents(); // Refresh the event list
            closeAddEventModal(); // Close modal
        }
    });
});

// Edit Event
function editEvent(eventId) {
    // Get the event details (this could be from your backend or an API call)
    $.ajax({
        url: 'backend.php',
        method: 'POST',
        data: { action: 'getEventDetails', eventId: eventId },
        success: function(response) {
            const eventDetails = JSON.parse(response);
            // Show the edit modal with pre-filled data
            showEditEventModal(eventDetails);
        }
    });
}



// Delete Event (POST method)
// function deleteEvent(eventId) {
//     $.ajax({
//         url: 'backend.php',
//         method: 'POST',
//         data: { action: 'deleteEvent', eventId: eventId },
//         success: function(response) {
//             fetchEvents(); 
//         }
//     });
// }