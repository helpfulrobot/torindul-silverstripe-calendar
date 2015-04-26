<div class="row">
	
	<!-- FULL -->
	<div class="col-md-12 main_section">
		
		<div class="col-md-12 page">
			
			<div class="row page_title">
				<div class="col-md-12">
					<h1>$Title</h1>
				</div>
			</div>
			
			<div class="col-md-12 page_content">
				
				<!-- Content -->
				$Content
				
				<!-- Calendar Controls -->
				<% include CalendarControls %>
				
				<!-- If a small screened device, show the list view -->
				<div class="calendar-container visible-xs-12 hidden-sm hidden-md hidden-lg"> 
					<% include CalendarList %>
				</div>
				
				<!-- Otherwise, show the full calendar. -->
				<div class="calendar-container hidden-xs">
					
					<!-- Full Day View -->
					
					<div class="calendar-no-padding list-view">
					
						<div class="list-month">
							
							<div class="list-month-name">$TodaysDate</div>
							<div class="list-day-container">							
								
								<% if $Top.EventsInDay() %>							
								
									<% loop $Top.EventsInDay() %>
										
										<div class="col-md-12 calendar-no-padding calendar-no-margin">
											<p>
												<a href="$Top.URLSegment/view/$Top.stringToSEOURL($Title)-ID-$ID" title="View $Title event">$Title</a>
											</p>
										</div>
									
									<% end_loop %>
								
								<% else %>
								
									<p class="list-day-none"><em>There are no events for the requested day.</em></p>
								
								<% end_if %>
								
							</div>	
											
						</div>
					
					</div>
					<!-- /Full Day View -->
					
				</div>
				
			</div>
			
		</div>
		
	</div>
	<!-- /FULL -->
	
</div>