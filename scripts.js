// Fetch events when the page loads
$(document).ready(function() {
        fetchEvents();
    });

    // Show Add Event Modal
        function showAddEventModal() {
            $('#addEventModal').addClass('modal-open');
        }

    // Close Add Event Modal
        function closeAddEventModal() {
            $('#addEventModal').removeClass('modal-open');
        }

// Function to format datetime string for datetime-local input
function convertToFullDateTimeWithTimezone(dateTimeLocal, timeZone) {
    let date = new Date(dateTimeLocal);
    let options = { timeZone: timeZone, hour12: false, weekday: "short", year: "numeric", month: "short", day: "numeric" };
    let formattedDate = date.toLocaleString("en-US", options); 

        return new Date(date).toISOString();
    }


// Show Edit Event Modal
function showEditEventModal(eventDetails) {
    // Pre-fill the Edit Event Form with current event details
    $('#editEventId').val(eventDetails.id);
    $('#editSummary').val(eventDetails.summary);
    $('#editLocation').val(eventDetails.location);
    $('#editDescription').val(eventDetails.description);
    // Format start and end times for datetime-local input
    $('#editStartTime').val(eventDetails.start.dateTime.slice(0, 16));
    $('#editEndTime').val(eventDetails.end.dateTime.slice(0, 16)); 

    // Show the Edit Event Modal
    $('#editEventModal').addClass('modal-open');
}

    // Close Edit Event Modal
        function closeEditEventModal() {
            $('#editEventModal').removeClass('modal-open');
        }

// Function to fetch events and format the start and end times
function fetchEvents() {
    $.ajax({
        url: 'backend.php',
        method: 'POST',
        data: { action: 'getEvents' },
        success: function(response) {
            const events = JSON.parse(response);
            let eventListHtml = '';

            events.forEach(event => {
                // Format the start and end times
                const formattedStart = formatDate(event.start);
                const formattedEnd = formatDate(event.end);

                eventListHtml += `
                    <div class="event-item">
                        <h3>${event.summary}</h3>
                        <p><strong>Description:</strong> ${event.description}</p>
                        <p><strong>Location:</strong> ${event.location}</p>
                        <p><strong>Start:</strong> ${formattedStart}</p>
                        <p><strong>End:</strong> ${formattedEnd}</p>
                        <button onclick="editEvent('${event.id}')"><i class="fas fa-edit"></i> Edit</button>
                        <button onclick="deleteEvent('${event.id}')"><i class="fas fa-trash-alt"></i> Delete</button>
                    </div>
                `;
            });

            $('#eventList').html(eventListHtml);
        }
    });
}

// Function to format the date-time string into a more readable format without time zone
function formatDate(dateString) {
    const options = {
        weekday: 'short',  
        year: 'numeric',
        month: 'short',    
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
      
    };

    const date = new Date(dateString);
    return date.toLocaleString('en-US', options);
}


// Handle Add Event Form Submission
$('#addEventForm').on('submit', function(event) {
    event.preventDefault();

    const formData = {
        summary: $('#summary').val(),
        description: $('#description').val(),
        location: $('#location').val(),
        startTime: convertToUTC($('#startTime').val()), // Convert to UTC time
        endTime: convertToUTC($('#endTime').val()) // Convert to UTC time
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


function convertToUTC(localTime) {
    let date = new Date(localTime);
    return date.toISOString(); // Converts to UTC ISO string
}

// Edit Event
function editEvent(eventId) {
    $.ajax({
        url: 'backend.php',
        method: 'POST',
        data: { action: 'getEventDetails', eventId: eventId },
        success: function(response) {
            const eventDetails = JSON.parse(response);
            showEditEventModal(eventDetails);
        }
    });
}

// Edit event functionality
$('#editEventForm').on('submit', function(event) {
    event.preventDefault();

    const formData = {
        eventId: $('#editEventId').val(),
        summary: $('#editSummary').val(),
        description: $('#editDescription').val(),
        location: $('#editLocation').val(),
        startTime: convertToUTC($('#editStartTime').val()), // Convert to UTC
        endTime: convertToUTC($('#editEndTime').val()) // Convert to UTC
    };

    $.ajax({
        url: 'backend.php',
        method: 'POST',
        data: {
            action: 'editEvent',
            data: JSON.stringify(formData)
        },
        success: function(response) {
            fetchEvents(); // Refresh the event list
            closeEditEventModal(); // Close modal
        }
    });
});

// Delete Event (POST method)
function deleteEvent(eventId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this event?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'backend.php',
                method: 'POST',
                data: { action: 'deleteEvent', eventId: eventId },
                success: function(response) {
                    fetchEvents();
                }
            });
            Swal.fire(
                'Deleted!',
                'The event has been deleted.',
                'success'
            );
        } else {
            Swal.fire(
                'Cancelled',
                'The event was not deleted.',
                'error'
            );
        }
    });
}

// Close modals if clicked outside modal content
$(document).click(function(event) {
    if ($(event.target).is('#addEventModal')) {
        closeAddEventModal();
    }
    if ($(event.target).is('#editEventModal')) {
        closeEditEventModal();
    }
});