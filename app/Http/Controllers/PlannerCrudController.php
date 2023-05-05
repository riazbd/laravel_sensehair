<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use function PHPSTORM_META\type;

class PlannerCrudController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();

        $events = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->booking_time)->format('Y-m-d\TH:i:s');
            $end = Carbon::parse($booking->booking_time)->addMinutes($booking->duration)->format('Y-m-d\TH:i:s');
            // dd(optional($booking->server()->first())->getRoleNames()[0]);
            if (isset($booking)) {
                // Get the user object from the server relationship
                $user = $booking->server()->first();
                // Check if the user object is not null
                if ($user) {
                    // Get the role names of the user
                    $roleNames = $user->getRoleNames();
                    // Check if the role names array is not empty
                    if (!empty($roleNames)) {
                        // Get the first role name
                        $firstRoleName = $roleNames[0];
                    } else {
                        // Provide a default value for the first role name
                        $firstRoleName = 'No role';
                    }
                } else {
                    // Provide a default value for the first role name
                    $firstRoleName = 'No user';
                }
            } else {
                // Provide a default value for the first role name
                $firstRoleName = 'No booking';
            }

            // Use the first role name as needed
            //   echo $firstRoleName;
            // $server_roles = is_array((array) $booking->server()->exists()->first()->getRoleNames()) ? (array) $booking->server()->first()->getRoleNames()[0] : '';
            // $server_role =  $server_roles[0];
            // dd($server_roles[0]);
            $events[] = [
                'title' => ucfirst(str_replace('_', " ", $firstRoleName))  . ' ' . optional($booking->server()->first())->name . ' to ' . optional($booking->customer()->first())->name,
                'start' => $start,
                'end' => $end,
                "backgroundColor" => '#EFC050'
            ];
        }
        return view('planner', compact('events'));
    }

    public function getEvents()
    {
        $selectedRole = request('role');
        if ($selectedRole == 'all') {
            $bookings = Booking::all();
        } else {
            $bookings = Booking::whereHas('server', function ($query) use ($selectedRole) {
                $query->role($selectedRole);
            })->get();
        }

        // dd($bookings);
        $events = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->booking_time)->format('Y-m-d\TH:i:s');
            $end = Carbon::parse($booking->booking_time)->addMinutes($booking->duration)->format('Y-m-d\TH:i:s');
            // dd(optional($booking->server()->first())->getRoleNames()[0]);
            if (isset($booking)) {
                // Get the user object from the server relationship
                $user = $booking->server()->first();
                // Check if the user object is not null
                if ($user) {
                    // Get the role names of the user
                    $roleNames = $user->getRoleNames();
                    // Check if the role names array is not empty
                    if (!empty($roleNames)) {
                        // Get the first role name
                        $firstRoleName = $roleNames[0];
                    } else {
                        // Provide a default value for the first role name
                        $firstRoleName = 'No role';
                    }
                } else {
                    // Provide a default value for the first role name
                    $firstRoleName = 'No user';
                }
            } else {
                // Provide a default value for the first role name
                $firstRoleName = 'No booking';
            }

            // Use the first role name as needed
            //   echo $firstRoleName;
            // $server_roles = is_array((array) $booking->server()->exists()->first()->getRoleNames()) ? (array) $booking->server()->first()->getRoleNames()[0] : '';
            // $server_role =  $server_roles[0];
            // dd($server_roles[0]);
            $events[] = [
                'title' => ucfirst(str_replace('_', " ", $firstRoleName))  . ' ' . optional($booking->server()->first())->name . ' to ' . optional($booking->customer()->first())->name,
                'start' => $start,
                'end' => $end,
                "backgroundColor" => '#EFC050',
                "role" => $firstRoleName,
            ];

            // dd(json_encode($events));


        }

        return response()->json($events);
    }
}
