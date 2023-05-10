<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;

use function PHPSTORM_META\type;

class PlannerCrudController extends Controller
{
    public function index()
    {
        $bookings = Booking::whereIn('payment_status', ['Paid', 'Unpaid'])->get();
        $events = [];

        $users = User::role(['stylist', 'art_director'])->get();

        // dd($users);

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
                'title' => optional($booking->services()->first())->name . ucfirst(str_replace('_', " ", $firstRoleName))  . ' ' . optional($booking->server()->first())->name . ' to ' . optional($booking->customer()->first())->name,
                'start' => $start,
                'end' => $end,
                "backgroundColor" => '#EFC050',
                "id" => $booking->id,
            ];
        }
        return view('planner', compact('users', 'events'));
    }

    public function getEvents()
    {
        $selectedName = request('name');
        $userId = User::where('id', $selectedName)->get()->first();
        if ($selectedName == 'all') {
            // $bookings = Booking::all();
            $bookings = Booking::whereIn('payment_status', ['Paid', 'Unpaid'])->get();
        } else {

            $bookings = Booking::whereHas('server', function ($query) use ($selectedName) {
                $query->where('id', (integer) $selectedName)->whereIn('payment_status', ['Paid', 'Unpaid']);
            })->get();
            // $bookings = Booking::where('server_id', $userId)->get();
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
                'title' => optional($booking->services()->first())->name . ucfirst(str_replace('_', " ", $firstRoleName))  . ' ' . optional($booking->server()->first())->name . ' to ' . optional($booking->customer()->first())->name,
                'start' => $start,
                'end' => $end,
                "backgroundColor" => '#EFC050',
                "role" => $firstRoleName,
                "id" => $booking->id,
            ];

            // dd(json_encode($events));


        }

        return response()->json($events);
    }

    public function getEventInfo($id) {
        $booking = Booking::where('id', $id)->first();

        $bookingResponse = [
            'id' => $booking->id,
            'title' => optional($booking->services()->first())->name,
            'customer_name' => optional($booking->customer()->first())->name,
            'customer_phone' => optional($booking->customer()->first())->phone,
            'booking_time' => $booking->booking_time,
            'booking_duration' => $booking->duration,
            'payment_status' => $booking->payment_status

        ];

        return response()->json($bookingResponse);
    }

    public function cancelBooking($id) {
        $booking = Booking::where('id', $id)->first();
        try {
            $booking->payment_status = "Cancelled";
            $booking->save();
            // $title = "Booking Cancelled!";
            // $body = "Your appointment with Sense Hair on " . $booking->booking_time->toDateString() . " at " . $booking->booking_time->format('H:i') . " at Central Plaza 12. has been cancelled!";
            // Mail::to($booking->customer->email)->send(new BookingSuccessful($body, $title));
            // LaraTwilio::notify($booking->customer->phone, $body);
            return redirect()->back();
        } catch (\Exception $e) {
            echo $e->getMessage();
            return $this->respondServerError(['message' => $e->getMessage()]);
        }
    }
}
