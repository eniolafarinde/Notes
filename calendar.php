<?php
// PHP setup (minimal, just for the sidebar consistency)
$selectedFolderId = null; // No folder selected on the calendar page

// Note: In a production app, you would load tags/events dynamically here.
$events = []; 
$tags = ['work', 'school', 'event', 'personal']; // Define available tags

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notes Calendar</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        /* Ensures the calendar fills the entire main panel */
        #calendar {
            max-width: 100%;
            height: 100%;
            padding: 20px;
        }
        /* Style adjustments for dark theme compatibility */
        .fc .fc-toolbar-title,
        .fc .fc-button-primary {
            color: white !important;
        }
        .fc-theme-standard td, .fc-theme-standard th {
            border: 1px solid #1f1f1f !important;
        }
        .fc-daygrid-body, .fc-scrollgrid-sync-table {
            background-color: #111; /* Dark background for the grid */
        }
        .fc .fc-daygrid-day.fc-day-today {
            background-color: #161616; /* Highlight today's date */
        }
        .fc-event {
            border-radius: 4px;
            font-size: 14px;
            /* Use a lighter border color for better contrast */
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
        }
        
        /* ================================== */
        /* --- CALENDAR TAG STYLES (REDUCED OPACITY) --- */
        /* Using rgba() for background transparency */
        /* ================================== */
        .tag-work { 
            background-color: rgba(255, 77, 0, 0.4) !important; /* #ff4d00 with 40% opacity */
            border-color: #ff4d00 !important; /* Keep original color for border if needed, or change to rgba as well */
        }
        .tag-school { 
            background-color: rgba(76, 175, 80, 0.4) !important; /* #4CAF50 with 40% opacity */
            border-color: #4CAF50 !important; 
        }
        .tag-event { 
            background-color: rgba(33, 150, 243, 0.4) !important; /* #2196F3 with 40% opacity */
            border-color: #2196F3 !important; 
        }
        .tag-personal { 
            background-color: rgba(156, 39, 176, 0.4) !important; /* #9C27B0 with 40% opacity */
            border-color: #9C27B0 !important; 
        }


        /* ================================== */
        /* --- MODAL STYLES (No changes needed for functionality) --- */
        /* ================================== */

        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.8); 
        }

        .modal-content {
            background-color: #1f1f1f;
            margin: 10% auto; 
            padding: 25px;
            border-radius: 10px;
            width: 400px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .close-btn {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: white;
            text-decoration: none;
        }

        .modal-body label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .modal-body input[type="text"],
        .modal-body input[type="datetime-local"],
        .modal-body select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #333;
            border-radius: 5px;
            background: #0d0d0d;
            color: white;
            box-sizing: border-box;
        }
        
        .modal-footer {
            padding-top: 15px;
            text-align: right;
        }

        .modal-footer button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        #saveEventBtn {
            background-color: #ff4d00;
            color: white;
        }
        
        #closeModalBtn {
            background-color: #333;
            color: white;
            margin-right: 10px;
        }

    </style>
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <h2 class="sidebar-title">Menu</h2>
        <ul class="nav">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="notes.php" class="nav-link">Notes</a></li>
            <li><a href="folders_page.php" class="nav-link">Folders</a></li>
            <li><a href="calendar.php" class="nav-link active-folder">Calendar</a></li>
        </ul>
    </aside>

    <div class="notes-panel" style="width: 100%;">
        <h2 class="panel-title">Your Calendar</h2>
        <div id='calendar'></div>
    </div>
</div>

<div id="eventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New Event</h3>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="eventForm">
                <input type="hidden" id="modal-start-date">
                <input type="hidden" id="modal-end-date">

                <label for="eventTitle">Title:</label>
                <input type="text" id="eventTitle" required>

                <label for="eventTag">Tag:</label>
                <select id="eventTag" required>
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= htmlspecialchars($tag) ?>"><?= ucfirst($tag) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="eventStart">Start Time:</label>
                <input type="datetime-local" id="eventStart" required>

                <label for="eventEnd">End Time:</label>
                <input type="datetime-local" id="eventEnd">
            </form>
        </div>
        <div class="modal-footer">
            <button id="closeModalBtn" onclick="closeModal()">Cancel</button>
            <button id="saveEventBtn">Save Event</button>
        </div>
    </div>
