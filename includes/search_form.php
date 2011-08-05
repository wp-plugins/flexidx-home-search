<?php
$cities = array('Phoenix', 'Scottsdale', 'Cave Creek', 'Carefree', 'Rio Verde', 'Paradise Valley', 'Tempe', 'Gilbert', 'Mesa', 'Chandler', 'Fountain Hills', 'Anthem', 'Peoria', 'Sun City', 'Glendale');
sort($cities);

$prices = array( 25000, 50000, 75000, 100000, 125000, 150000, 175000, 200000, 250000, 300000, 350000, 400000, 450000, 500000, 550000, 650000, 700000, 750000, 800000, 850000, 900000, 950000, 1000000, 1100000, 1200000, 1300000, 1400000, 1500000, 1600000, 1700000, 1800000, 1900000, 2000000, 2500000, 3000000, 3500000, 4000000, 4500000, 5000000);

foreach($prices as $price){
	$price_options .= '<option value="' . $price . '.0">$' . number_format($price) . '</option>';	
}

foreach($cities as $city){
	$options .= "<option>{$city}</option>";
}
?>
	<p>City
	  <select name="param[City]" size="1">
	    <?php echo $options; ?>
	  </select>
	</p>
	<p>Property Type
	  <select name="param[PropertySubType]" size="1">
	    <option value="SF">Single Family Homes</option>
	    <option value="AF">Condos/Townhouses</option>
	    <option value="LS">Lofts</option>
	  </select>
	</p>
	<p>Bedrooms
	  <select name="param[BedsTotal]" size="1">
	    <option value="1">1+</option>
	    <option value="2">2+</option>
	    <option value="3">3+</option>
	    <option value="4">4+</option>
	    <option value="5">5+</option>
	    <option value="6">6+</option>	    
	  </select>
	</p>
	<p>Bathrooms
	  <select name="param[BathsTotal]" size="1">
	    <option value="1.0">1+</option>
	    <option value="2.0">2+</option>
	    <option value="3.0">3+</option>
	    <option value="4.0">4+</option>
	    <option value="5.0">5+</option>
	    <option value="6.0">6+</option>	    
	  </select>
	</p>
	<p>Price Min
	  <select name="param[PriceMin]" size="1">
	  	<option value="">No Minimum</option>
	    <?php echo $price_options;?>
	  </select>
	</p>
	<p>Price Max
	  <select name="param[PriceMax]" size="1">
	    <option value="">No Maximum</option>
	    <?php echo $price_options;?>
	  </select>
	</p>
	<input type="submit" value="search" name="idxs_do" />