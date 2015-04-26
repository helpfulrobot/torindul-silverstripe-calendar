<div class="Actions hidden-xs">
	
	<div class="left">
	
		<!-- Previous Day, Week, Month Control -->	
		<% if $Action='dayview' %>
			<a href="$URLSegment/dayview/$GETPreviousDay" title="View Previous Month" class="btn btn-primary prev-month">&lt;- Previous Day</a>
		<% else_if $Action='weekview' %>
			<a href="$URLSegment/weekview/$GETPreviousWeek" title="View Previous Month" class="btn btn-primary prev-month">&lt;- Previous Week</a>		
		<% else %>
			<a href="$URLSegment/$GETPreviousMonth" title="View Previous Month" class="btn btn-primary prev-month">&lt;- Previous Month</a>				
		<% end_if %>
		
	</div>
	
	<div class="right">
		
		<!-- Next Day, Week, Month Control -->
		<% if $Action='dayview' %>
			<a href="$URLSegment/dayview/$GETNextDay" class="btn btn-primary next-month">Next Day -&gt;</a>
		<% else_if $Action='weekview' %>
			<a href="$URLSegment/weekview/$GETNextWeek" class="btn btn-primary next-month">Next Week -&gt;</a>
		<% else %>
			<a href="$URLSegment/$GETNextMonth" class="btn btn-primary next-month">Next Month -&gt;</a>
		<% end_if %>
		
		<!-- View Controls (Specify Day, Week, or Month View) -->
		<div class="calendar-view btn-group hidden-xs" role="group">
			
			<a href="/$URLSegment/$Action" class="btn btn-default" title="View Today" rel="nofollow">
				Today
			</a>
			
			<a href="$URLSegment/dayview" class="btn btn-default<% if $Action='dayview' %> btn-success<% end_if %>" title="View Calendar in Day View">
				Day View
			</a>
			
			<a href="$URLSegment/weekview" class="btn btn-default<% if $Action='weekview' %> btn-success<% end_if %>" title="View Calendar in Week View">
				Week View
			</a>
			
			<a href="$URLSegment" class="btn btn-default<% if $Action='index' %> btn-success<% end_if %>" title="View Calendar in Month View">
			  Month View
			</a>
			
		</div>
		
	</div>
	
</div>