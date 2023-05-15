<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<div>
	<h2>Reserved Seats</h2>
	<table>
	<tr>
		<td width='15%' align='center'  bgcolor='green'>Current Booked Seats</td>
		<td width='15%' align='center'  bgcolor='red'>Available Seats</td>
		<td width='15%' align='center'  bgcolor='grey'>Other Booked Seats</td>
	</tr>
	</table>
</div>
@php
	$divide = 7;
	$inside = 7;
@endphp
<table border='1'>

@for($i = 1; $i <= 80; $i++)
    @if($i % $divide == 1)
        <tr>
    @endif
    @if(in_array($i,$currentBookingSeat))
    	<td width='15%' align='center'  bgcolor='green'>{{ $i }}</td>
	@elseif(isset($alreadyBookedSeats) && in_array($i,$alreadyBookedSeats))
		<td width='15%' align='center'  bgcolor='grey'>{{ $i }}</td>
	@else
    	<td width='15%' align='center'  bgcolor='red'>{{ $i }}</td>
    @endif	
    @if($i % $inside == 0 && $i % $divide != 0)
        <td width='40%'>&nbsp;</td>
    @endif
    @if($i % $divide == 0)
        </tr>
    @endif
@endfor
<tr>
</tr>
</table>

</body>
</html>