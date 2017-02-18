/**
 * 
 */

$(document).ready(function () {
	
	
	
	
	///bar 1 ///
	
    var s1 = [[2002, 112000], [2003, 122000], [2004, 104000], [2005, 99000], [2006, 121000], 
    [2007, 148000], [2008, 114000], [2009, 133000], [2010, 161000], [2011, 173000]];
    var s2 = [[2002, 10200], [2003, 10800], [2004, 11200], [2005, 11800], [2006, 12400], 
    [2007, 12800], [2008, 13200], [2009, 12600], [2010, 13100]];
 
    plot1 = $.jqplot("chart1", [s2, s1], {
    	
    	 seriesColors: [ "#7dc6ec", "#f8a4a3"],
        // Turns on animatino for all series in this plot.
        animate: true,
        // Will animate plot on calls to plot1.replot({resetAxes:true})
        animateReplot: true,
        cursor: {
            show: true,
            zoom: true,
            looseZoom: true,
            showTooltip: false
        },
        series:[
            {
                pointLabels: {
                    show: true
                },
                renderer: $.jqplot.BarRenderer,
                showHighlight: false,
                yaxis: 'y2axis',
                rendererOptions: {
                    // Speed up the animation a little bit.
                    // This is a number of milliseconds.  
                    // Default for bar series is 3000.  
                    animation: {
                        speed: 2500
                    },
                    barWidth: 15,
                    barPadding: -15,
                    barMargin: 0,
                    highlightMouseOver: false
                }
            }, 
            {
                rendererOptions: {
                    // speed up the animation a little bit.
                    // This is a number of milliseconds.
                    // Default for a line series is 2500.
                    animation: {
                        speed: 2000
                    }
                }
            }
        ],
        axesDefaults: {
            pad: 0
        },
        axes: {
            // These options will set up the x axis like a category axis.
            xaxis: {
                tickInterval: 1,
                drawMajorGridlines: false,
                drawMinorGridlines: true,
                drawMajorTickMarks: false,
                rendererOptions: {
                tickInset: 0.5,
                minorTicks: 1
            }
            },
            yaxis: {
                tickOptions: {
                    formatString: "$%'d"
                },
                rendererOptions: {
                    forceTickAt0: true
                }
            },
            y2axis: {
                tickOptions: {
                    formatString: "$%'d"
                },
                rendererOptions: {
                    // align the ticks on the y2 axis with the y axis.
                    alignTicks: true,
                    forceTickAt0: true
                }
            }
        },
        highlighter: {
            show: true, 
            showLabel: true, 
            tooltipAxes: 'y',
            sizeAdjust: 7.5 , tooltipLocation : 'ne'
        }
    });
    
    
    
    ///// bar 2 ////
    
    var s1 = [200, 600, 700, 1000];
    var s2 = [460, -210, 690, 820];
    var s3 = [-260, -440, 320, 200];
    // Can specify a custom tick Array.
    // Ticks should match up one for each y value (category) in the series.
    var ticks = ['May', 'June', 'July', 'August'];
     
    var bar2 = $.jqplot('bar2', [s1, s2, s3], {

    	seriesColors: [ "#b5658b", "#7dc6ec", "#eceff1","#aa3333","#5fab78","#fba867","#3333aa"],
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true}
        },
       
        series:[
            {label:'Hotel'},
            {label:'Event Regristration'},
            {label:'Airfare'}
        ],
        
        legend: {
            show: true,
            placement: 'outsideGrid'
        },
        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
                pad: 1.05,
                tickOptions: {formatString: '$%d'}
            }
        }
    });
    
   //////////////////////////////////// 
    
    
    <!-- pie-->
   //  "#b5658b","#7dc6ec","#eceff1","#aa3333","#5fab78","#fba867","#3333aa",

    	  var data = [
    	    ['Heavy Industry', 12],['Retail', 9], ['Light Industry', 14], 
    	    ['Out of home', 16],['Commuting', 7], ['Orientation', 9]
    	  ];
    	  var pie = jQuery.jqplot ('pie', [data], 
    	    { 
    		  
    	      seriesDefaults: {
    	    	 seriesColors: [ "#b5658b", "#7dc6ec", "#eceff1","#aa3333","#5fab78","#fba867","#3333aa"],
    	        // Make this a pie chart.
    	        renderer: jQuery.jqplot.PieRenderer, 
    	        rendererOptions: {
    	          // Put data labels on the pie slices.
    	          // By default, labels show the percentage of the slice.
    	          showDataLabels: true
    	        }
    	      }, 
    	      legend: { show:true, location: 'e' }
    	    }
    	  );
    	  
    	  ///pie 2///
    	  
    	  jQuery.jqplot.config.enablePlugins = true;
    	  pie2 = jQuery.jqplot('pie2', 
    	    [[['Verwerkende industrie', 9],['Retail', 8], ['Primaire producent', 7], 
    	    ['Out of home', 6],['Groothandel', 5], ['Grondstof', 4], ['Consument', 3], ['Bewerkende industrie', 2]]], 
    	    {
    	      title: ' ', 
    	      seriesDefaults: {shadow: true, renderer: jQuery.jqplot.PieRenderer, rendererOptions: { 
    	          fill: false, 
    	          sliceMargin: 4, 
    	          showDataLabels: true
    	          } 
    	      }, 
    	      legend: { show:true }
    	    }
    	  );
  
    	  /////////////////////////////////////////
    	  
    	  
    	  
    	  
    	  /// bubble ///
    	  
    	  var arr = [[11, 123, 1236, "Acura"], [45, 92, 1067, "Alfa Romeo"], 
    	             [24, 104, 1176, "AM General"], [50, 23, 610, "Aston Martin Lagonda"], 
    	             [18, 17, 539, "Audi"], [7, 89, 864, "BMW"], [2, 13, 1026, "Bugatti"]];
    	              
    	             var plot3 = $.jqplot("bubblechart", [arr],{
    	                 title: 'Bubble Chart with Gradient Fills',
    	                 seriesDefaults:{
    	                     renderer: $.jqplot.BubbleRenderer,
    	                     rendererOptions: {
    	                         bubbleGradients: true
    	                     },
    	                     shadow: true
    	                 }
    	             });
    	  
    	/// bubble ///
    	             
    	             
    	             
    /// another ///
    	         
    	             var l0 = [6,  11, 10, 13, 11,  7];
    	             var l1 = [3,   6,  7,  7,  5,  3];
    	             var l2 = [4,   8,  9, 10, 8,   6];
    	             var l3 = [9,  13, 14, 16, 17, 19];
    	             var l4 = [15, 17, 16, 18, 13, 11];
    	              
    	             var plot1 = $.jqplot("another", [l0, l1, l2, l3, l4], {
    	                 title: "Fill between 2 lines",
    	                 axesDefaults: {
    	                     pad: 1.05
    	                 },
    	                 //////
    	                 // Use the fillBetween option to control fill between two
    	                 // lines on a plot.
    	                 //////
    	                 fillBetween: {
    	                     // series1: Required, if missing won't fill.
    	                     series1: 1,
    	                     // series2: Required, if  missing won't fill.
    	                     series2: 3,
    	                     // color: Optional, defaults to fillColor of series1.
    	                     color: "rgba(227, 167, 111, 0.7)",
    	                     // baseSeries:  Optional.  Put fill on a layer below this series
    	                     // index.  Defaults to 0 (first series).  If an index higher than 0 is
    	                     // used, fill will hide series below it.
    	                     baseSeries: 0,
    	                     // fill:  Optional, defaults to true.  False to turn off fill.  
    	                     fill: true
    	                 },
    	                 seriesDefaults: {
    	                     rendererOptions: {
    	                         //////
    	                         // Turn on line smoothing.  By default, a constrained cubic spline
    	                         // interpolation algorithm is used which will not overshoot or
    	                         // undershoot any data points.
    	                         //////
    	                         smooth: true
    	                     }
    	                 }
    	             });
    	          
    	             // bind a function to the change button to update the plot's fillBetween series
    	             // and replot().
    	             $("button[name=changeFill]").click(function(e) {
    	                 plot1.fillBetween.series1 = parseInt($("input[name=series1]").val());
    	                 plot1.fillBetween.series2 = parseInt($("input[name=series2]").val());
    	                 plot1.replot();
    	             }); 	             
    	             
  /// another ///  	   
    	             
    	             
    	             
 
 /// gague ////
 
    	             s1 = [52200];
    	             
    	             gague = $.jqplot('gague',[s1],{
    	                 seriesDefaults: {
    	                     renderer: $.jqplot.MeterGaugeRenderer,
    	                     rendererOptions: {
    	                         label: 'Metric Tons per Year',
    	                         labelPosition: 'bottom',
    	                         labelHeightAdjust: -5,
    	                         intervalOuterRadius: 85,
    	                         ticks: [10000, 30000, 50000, 70000],
    	                         intervals:[22000, 55000, 70000],
    	                         intervalColors:['#66cc66', '#E7E658', '#cc6666']
    	                     }
    	                 }
    	             });         
    	             
    	             
    	             
 
 /// gague ////
 
    	             
     	             
    	  
    
   
});