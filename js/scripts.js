// Load the Visualization API and the piechart package.
google.load('visualization', '1', {'packages':['corechart']});

//Functions
function idx_market_stats(zipcode, property_type, options){
  jQuery.post(idxAjax.ajaxurl, {
    action: 'idx_getStats_ajax',
    zipcode:		zipcode,
    //type:		type,
    property_type:      property_type,
    options:            options
    }, function(content){
        if(content){
          for(var i in content){
            var draw_func = 'draw_' + i + '_chart';
            _print_stats(content[i], draw_func);
          }
        }
    },"json"
  );
  return false;
}

function _print_stats(content, callback){
  window[callback](content);
}

//On document ready
jQuery(document).ready(function(){
  jQuery("a[rel='idx-photos']").colorbox();
  jQuery("a.more-info-btn").colorbox({inline:true, href:"#form1"});
  jQuery("a.showing-btn").colorbox({inline:true, href:"#form2"});

        jQuery("#idx_property_tabs").tabs();
        var stat_types = new Array('absorption', 'inventory', 'price', 'ratio', 'dom', 'volume');

        if(jQuery("#market_stats_tab").length > 0){
          for(i in stat_types){
            jQuery("#market_stats_tab").append('<div id="' + stat_types[i] + '"><p>Loading '+ stat_types[i] +' chart.</p></div>');
          }
          idx_market_stats(idxAjax.zip, 'A', '');
        }
});

// Callback that creates and populates a data table,
// instantiates the pie chart, passes in the data and
// draws it.
function draw_absorption_chart(content) {
  // Create our data table.
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Date');
  data.addColumn('number', 'Absorption Rate');
  var vals = content.Dates.length;
  data.addRows(vals);
  for(var i = 0; i < vals; i++){
      data.setValue(i, 0, content.Dates[i]);
      data.setValue(i, 1, content.AbsorptionRate[i]);
  }

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.LineChart(document.getElementById('absorption'));
  chart.draw(data, {width: 600, height: 240, title: 'Absorption Rate', hAxis: {title: 'Date'}, vAxis: {title: '% Rate'}});
}

function draw_inventory_chart(content){
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Date');
  data.addColumn('number', 'Active');
  data.addColumn('number', 'New');

  var vals = content.Dates.length;
  data.addRows(vals);
  for(var a = 0; a < vals; a++){
    data.setValue(a, 0, content.Dates[a]);
    data.setValue(a, 1, content.ActiveListings[a]);
    data.setValue(a, 2, content.NewListings[a]);
  }
   var chart = new google.visualization.LineChart(document.getElementById('inventory'));
  chart.draw(data, {width: 600, height: 240, title: 'Inventory', hAxis: {title: 'Date'}, vAxis: {title: 'Properties'}});
}

function draw_price_chart(content){
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Date');
  data.addColumn('number', 'Active Avg');
  data.addColumn('number', 'Active Median');
  data.addColumn('number', 'New Avg');
  data.addColumn('number', 'New Median');

  var vals = content.Dates.length;
  data.addRows(vals);
  for(var a = 0; a < vals; a++){
    data.setValue(a, 0, content.Dates[a]);
    data.setValue(a, 1, Math.round(content.ActiveAverageListPrice[a]));
    data.setValue(a, 2, Math.round(content.ActiveMedianListPrice[a]));
    data.setValue(a, 3, Math.round(content.NewAverageListPrice[a]));
    data.setValue(a, 4, Math.round(content.NewMedianListPrice[a]));
  }
   var chart = new google.visualization.LineChart(document.getElementById('price'));
  chart.draw(data, {width: 600, height: 240, title: 'Price', hAxis: {title: 'Date'}, vAxis: {title: 'Price'}});
}

function draw_ratio_chart(content){
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Date');
  data.addColumn('number', 'Ratio');

  var vals = content.Dates.length;
  data.addRows(vals);
  for(var a = 0; a < vals; a++){
    data.setValue(a, 0, content.Dates[a]);
    data.setValue(a, 1, Math.round(content.SaleToOriginalListPriceRatio[a]));
  }
   var chart = new google.visualization.LineChart(document.getElementById('ratio'));
  chart.draw(data, {width: 600, height: 240, title: 'Sales to List Price Ratios', hAxis: {title: 'Date'}, vAxis: {title: '% Ratio'}});
}

function draw_dom_chart(content){
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Date');
  data.addColumn('number', 'DOM');

  var vals = content.Dates.length;
  data.addRows(vals);
  for(var a = 0; a < vals; a++){
    data.setValue(a, 0, content.Dates[a]);
    data.setValue(a, 1, Math.round(content.AverageDom[a]));
  }
   var chart = new google.visualization.LineChart(document.getElementById('dom'));
  chart.draw(data, {width: 600, height: 240, title: 'Days On Market', hAxis: {title: 'Date'}, vAxis: {title: 'Avg DOM'}});
}

function draw_volume_chart(content){
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Date');
  data.addColumn('number', 'Active');
  data.addColumn('number', 'New');

  var vals = content.Dates.length;
  data.addRows(vals);
  for(var a = 0; a < vals; a++){
    data.setValue(a, 0, content.Dates[a]);
    data.setValue(a, 1, Math.round(content.ActiveListVolume[a]));
    data.setValue(a, 2, Math.round(content.NewListVolume[a]));
  }
   var chart = new google.visualization.LineChart(document.getElementById('volume'));
  chart.draw(data, {width: 600, height: 240, title: 'Volume', hAxis: {title: 'Date'}, vAxis: {title: 'Volume'}});
}