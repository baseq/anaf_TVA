<?php
function anaf() {
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if file already exists
	/*if (file_exists($target_file)) {
		echo "Sorry, file already exists.";
		$uploadOk = 0;
	}*/
	// Allow certain file formats
	if($fileType != "txt") {
		echo "Eroare! Doar txt-uri.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			$all_rows = array();
			$header = null;
			$handle = fopen($target_file, "r");

			while ($row = fgetcsv($handle)) {
		                if(array(null) !== $row) {
        	        	    if ($header === null) {
                	        	$header = $row;
		                        continue;
                		    }
		                    $all_rows[] = array_combine($header, $row);
                		}
			}
			$json = json_encode($all_rows);

			$url = "https://webservicesp.anaf.ro/PlatitorTvaRest/api/v1/ws/tva";
			$content = $json;

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,
					array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

			$json_response = curl_exec($curl);

			curl_close($curl);
			return str_replace('"',"'",$json_response);

		} else {
			return "Fiserul a mai fost adaugat odata";
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.1.0/css/responsive.dataTables.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	var data = <?php echo anaf(); ?>;
	jQuery('#myTableFound').DataTable( {
		data: data.found,
		paging: false,
		columns: [
			{ "title": "Adresa", "data" : "adresa" },
			{ "title": "CUI", "data" : "cui" },
			{ "title": "Data", "data" : "data" },
			{ "title": "Data Anul Imp", "data" : "data_anul_imp" },
			{ "title": "Data Sfarsit", "data" : "data_sfarsit" },
			{ "title": "Denumire", "data" : "denumire" },
			{ "title": "Mesaj", "data" : "mesaj" },
			{ "title": "TVA", "data" : "tva" }
		]
	});
	jQuery('#myTableNotFound').DataTable( {
	data: data.notfound,
	paging: false,
	columns: [
			{ "title": "Adresa", "data" : "adresa" },
			{ "title": "CUI", "data" : "cui" },
			{ "title": "Data", "data" : "data" },
			{ "title": "Data Anul Imp", "data" : "data_anul_imp" },
			{ "title": "Data Sfarsit", "data" : "data_sfarsit" },
			{ "title": "Denumire", "data" : "denumire" },
			{ "title": "Mesaj", "data" : "mesaj" },
			{ "title": "TVA", "data" : "tva" }
	]
});

});
</script>
</head>
<body>
<h3>Gasiti</h3>
<table class="display" id="myTableFound"></table>
<h3>Negasiti</h3>
<table class="display" id="myTableNotFound"></table>
<p><a href="index.php">Inapoi la Upload</a></p>
</body>
</html>
