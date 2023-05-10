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
            <select class="form-control" name="role" id="role_selector">
                <option value="all">All</option>
                <option value="stylist">Stylist</option>
                <option value="art_director">Art director</option>
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
                let selectedRole = $('#role_selector').val();
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
                        let eventsUrl = '/getEvents?role=' + selectedRole;

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
                    // eventRender: function eventRender(event, element, view) {
                    //     return ['all', $('#role_selector').val()].indexOf(event.title.split(' ')[0]) >= 0
                    // }

                });

                calendar.render();
                $('#role_selector').on('change', function() {
                    selectedRole = $(this).val();
                    calendar.refetchEvents();

                    console.log('changed', selectedRole);

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
