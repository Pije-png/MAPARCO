<?php
include '../../connection.php';

// Fetch top sales products where payment status is 'Paid'
$sqlTopSales = "SELECT p.ProductName, p.Photo, SUM(oi.Quantity) as TotalQuantity, SUM(oi.Quantity * oi.Price) as TotalSales
                FROM orderitems oi
                INNER JOIN products p ON oi.ProductID = p.ProductID
                INNER JOIN orders o ON oi.OrderID = o.OrderID
                WHERE o.PaymentStatus = 'Paid'
                GROUP BY p.ProductID
                ORDER BY TotalSales DESC
                LIMIT 10";

$resultTopSales = $conn->query($sqlTopSales);
$topSalesData = [];
while ($row = $resultTopSales->fetch_assoc()) {
    $topSalesData[] = $row;
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Sales Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .cards-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .head {
            display: flex;
            justify-content: center;
        }

        .card1 {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            margin: 10px;
            padding: 20px;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .card1 img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .card-content1 {
            flex-grow: 1;
        }

        .card-content1 h3 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .card-content1 p {
            margin: 5px 0;
            color: #666;
        }

        .crown {
            font-size: 24px;
            color: gold;
            margin-left: 10px;
        }

        .highlight {
            background-color: rgb(255, 255, 68);
        }

        /*@media only screen and (max-width: 600px) and (min-device-width: 320px) {*/
        @media (max-width: 510px) {
            .card1 {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                width: 90%;
                max-width: 600px;
                margin: 10px;
                padding: 20px;
                display: flex;
                flex-direction: row;
                align-items: center;
            }

            .card1 img {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 8px;
                /* margin-right: 20px; */
            }

            .card-content1 h3 {
                font-size: 15px;
            }

            .card-content1 p {
                font-size: 12px;
            }

            .cards-container {
                display: flex;
                margin: auto;
                justify-content: center;
                width: 90%;
                padding-left: 10%;
            }

            .crown {
                font-size: 24px;
                color: gold;
                margin-left: 0px;
            }

            .highlight {
            background-color: rgb(255, 255, 68);
        }
        }
    </style>
</head>

<body class="body">
    <?php include 'sidebar.php'; ?>

     

    <div class="container-fluid">
        <div class="head mt-3">
            <h2>Top Sales Products</h2>
        </div>
        <div class="cards-container">
            <?php $rank = 1; ?>
            <?php foreach ($topSalesData as $row) : ?>
                <div class="card1 <?php echo $rank === 1 ? 'highlight' : ''; ?>">
                    <img src="<?php echo $row['Photo']; ?>" alt="<?php echo $row['ProductName']; ?>">
                    <div class="card-content1">
                        <h3>
                            <?php echo $row['ProductName']; ?>
                            <?php if ($rank === 1) : ?>
                                <i class="fas fa-crown crown"></i>
                            <?php endif; ?>
                        </h3>
                        <p>Total Quantity Sold: <?php echo $row['TotalQuantity']; ?></p>
                        <p>Total Sales Amount: ₱<?php echo number_format($row['TotalSales'], 2); ?></p>
                    </div>
                </div>
                <?php $rank++; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>