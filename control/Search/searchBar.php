<?php /* /control/Search/results.php
/control/FAQ/searchResults.php 
"/control/FAQ/createFAQ.php"*/ ?>

<form action="/control/Search/results.php" method="post" id="search">
<fieldset>
	<legend>Search</legend>
		<p>
		<input type="text" id='searchBox' name="searchString" class="input-text" />
		&nbsp;
		<input type="submit" value="OK" class="input-submit-02" />
		<br />
		<a href="javascript:toggle('search-options');" class="ico-drop">Search Options</a></p>
		<!-- Advanced search -->
		<div id="search-options" style="display:none;">
			<p>
				<label>
				<input type="radio" name="searchType" value=1 checked="checked" />
				Active</label>
				<br />
				<label>
				<input type="radio" name="searchType" value=2 />
				Old Data</label>
				<br />
				<label>
				<input type="radio" name="searchType" value=3 />
				FAQ</label>
			</p><p>
				<a href='/control/Search/createFAQ.php'>Create FAQ</a>
			</p>
		</div>
</fieldset>
</form>