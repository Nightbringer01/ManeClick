<!-- header.html -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maneclick</title>
    <style>
        .header-div {
            background-color: #415E35;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .header-div h1 {
            background-color: #2f603b;
            color: white;
            border: 1px solid black;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .nav-div ul {
            display: flex;
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .nav-div ul li {
            margin-left: 10px;
        }

        .nav-div ul li:first-child {
            margin-left: 0;
        }

        .nav-div ul li a {
            border: 1px solid #ffffff;
            border-radius: .5rem;
            background-color: #69aa85;
            padding: 5px 10px;
            margin-bottom: 5px;
            text-decoration: none;
            font-size: 24px;
            color: white;
        }

        .nav-div ul li a:hover {
            background-color: #79a18a;
        }
    </style>
</head>

<body>
    <header class="header-div">
        <a href="index.php" style="text-decoration: none;">
            <h1>Mane Click</h1>
        </a>
    </header>
</body>



</html>