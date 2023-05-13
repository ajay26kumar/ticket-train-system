<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<style>
* {
  box-sizing: border-box;
}

input[type=text], select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  resize: vertical;
}

label {
  padding: 12px 12px 12px 0;
  display: inline-block;
}

input[type=submit] {
  background-color: #04AA6D;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  float: right;
}

input[type=submit]:hover {
  background-color: #45a049;
}

.container {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}

.col-25 {
  float: left;
  width: 25%;
  margin-top: 6px;
}

.col-75 {
  float: left;
  width: 75%;
  margin-top: 6px;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}

/* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .col-25, .col-75, input[type=submit] {
    width: 100%;
    margin-top: 0;
  }
}
</style>
</head>
<body>

<h2>Search Train</h2>

<div class="container">
  <form action="" name="searchTrainForm" id="searchTrainForm" method="post" >
    @csrf
    <div class="row">
      <div class="col-25">
        <label for="fname">Source Station</label>
      </div>
      <div class="col-75">
        <select name="s_station" id="s_station">
          <option value="">Select</option>
          @foreach ($stations as $station)
          <option value="{{ $station->s_no }}">{{ $station->s_name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="lname">Destination Station</label>
      </div>
      <div class="col-75">
        <select name="d_station" id="d_station">
          <option value="">Select</option>
          @foreach ($stations as $station)
          <option value="{{ $station->s_no }}">{{ $station->s_name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="lname">Number of seats</label>
      </div>
      <div class="col-75">
        <input type="number" id="no_seat" name="no_seat" placeholder="No of Seat">
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="lname">Journey Date</label>
      </div>
      <div class="col-75">
        <input type="date" id="rev_date" name="rev_date" placeholder="Your last name..">
      </div>
    </div>
    <div class="row">
      <input type="submit" id="submit" value="Submit">
    </div>
  </form>
</div>

<div class="container">
  <form action="{{url('view-reservation-seat')}}" name="submitPassengerDetails" id="submitPassengerDetails" method="post" >
    @csrf
    <div class="passengerdetail">
      
    </div>
  </form>
</div>

</body>
</html>
<script>
if ($("#searchTrainForm").length > 0) {
    $("#searchTrainForm").validate({
        rules: {
            s_station: {
                required: true
            },
            d_station: {
                required: true
            },
            rev_date: {
                required: true
            },
            no_seat: {
                required: true
            },
        },
        messages: {
            s_station: {
                required: "Please Select Source Station"
            },
            d_station: {
                required: "Please Select Destination Station"
            },
            rev_date: {
                required: "Please Select Date"
            },
            no_seat: {
                required: "Please Enter Seat"
            },
        },
        submitHandler: function(form) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if ($('#no_seat').val() > 7) {
                alert("Please Enter Upto 7 seats");
                return false;
            }
            if ($('#no_seat').val() <= 0) {
                alert("Seats should be greater than zero");
                return false;
            }
            $("#submit").attr("disabled", true);
            $.ajax({
                url: "{{url('search-train')}}",
                type: "POST",
                data: $('#searchTrainForm').serialize(),
                success: function(response) {
                    $('#submit').html('Submit');
                    $("#submit").attr("disabled", false);
                    alert(response.Available_Seat+ " " +response.message);
                    
                    $('.passengerdetail').append("<h2>Enter Passenger Details</h2>")
                    for (let i = 0; i < response.no_seat; i++) {
                        $('.passengerdetail').append("<div class='row'><div class='col-25'><label for='fname'>Passenger Name</label></div><div class='col-75'><input type='text' id='p_detail[]' name='p_detail[]' placeholder='Your name..''></div></div>")
                    }
                    $('.passengerdetail').append("<input type='hidden' id='s_station' name='s_station' value = '"+response.s_station+"'><input type='hidden' id='d_station' name='d_station' value = '"+response.d_station+"'><input type='hidden' id='no_seat' name='no_seat' value = '"+response.no_seat+"'>")
                    $('.passengerdetail').append("<div class='row'><input type='submit' id='p_submit' value='Submit'></div>");
                    // document.getElementById("searchTrainForm").reset();
                }
            });
        }
    })
}
</script>
