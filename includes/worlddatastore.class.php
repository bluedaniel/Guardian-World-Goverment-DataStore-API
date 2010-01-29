<?php

	/************************************************************\
	 *	This is a script to display the World Goverment DataStore
	 *  search API widget on any website.
	 *
	 *  1) Include this script into your file.
	 *  2) Make sure you attach the stylesheet
	 *  3) Just copy and paste the following two lines anywhere you
	 *     want the box to be displayed.
	 *  
	 *     $guardianapi = new worldDataStore;
	 *     echo $guardianapi->guardian_world_data_store('Name of filename.php', $_GET['q'], $_GET['facet_country'], $_GET['facet_department'], $_GET['facet_year'], $_GET['facet_format'], $_GET['page']);
	 *  
	 *  ----------------------------------------------------------
	 *  Version No: 1
	 *  Release Date: 24th January 2010 
	 *
	 *  Any improvements or comments can be left at:
	 *  http://www.github.com/87930
	 *
	 \************************************************************/
	
	class worldDataStore {
		
		// Function to get the JSON results
		function get_content($url) {
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			ob_start();
			curl_exec ($ch);
			curl_close ($ch);
			$string = ob_get_contents();
			ob_end_clean();
			return $string;   
		}
		
		// Function to convert the JSON into a PHP Array.
		function object_2_array($data) {
			if(is_array($data) || is_object($data)) {
				$result = array(); 
				foreach ($data as $key => $value) { 
					$result[$key] = $this->object_2_array($value); 
				}
				return $result;
			}
			return $data;
		}
		
		
		function guardian_world_data_store($filename, $searchterm, $countryfacet, $deptfacet, $yearfacet, $formatfacet, $pageno) {
			
			// Determine what variables have been passed if any.
			
			if(!empty($searchterm)) {
				$search = "q=".$searchterm;
			} else {
				$search = "q=";
			}
			
			if(!empty($pageno)) {
				$pageno = "&page=".$pageno;
			}
			
			if(!empty($deptfacet)) {
				$deptfacet = "&facet_department=".$deptfacet;
			}
			
			if(!empty($countryfacet)) {
				$country = "&facet_country=".$countryfacet;
			}
			
			if(!empty($yearfacet)) {
				$yearfacet = "&facet_year=".$yearfacet;
			}
			
			if(!empty($formatfacet)) {
				$formatfacet = "&facet_format=".$formatfacet;
			}
			
			// Start to build the URL for the API and getting the data into a useable Array.
							
			$jsonurl = "http://www.guardian.co.uk/world-government-data/search.json?".$search.$country.$deptfacet.$yearfacet.$formatfacet.$pageno;

			$json = $this->get_content($jsonurl);
			
			$json_output = $this->object_2_array(json_decode($json));
			
			// This is the form your users will use to search the website.
			$outputform .= '
			<div class="guardiantop">
				<img alt="Data store: World Government Data" src="http://static.guim.co.uk/gudata/static/100/img/world-gov-data-med.gif"/>
				<br/>
				<form method="get" action="'.$filename.'" class="search-datastore">
					<input class="searchbar-datastore" type="text" value="';
					if(!empty($searchterm)) { $outputform .= $searchterm; }
				$outputform .= '" name="q"" autocomplete="off"/>
					<select class="searchdrop-datastore" name="facet_country">
						<option '; if ($countryfacet == '0') { $outputform .= 'selected="selected"'; } $outputform .= 'value="0">All Countries</option>
						<option '; if ($countryfacet == 'GB') { $outputform .= 'selected="selected"'; } $outputform .= 'value="GB">United Kingdom</option>
						<option '; if ($countryfacet == 'US') { $outputform .= 'selected="selected"'; } $outputform .= 'value="US">America</option>
						<option '; if ($countryfacet == 'AU') { $outputform .= 'selected="selected"'; } $outputform .= 'value="AU">Austrailia</option>
						<option '; if ($countryfacet == 'NZ') { $outputform .= 'selected="selected"'; } $outputform .= 'value="NZ">New Zealand</option>
					</select>
					<input type="submit" class="sumbit-datastore" value="Search"/>
				</form>
			</div>
			';
			
			if (!empty($searchterm)) {
				if ($json_output['results']) {
					if ($json_output['facets']) {
						$i = 0;
						$outputfacets .= '<div class="facets"><h2>Browse by</h2>';
						// Go through the list of facets and echo them with the proper URL using the
						// 'get_facet_url' function at the bottom of this page.
						foreach ($json_output['facets'] as $key => $value) {
							if (is_array($value)) {
								$outputfacets .= "<h4>".ucfirst($key)."</h4>";
								foreach ($value as $facet) {
									$outputfacets .= "<li><a href='".$this->get_facet_url($key, $filename, $search, $country, $deptfacet, $yearfacet, $formatfacet)."&facet_".$key."=".$facet['key']."'>".$facet['name']." (".$facet['count'].")</a></li>";
								}
							}
							$i++;
						}
						$outputfacets .= '</div>';
					}
					$i = 0;
					
					// This line of code outputs how many results there are, plus a link to the ATOM feed of this search.
					$outputresults .= "
					<p><strong>".$json_output['total_results']."</strong> results | <a href='http://www.guardian.co.uk/world-government-data/search.atom?".$search.$country.$deptfacet.$yearfacet.$formatfacet."'>Click here for a feed of these results (atom)</a></p>";
					
					// Use the getResult function to loop through the results and apply some HTML styling to each one.
					$outputresults .= $this->getResult($json_output['results']);
					
					// Use the dataPagination function to build the links to the other pages.
					$outputresults .= $this->dataPagination($json_output['current_page'],$json_output['total_pages'],$filename,$search,$country,$deptfacet,$yearfacet,$formatfacet);
				} else {
					// No results
					$outputresults .= "<h3>Im sorry there are no results for that search, please try again</h3>";
				}
			} else {
				// No search term entered
				$outputresults .= "<h3>Use the search box above to search through our API</h3>";
			}
		
		// Return data with HTML as per YUI Grids CSS standards.
		return 
		'<div class="yui-t5">
		'.$outputform.'
			<div id="yui-main">
				<div class="yui-b">
				'.$outputresults.'
				</div>
			</div>
			<div class="yui-b">
			'.$outputfacets.'
			</div>
		</div>';
		}
		
		
		// This is the fcuntion that will output the results along with some HTML
		function getResult($results) {
			foreach ($results as $result) {
				$outputresults .= "<li>";
				// Here is a link to a small png flag icon, we highly recommend using the ones found at:
				// http://www.famfamfam.com/lab/icons/flags/
				$outputresults .= '<p class="results_country">Country: <img src="flagpng/'.strtolower($result['country']).'.png" class="flagicon" /> '.$result['country_display'].'</p>';
				$outputresults .= "<h2><a href='".$result['details_link']."' >".$result['title']."</a></h2>";
				$outputresults .= "<p>".$result['description']."</p>";
				
				$outputresults .= '
				<dl>
					<dt>Source:</dt>
					<dd property="dc:source"><a href="'.$result['source_link'].'" >'.$result['source_title'].'</a></dd>
				</dl>';
				$i++;
				$outputresults .= "</li>";
			}
			return $outputresults;
		}
					
		
		// Simple function to render a URL for faceting.
		function get_facet_url($key, $filename, $search, $country, $deptfacet, $yearfacet, $formatfacet) {
			if($key == 'department') {
				$faceturl = $filename."?".$search.$country.$yearfacet.$formatfacet;
			} elseif ($key == 'format') {
				$faceturl = $filename."?".$search.$country.$deptfacet.$yearfacet;
			} elseif ($key == 'year') {
				$faceturl = $filename."?".$search.$country.$deptfacet.$formatfacet;
			} elseif ($key == 'country') {
				$faceturl = $filename."?".$search.$deptfacet.$yearfacet.$formatfacet;
			}
			return $faceturl;
		}
		
		
		// Function to build the links for pagination, the only variable that you might want to change here is swing, ie 4 results either side of the current page.
		function dataPagination($current_page,$total_pages,$filename,$search,$country,$deptfacet,$yearfacet,$formatfacet) {
			if ($total_pages > 1) {
				$pagination .= "<div class='pagination'><p>Go to page:</p>";
				
				$swing = 4; // How many numbers either side of the current page we should display
				
				$p = $current_page;
				$lp = $current_page-$swing;
				$hp = $current_page+$swing;
				
				while ($lp < $total_pages+1) {
					if ( ($lp > 0) && ($lp <= $hp) ) {
						if ($lp == $p) {
							$pagination .= $lp."  ";
						} else {
							$pagination .= "<a href='".$filename."?".$search.$country.$deptfacet.$yearfacet.$formatfacet."&page=".$lp."'>".$lp."</a>  ";
						}
					}
					$lp++;
				}
				$pagination .= "</div>";
				return $pagination;
			}
		}
		
		
	}// End of Class
			
?>