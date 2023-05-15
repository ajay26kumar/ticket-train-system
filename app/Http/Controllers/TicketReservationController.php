<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Station;
use App\Models\Train;
use App\Models\Booking;

class TicketReservationController extends Controller
{
    public function viewTrainSearch()
    {
    	$stations = Station::all();

    	return view('viewtrainsearch',compact('stations'));
    }

    public function SearchTrainRoute(Request $req)
    {
    	$searchroute = $this->getTrainAndSeatAvailability($req);
    	
    	if($searchroute->isNotEmpty()) {
	    	if ($searchroute[0]->num_of_seats == 0) {
	    	 	return response()->json(['message'=>'NO Seats Available']);
	    	}
	    
	    	return response()->json(['message'=>'Seats Available','Available_Seat'=>$searchroute[0]->num_of_seats,'no_seat'=>$req->no_seat,'s_station'=>$req->s_station,'d_station'=>$req->d_station]);	    	
    	}else {
	    	return response()->json(['message'=>'Train Route Not Found']);
	    }
    }

    public function viewReservationSeats(Request $req)
    {
    	$availableTrainSeat = $this->getTrainAndSeatAvailability($req);

    	$consectiveAvailableSeat =  $this->find_consecutive_seats(json_decode($availableTrainSeat[0]->seat_left,true),$req->no_seat);
    	$alreadyBookedSeats = [];
    	if(!empty($consectiveAvailableSeat)){
    		$alreadyBookedSeats = json_decode($availableTrainSeat[0]->booked_seat,true);
    		$seatsLeft = $this->calculateLeftSeats($availableTrainSeat[0]->seat_left,$consectiveAvailableSeat);
    		$currentBookingSeat = $consectiveAvailableSeat;
    		if(!empty($availableTrainSeat[0]->booked_seat)){
    			$allBookedSeat = array_merge(json_decode($availableTrainSeat[0]->booked_seat),$currentBookingSeat);
    		}else{
    			$allBookedSeat =  $currentBookingSeat;
    		}
    		
     		Train::where('s_station','=',$req->s_station)
				 ->where('d_station','=',$req->d_station)
		         ->update(['num_of_seats' => $availableTrainSeat[0]->num_of_seats - $req->no_seat,'seat_left'=>$seatsLeft,'booked_seat'=>$allBookedSeat]);
		}else{

			$nonConsectiveAvailableSeat = array_values(json_decode($availableTrainSeat[0]->seat_left,true));
			$alreadyBookedSeats = json_decode($availableTrainSeat[0]->booked_seat,true);
			$random_seats = array_rand($nonConsectiveAvailableSeat,$req->no_seat);
			if($random_seats == 0 ){
				++$random_seats;
				$random_seats = explode(" ", $random_seats);
			}
			for ($i=0; $i < count($random_seats) ; $i++) { 
				$currentBookingSeat[] = $nonConsectiveAvailableSeat[$i];
			}
			$seatsLeft = $this->calculateLeftSeats($availableTrainSeat[0]->seat_left,$currentBookingSeat);

			if(!empty($availableTrainSeat[0]->booked_seat)){
    			$allBookedSeat = array_merge(json_decode($availableTrainSeat[0]->booked_seat),$currentBookingSeat);
    		}
    		
			 Train::where('s_station','=',$req->s_station)
			      ->where('d_station','=',$req->d_station)
		          ->update(['num_of_seats' => $availableTrainSeat[0]->num_of_seats - $req->no_seat,'seat_left'=>$seatsLeft,'booked_seat'=>$allBookedSeat]);
		}

		$this->insertBookingDetails($availableTrainSeat,$req,$currentBookingSeat);

		return view('reservedSeatPreview',compact('seatsLeft','currentBookingSeat','allBookedSeat','alreadyBookedSeats'));
    }

    public function find_consecutive_seats($array, $count) {
	    $consecutive = array();
	    $previous = null;
	    foreach ($array as $value) {
	        if ($previous !== null && $value == $previous + 1) {
	            $consecutive[] = $value;
	            if ($found == $count) {
	                return $consecutive;
	            }
	        } else {
	            $consecutive = array($value);
	            $found = 1;
	        }
	        $previous = $value;
	        $found++;
    	}
	}

	function getTrainAndSeatAvailability($req)
	{
		return Train::where('s_station','=',$req->s_station)
					->where('d_station','=',$req->d_station)
					->Orwhere('num_of_seats','>',$req->no_seat)
					->get();
	}

	public function calculateLeftSeats($availableTrainSeat,$consectiveAvailableSeat)
	{
		$availableTrainSeat = array_values(json_decode($availableTrainSeat,true));
		$seatLeft = array_diff_assoc($availableTrainSeat,$consectiveAvailableSeat);
		
		return $seatLeft;
		
	}

	public function insertBookingDetails($availableTrainSeat,$req,$currentBookingSeat)
	{
		$passenger_details = json_encode($req->p_detail);
		
		$Booking = new Booking;
        $Booking->train_no = $availableTrainSeat[0]->train_number;
        $Booking->no_of_seats = $req->no_seat;
        $Booking->booked_seat = json_encode($currentBookingSeat);
        $Booking->passenger_details = $passenger_details;
        
        $Booking->save();

        return;
	}
}
