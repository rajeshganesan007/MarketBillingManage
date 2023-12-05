<!DOCTYPE html>
<html>
<head>
    <title>Product Stack</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .notification {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        .navigation {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Stack</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stack</th>
            </tr>
            <?php
            $servername = 'localhost';
            $dbname = 'ecom';
            $username = 'root';
            $password = '';

            $connection = mysqli_connect($servername, $username, $password, $dbname);

            if (!$connection) {
                die("Error: " . mysqli_error($connection));
            }

            $query = "SELECT id, nameofproduct, price, stack FROM client";
            $result = mysqli_query($connection, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $stack = $row['stack'];
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['nameofproduct'] . "</td>";
                    echo "<td>" . $row['price'] . "</td>";
                    echo "<td>" . $stack . "</td>";
                    echo "</tr>";

                    // No JavaScript function call here
                }
            }

            mysqli_close($connection);
            ?>
        </table>
        <div class="navigation">
            <a href="billing.php"><button>Go to Billing Page</button></a>
        </div>
    </div>
    <div class="notification" id="notification"></div>
    <script>
        // JavaScript function to show the notification
        function showNotification(message) {
            var notification = document.getElementById('notification');
            notification.innerText = message;
            notification.style.display = 'block';
            setTimeout(function() {
                notification.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        }

        // JavaScript to check and show notifications for low stack
        var rows = document.querySelectorAll('table tr');
        rows.forEach(function(row) {
            var cells = row.querySelectorAll('td');
            if (cells.length === 4) {
                var stackValue = parseInt(cells[3].textContent);
                if (stackValue < 10) {
                    var productName = cells[1].textContent;
                    showNotification('Low Stack Alert: ' + productName);
                }
            }
        });
    </script>
</body>
</html>
