<?php include('includes/worlddatastore.class.php') ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Json Test</title>

<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.8.0r4/build/reset-fonts-grids/reset-fonts-grids.css"> 

<style type="text/css">
.guardiantop { height:auto; margin-top:15px;}
.search-datastore {border-top:3px solid #0061A6;padding:5px 0 3px; margin:8px 0;}

.searchbar-datastore, .searchdrop-datastore {color:#333333;font-family:georgia,serif;font-size:20px;height:auto;margin-bottom:8px;margin-top:8px;margin-right:4px;padding:5px;width:60%;}

.searchdrop-datastore {padding:4px;width:25%;font-size:18px;}
.sumbit-datastore {height:36px;vertical-align:top;width:10%; margin-top:8px;}

a {text-decoration:none;color:#005689}
li {float:left;width:95%;margin-bottom:10px; padding-top:5px;}
li h2 {border-top:1px dotted #999999;float:none;font-family:georgia,serif;font-size:16px;font-weight:normal;margin-bottom:10px;min-height:100%;padding-top:5px;width:100%;color:#005689 }
dl {margin-top:14px;}
dd, dt {font-weight:bold;width:140px;float:left;}
p {margin-bottom:10px;}

.results_country {border-top:1px solid #999999;font-weight:bold;margin-bottom:0;padding:3px 0;}
.flag_icon {float:none;margin-right:2px;position:relative;top:1px;}

.pagination { margin:10px 0;float:left; clear:both; }
.pagination a {border:1px solid #CCCCCC;color:#005689;padding:5px; margin:2px;}


.facets {float:right;width:95%;height:auto;padding:5px;margin-left:5px;}
.facets li {border-top:1px dotted #999999;}
.facets h2 {background-color:#EDEDED;border-top:3px solid #0061A6 !important;font-size:20px;margin:0 0 10px;min-height:28px;padding:5px 0 15px 5px;}
.facets h4 {font-size:12px;margin-top:0;min-height:100%;padding:4px 0 9px;width:100%;font-weight:bold;}
.facets a {padding:4px 0;}
</style>
</head>
<body>

<?php
$guardianapi = new worldDataStore;
echo $guardianapi->guardian_world_data_store('/jsontest.php', $_GET['q'], $_GET['facet_country'], $_GET['facet_department'], $_GET['facet_year'], $_GET['facet_format'], $_GET['page']);
?>

</body>
</html>
