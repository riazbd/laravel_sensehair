@extends(backpack_view('blank'))

<style>
    #calendar {
        width: 100%;
        margin: 0 auto;
    }

    .fc-scrollgrid-sync-table {
        /* width: 100vw !important; */
        height: 100% !important;
    }

    a.fc-event {
        background-color: #009B77;
        color: #fff !important;
        cursor:pointer;
    }

    a.fc-event:hover {
        color: #000 !important;
    }
</style>


@section('header')
    <section class="container-fluid d-print-none">

        <h2>
            <span class="text-capitalize">planner</span>
        </h2>
    </section>
@endsection

<style>
    .form-group {
        margin-bottom: 1rem;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    select.form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    select.form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>



@section('content')

    <div class="row">
        <div class="form-group">
            <label for="role_selector">Filter Server:</label>
            <select class="form-control" name="name" id="role_selector">
                <option value="all">All</option>
                @foreach ($users as $user)
                    <option value="{{$user->id}}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div id='calendar'></div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');

                // Create a select element for roles
                // var roleSelector = document.createElement('select');
                // roleSelector.id = 'role_selector';
                // roleSelector.innerHTML =
                //     '<option value="all">All roles</option><option value="stylist">Stylist</option><option value="art_director">Art director</option>';

                // // Append the select element to the body
                // document.body.appendChild(roleSelector);
                let selectedName = $('#role_selector').val();
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    initialView: 'dayGridMonth',
                    // events: {!! json_encode($events) !!},
                    // events: '/getEvents?role=' + selectedRole,
                    events: function(fetchInfo, successCallback, failureCallback) {
                        // Get the selected role from the dropdown
                        // let selectedRole = $('#role_selector').val();

                        // Build the event source URL with the selected role
                        let eventsUrl = '/getEvents?name=' + selectedName;

                        // Fetch events from the event source
                        $.ajax({
                            url: eventsUrl,
                            dataType: 'json',
                            success: function(events) {
                                successCallback(events);
                            },
                            error: function() {
                                failureCallback();
                            }
                        });
                    },
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },

                    eventClick: function (info) {
                        // Fetch event details via AJAX
                        $.ajax({
                            url: '/events/' + info.event.id,
                            type: 'GET',
                            success: function (response) {
                                // Populate modal with event details
                                $('#eventModalTitle').text(response.title);
                                $('#booking_id').html('<strong>Booking ID</strong/>: ' + response.id);
                                $('#customer_name').html('<strong>Customer Name</strong/>: ' + response.customer_name);
                                $('#customer_phone').html('<strong>Phone</strong/>: ' + response.customer_phone);
                                $('#booking_time').html('<strong>Booking Time</strong/>: ' + new Date(response.booking_time).toLocaleDateString('en-us', { weekday:"long", year:"numeric", month:"short", day:"numeric", hour: 'numeric', minute: 'numeric'}));
                                $('#booking_duration').html('<strong>Booking Duration</strong/>: ' + response.booking_duration);
                                $('#Payment_status').html('<strong>Payment Status</strong/>: ' + response.payment_status);
                                $('#booking_cancel').html('<a href="/cancel-booking/' + response.id + '" class="btn btn-sm btn-danger">Cancel Booking</a>')

                                // Show the modal
                                $('#eventModal').modal('show');
                                    console.log(response.id)
                                    console.log(response.customer_name)
                                    console.log(response.customer_phone)
                                    console.log(response.booking_time)
                                    console.log(response.booking_duration)
                                    console.log(response.payment_status)
                                // // Show the modal without disabling the backdrop
                                // $('#eventModal').modal({ backdrop: false });
                            },
                            error: function () {
                                // Handle error
                                console.log('Error occurred while fetching event details');
                            }
                        });
                    }
                    // eventRender: function eventRender(event, element, view) {
                    //     return ['all', $('#role_selector').val()].indexOf(event.title.split(' ')[0]) >= 0
                    // }

                });

                calendar.render();
                $('#role_selector').on('change', function() {
                    selectedName = $(this).val();
                    calendar.refetchEvents();

                    console.log('changed', selectedName);

                });


            });
        </script>

    </div>
@endsection


@section('after_styles')
    <link rel="stylesheet"
        href="{{ asset('packages/backpack/crud/css/crud.css') . '?v=' . config('backpack.base.cachebusting_string') }}">
    <link rel="stylesheet"
        href="{{ asset('packages/backpack/crud/css/show.css') . '?v=' . config('backpack.base.cachebusting_string') }}">
@endsection

@section('after_scripts')
    <script src="{{ asset('packages/backpack/crud/js/crud.js') . '?v=' . config('backpack.base.cachebusting_string') }}">
    </script>
    <script src="{{ asset('packages/backpack/crud/js/show.js') . '?v=' . config('backpack.base.cachebusting_string') }}">
    </script>
@endsection

<div id="eventModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <p id="booking_id"></p>
                <p id="customer_name"></p>
                <p id="customer_phone"></p>
                <p id="booking_time"></p>
                <p id="booking_duration"></p>
                <p id="Payment_status"></p>
                <p id="booking_cancel"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