</div>


<script>
    let currentSelectionInfo = null;
    const modal = document.getElementById('eventModal');
    const saveBtn = document.getElementById('saveEventBtn');
    const form = document.getElementById('eventForm');
    
    // Helper to format date strings for the datetime-local input
    function formatDate(date) {
        let d = new Date(date);
        let year = d.getFullYear();
        let month = String(d.getMonth() + 1).padStart(2, '0');
        let day = String(d.getDate()).padStart(2, '0');
        let hours = String(d.getHours()).padStart(2, '0');
        let minutes = String(d.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function closeModal() {
        modal.style.display = 'none';
        form.reset(); // Clear the form fields
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            selectable: true,
            editable: true,
            events: 'load_events.php',
            
            // --- DATE SELECTION (Opens Modal) ---
            select: function(info) {
                // Store the selected date info globally
                currentSelectionInfo = info;

                // Pre-fill hidden date fields
                document.getElementById('modal-start-date').value = info.startStr;
                document.getElementById('modal-end-date').value = info.endStr;
                
                // Pre-fill datetime-local inputs
                let startDateTime = info.start;
                let endDateTime = info.end; 
                
                if (info.allDay) {
                    startDateTime.setHours(9, 0, 0); // 9:00 AM default
                    endDateTime = new Date(startDateTime.getTime() + 60 * 60 * 1000); 
                }

                document.getElementById('eventStart').value = formatDate(startDateTime);
                document.getElementById('eventEnd').value = formatDate(endDateTime);

                // Show the modal
                modal.style.display = 'block';
                calendar.unselect(); // Deselect the date range on the calendar
            },
            
            // Customize event rendering to apply tags
            eventClassNames: function(arg) {
                return ['tag-' + arg.event.extendedProps.tag];
            },

            // Handle event drag/drop or resize (to update dates on the server)
            eventChange: function(info) {
                 // updateEvent(info.event); // Function call remains here
                 console.log("Event updated via drag/resize:", info.event.title, info.event.startStr, info.event.endStr);
            }
        });
        calendar.render();


        // --- Save Button Click Handler ---
        saveBtn.onclick = function() {
            if (!form.reportValidity()) return; // Simple form validation

            const title = document.getElementById('eventTitle').value;
            const tag = document.getElementById('eventTag').value;
            const start = document.getElementById('eventStart').value;
            const end = document.getElementById('eventEnd').value;

            saveEvent({
                title: title,
                start: start,
                end: end,
                allDay: false, 
                tag: tag
            }, calendar);

            closeModal();
        };

        // --- Event Saving/Updating Functions (AJAX) ---
        function saveEvent(eventData, cal) {
            // Using Fetch API to send data to save_event.php
            const formData = new FormData();
            formData.append('title', eventData.title);
            formData.append('start', eventData.start);
            formData.append('end', eventData.end);
            formData.append('allDay', eventData.allDay);
            formData.append('tag', eventData.tag);
            
            fetch('save_event.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(newEvent => {
                console.log("Server saved event:", newEvent);
                // Add event to calendar with extendedProps and color from the server response
                cal.addEvent({
                    id: newEvent.id,
                    title: newEvent.title,
                    start: newEvent.start,
                    end: newEvent.end,
                    allDay: newEvent.allDay,
                    color: newEvent.color,
                    extendedProps: { tag: newEvent.tag }
                });
            })
            .catch(error => {
                console.error('Error saving event:', error);
                alert('Failed to save event.');
            });
        }
    });
</script>

</body>
</html>