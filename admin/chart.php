 <!-- line chart -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@0.7.7"></script>

 <script>
     var salesData = <?php echo json_encode(array_values($salesData)); ?>;
     var displayYear = "<?php echo $displayYear; ?>";

     var ctx1 = document.getElementById('summaryChart').getContext('2d');

     // Create gradient for the chart
     var gradient = ctx1.createLinearGradient(0, 0, 0, 400);
     gradient.addColorStop(0, 'rgba(255, 99, 132, 0.5)');
     gradient.addColorStop(1, 'rgba(255, 99, 132, 0)');

     var chart1 = new Chart(ctx1, {
         type: 'line',
         data: {
             labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
             datasets: [{
                 label: 'Total Sales (₱)',
                 data: salesData,
                 backgroundColor: gradient,
                 borderColor: 'rgba(255, 99, 132, 1)',
                 borderWidth: 2,
                 tension: 0.4,
                 fill: true
             }]
         },
         options: {
             responsive: true,
             plugins: {
                 zoom: {
                     pan: {
                         enabled: true,
                         mode: 'xy'
                     },
                     zoom: {
                         enabled: true,
                         mode: 'xy',
                     }
                 },
                 tooltip: {
                     mode: 'index',
                     intersect: false
                 },
                 legend: {
                     display: true,
                     position: 'top',
                     labels: {
                         font: {
                             weight: 'bold'
                         }
                     }
                 }
             },
             scales: {
                 x: {
                     title: {
                         display: true,
                         text: displayYear,
                         font: {
                             weight: 'bold'
                         }
                     },
                     ticks: {
                         font: {
                             weight: 'bold'
                         }
                     }
                 },
                 y: {
                     beginAtZero: true,
                     title: {
                         display: true,
                         text: 'Sales (₱)',
                         font: {
                             weight: 'bold'
                         }
                     },
                     ticks: {
                         font: {
                             weight: 'bold'
                         }
                     }
                 }
             }
         }
     });
 </script>

 <!-- pie chart -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

 <script>
     // Embed PHP data into JavaScript for the pie chart
     var orderStatusData = <?php echo json_encode(array_values($orderStatusData)); ?>;
     var orderStatusLabels = ['Pending', 'Processing', 'Shipped', 'Delivered'];

     // Calculate total orders for percentage calculation
     var totalOrders = orderStatusData.reduce((acc, curr) => acc + curr, 0);

     // Create Pie Chart for Orders by Status
     var ctxPie = document.getElementById('ordersPieChart').getContext('2d');
     var pieChart = new Chart(ctxPie, {
         type: 'pie',
         data: {
             labels: orderStatusLabels,
             datasets: [{
                 data: orderStatusData,
                 backgroundColor: [
                     'rgba(255, 99, 132, 1)',
                     'rgba(54, 162, 235, 1)',
                     'rgba(255, 206, 86, 1)',
                     'rgba(75, 192, 192, 1)'
                 ],
                 borderColor: [
                     'rgba(255, 255, 255, 1)',
                     'rgba(255, 255, 255, 1)',
                     'rgba(255, 255, 255, 1)',
                     'rgba(255, 255, 255, 1)'
                 ],
                 borderWidth: 1
             }]
         },
         options: {
             responsive: true,
             maintainAspectRatio: false,
             plugins: {
                 legend: {
                     position: 'right',
                 },
                 title: {
                     display: true,
                     text: `Total Orders`
                     // text: `Total Orders: ${totalOrders}`
                 },
                 tooltip: {
                     callbacks: {
                         label: function(tooltipItem) {
                             var label = tooltipItem.label || '';
                             var value = tooltipItem.raw;
                             return `${label}: ${value} orders`;
                         }
                     }
                 },
                 datalabels: {
                     color: 'white',
                     formatter: (value, context) => {
                         if (value === 0) {
                             return ''; // Hide the data label when the value is 0
                         }

                         // Get the label for the current index
                         var statusLabel = orderStatusLabels[context.dataIndex];

                         // Replace "Orders" based on the status
                         switch (statusLabel) {
                             case 'Pending':
                                 return value + ' Pending';
                             case 'Processing':
                                 return value + ' Processing';
                             case 'Shipped':
                                 return value + ' Shipped';
                             case 'Delivered':
                                 return value + ' Delivered';
                             default:
                                 return value + ' Orders'; // Fallback just in case
                         }
                     },
                     font: {
                         weight: 'bold',
                         size: 12
                     },
                     align: 'center'
                 }
             }
         },
         plugins: [ChartDataLabels] // Enable the datalabels plugin
     });
 </script>

 <!-- Modal Structure -->
 <div class="modal fade" id="topSellingModal" tabindex="-1" aria-labelledby="topSellingModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-scrollable modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="topSellingModalLabel">All Top Selling Products</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <ul class="list-group" id="allTopSellingProducts">
                     <!-- Top Selling Products will be loaded here -->
                 </ul>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Include JS libraries -->
 <!-- modal -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
     // MODAL
     // JavaScript to dynamically load products in modal
     document.addEventListener('DOMContentLoaded', function() {
         var allTopSellingProducts = <?php echo json_encode($allTopSellingProductsResult->fetch_all(MYSQLI_ASSOC)); ?>;
         var modalProductList = document.getElementById('allTopSellingProducts');

         allTopSellingProducts.forEach(function(product, index) {
             var listItem = document.createElement('li');
             listItem.className = 'list-group-item d-flex justify-content-between align-items-center';

             // Apply gold background to the first-ranked product
             if (index === 0) {
                 listItem.style.backgroundColor = '#FFD700'; // Gold color for rank 1
             }

             listItem.innerHTML = `
            <div class="product-details d-flex align-items-center">
                <span class="rank-badge" style="background-color: ${index === 0 ? '#FFD700' : (index === 1 ? '#C0C0C0' : '#CD7F32')}; color: white; padding: 5px 10px; border-radius: 45%; margin-right: 15px;">${index + 1}</span>
                <img src="management/${product.Photo}" class="product-image" alt="error">
                <div>
                    <strong>${product.ProductName}</strong>
                    <div class="rating">
                        ${'<i class="fas fa-star" style="color: gold;"></i>'.repeat(Math.round(product.AverageRating))}
                        ${'<i class="far fa-star" style="color: grey;"></i>'.repeat(5 - Math.round(product.AverageRating))}
                    </div>
                    <span class="badge rounded-pill text-bg-primary">${product.Sales} Sold</span>
                </div>
            </div>
            <span class="badge rounded-pill text-bg-secondary">₱${parseFloat(product.Price).toFixed(2)}</span>
        `;
             modalProductList.appendChild(listItem);
         });
     });
 </script>