<!-- Full List View -->
<div class="calendar-no-padding list-view">
	
	<% loop loopMonth() %>
	
		<div class="list-month">
			
			<div class="list-month-name">$MonthName</div>
			<div class="list-day-container">							
				
				<% if $Top.EventsInMonth( $MonthNumber ) %>							
				
					<% loop $Top.EventsInMonth( $MonthNumber ) %>
					
						<div class="col-md-2 col-xs-3 calendar-no-padding">
							$Top.stringToDate($StartDate, 'jS M')
							<% if $StartDate!=$EndDate %> - $Top.stringToDate($EndDate, 'jS M')<% end_if %>
						</div>
						
						<div class="col-xs-1">&nbsp;</div>
						
						<div class="col-md-10 col-xs-8 calendar-no-padding">
							<p>
								<a href="$Top.URLSegment/view/$Top.stringToSEOURL($Title)-$ID" title="View $Title event">$Title</a>
							</p>
						</div>
					
					<% end_loop %>
				
				<% else %>
				
					<p class="list-day-none"><em>There are no events in $MonthName.</em></p>
				
				<% end_if %>
				
			</div>					
		</div>
		
	<% end_loop %>														
	
</div>
<!-- /Full List View -->