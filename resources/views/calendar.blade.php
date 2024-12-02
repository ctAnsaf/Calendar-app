@extends('layouts.app')

@section('content')
<div class="container-fluid position-relative vh-100 d-flex align-items-center justify-content-center" style="background-image: url('https://laravel.com/assets/img/welcome/background.svg'); background-size: cover; background-repeat: no-repeat; background-position: center;">

        <h1 class="text-center display-4 mb-4">Event Calendar</h1>
        <div id="calendar" class="bg-white p-4 rounded shadow w-100" style="max-width: 800px;">
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <input type="hidden" id="eventId">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" id="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" id="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="color" value="#3788d8">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="deleteEvent">Delete Event</button>
                    <button type="button" class="btn btn-primary" id="saveEvent">Save Event</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css" rel="stylesheet">
<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: '/api/events',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(data) {
                    successCallback(data.map(function(event) {
                        return {
                            id: event.id,
                            title: event.title,
                            description: event.description,
                            start: event.start_date,
                            end: event.end_date,
                            color: event.color
                        };
                    }));
                },
                error: function(xhr, status, error) {
                    failureCallback(error);
                }
            });
        },
        eventColor: '#378006',
        editable: true,
        dateClick: function(info) {
            openNewEventModal(info.date);
        },
        eventClick: function(info) {
            openEventModal(info.event);
        }
    });

    calendar.render();

    function openNewEventModal(date) {
        document.getElementById('eventId').value = '';
        document.getElementById('title').value = '';
        document.getElementById('description').value = '';
        
        let startDate = new Date(date);
        startDate.setHours(9, 0, 0);
        let endDate = new Date(date);
        endDate.setHours(10, 0, 0);

        document.getElementById('start_date').value = formatDateTimeLocal(startDate);
        document.getElementById('end_date').value = formatDateTimeLocal(endDate);
        document.getElementById('color').value = '#3788d8';
        
        eventModal.show();
    }

    function openEventModal(event) {
        document.getElementById('eventId').value = event.id;
        document.getElementById('title').value = event.title;
        document.getElementById('description').value = event.extendedProps.description || '';
        document.getElementById('start_date').value = formatDateTimeLocal(event.start);
        document.getElementById('end_date').value = formatDateTimeLocal(event.end || event.start);
        document.getElementById('color').value = event.backgroundColor;
        
        eventModal.show();
    }

    document.getElementById('saveEvent').addEventListener('click', function() {
        let eventId = document.getElementById('eventId').value;
        let url = eventId ? `/api/events/${eventId}` : '/api/events';
        let method = eventId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                start_date: document.getElementById('start_date').value,
                end_date: document.getElementById('end_date').value,
                color: document.getElementById('color').value,
                _token: csrfToken
            },
            success: function(response) {
                eventModal.hide();
                calendar.refetchEvents();
            },
            error: function(error) {
                alert('Error saving event: ' + (error.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    document.getElementById('deleteEvent').addEventListener('click', function() {
        let eventId = document.getElementById('eventId').value;
        if (!eventId) return;

        if (confirm('Are you sure you want to delete this event?')) {
            $.ajax({
                url: `/api/events/${eventId}`,
                method: 'DELETE',
                data: {
                    _token: csrfToken
                },
                success: function(response) {
                    eventModal.hide();
                    calendar.refetchEvents();
                },
                error: function(error) {
                    alert('Error deleting event: ' + (error.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    });

    function formatDateTimeLocal(date) {
        date = new Date(date);
        let year = date.getFullYear();
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let day = String(date.getDate()).padStart(2, '0');
        let hours = String(date.getHours()).padStart(2, '0');
        let minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
});
</script>
