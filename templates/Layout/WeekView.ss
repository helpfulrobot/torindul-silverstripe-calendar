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
					
					<!-- Full Week View -->
					<h3>Week commencing $CalendarWeekText</h3>
					$CalendarWeek()
					<!-- /Full Week View -->
					
				</div>
				
			</div>
			
		</div>
		
	</div>
	<!-- /FULL -->
	
</div>