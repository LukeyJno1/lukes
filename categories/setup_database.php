<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
</head>
<body>
    <h1>Database Setup</h1>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the form data
        $host = $_POST['host'];
        $db_name = $_POST['db_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $feedback = []; // Array to store feedback messages

        try {
            // Create a new PDO instance
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Create the database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");

            // Select the database
            $pdo->exec("USE `$db_name`");

            // Check if the 'categories' table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'categories'")->rowCount() > 0;

            if (!$tableExists) {
                // Create the 'categories' table
                $pdo->exec("
                    CREATE TABLE `categories` (
                        `id` INT AUTO_INCREMENT PRIMARY KEY,
                        `name` VARCHAR(255) NOT NULL,
                        `description` TEXT,
                        `image` VARCHAR(255),
                        `slug` VARCHAR(255) UNIQUE,
                        `keywords` TEXT,
                        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ");
                $feedback[] = "Table 'categories' created successfully.";
            } else {
                // Check if the 'keywords' column exists in the 'categories' table
                $columnExists = $pdo->query("SHOW COLUMNS FROM `categories` LIKE 'keywords'")->rowCount() > 0;
                if (!$columnExists) {
                    // Add the 'keywords' column to the 'categories' table
                    $pdo->exec("ALTER TABLE `categories` ADD COLUMN `keywords` TEXT");
                    $feedback[] = "Column 'keywords' added to the 'categories' table.";
                }
                $feedback[] = "Table 'categories' already exists. Checked and added the 'keywords' column if necessary.";

                // Check if each column exists in the 'categories' table
                $columnNames = ['name', 'description', 'image', 'slug', 'created_at', 'updated_at'];
                foreach ($columnNames as $columnName) {
                    $columnExists = $pdo->query("SHOW COLUMNS FROM `categories` LIKE '$columnName'")->rowCount() > 0;
                    if (!$columnExists) {
                        // Add the missing column to the 'categories' table
                        $pdo->exec("ALTER TABLE `categories` ADD COLUMN `$columnName` VARCHAR(255)");
                        $feedback[] = "Column '$columnName' added to the 'categories' table.";
                    }
                }
                $feedback[] = "Table 'categories' already exists. Checked and added missing columns if necessary.";
            }

            // Check if the 'category_closure' table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'category_closure'")->rowCount() > 0;

            if (!$tableExists) {
                // Create the 'category_closure' table
                $pdo->exec("
                    CREATE TABLE `category_closure` (
                        `ancestor_id` INT NOT NULL,
                        `descendant_id` INT NOT NULL,
                        `depth` INT NOT NULL,
                        PRIMARY KEY (`ancestor_id`, `descendant_id`),
                        FOREIGN KEY (`ancestor_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
                        FOREIGN KEY (`descendant_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
                    )
                ");
                $feedback[] = "Table 'category_closure' created successfully.";
            } else {
                // Check if each column exists in the 'category_closure' table
                $columnNames = ['ancestor_id', 'descendant_id', 'depth'];
                foreach ($columnNames as $columnName) {
                    $columnExists = $pdo->query("SHOW COLUMNS FROM `category_closure` LIKE '$columnName'")->rowCount() > 0;
                    if (!$columnExists) {
                        // Add the missing column to the 'category_closure' table
                        $pdo->exec("ALTER TABLE `category_closure` ADD COLUMN `$columnName` INT NOT NULL");
                        $feedback[] = "Column '$columnName' added to the 'category_closure' table.";
                    }
                }
                $feedback[] = "Table 'category_closure' already exists. Checked and added missing columns if necessary.";
            }

            // Check if the 'category_parents' table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'category_parents'")->rowCount() > 0;

            if (!$tableExists) {
                // Create the 'category_parents' table
                $pdo->exec("
                    CREATE TABLE `category_parents` (
                        `category_id` INT NOT NULL,
                        `parent_id` INT NOT NULL,
                        PRIMARY KEY (`category_id`, `parent_id`),
                        FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
                        FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
                    )
                ");
                $feedback[] = "Table 'category_parents' created successfully.";
            } else {
                // Check if each column exists in the 'category_parents' table
                $columnNames = ['category_id', 'parent_id'];
                foreach ($columnNames as $columnName) {
                    $columnExists = $pdo->query("SHOW COLUMNS FROM `category_parents` LIKE '$columnName'")->rowCount() > 0;
                    if (!$columnExists) {
                        // Add the missing column to the 'category_parents' table
                        $pdo->exec("ALTER TABLE `category_parents` ADD COLUMN `$columnName` INT NOT NULL");
                        $feedback[] = "Column '$columnName' added to the 'category_parents' table.";
                    }
                }
                $feedback[] = "Table 'category_parents' already exists. Checked and added missing columns if necessary.";
            }

            // Display success message and feedback
            echo '<div style="color: green;">Database setup completed successfully!</div>';
            echo '<ul>';
            foreach ($feedback as $message) {
                echo '<li>' . $message . '</li>';
            }
            echo '</ul>';
        } catch (PDOException $e) {
            // Display error message
            echo '<div style="color: red;">Error: ' . $e->getMessage() . '</div>';
        }
    }
    ?>

    <form method="post" action="">
        <label for="host">Host:</label>
        <input type="text" name="host" id="host" required><br>

        <label for="db_name">Database Name:</label>
        <input type="text" name="db_name" id="db_name" required><br>

        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password"><br>

        <input type="submit" value="Set Up Database">
    </form>
</body>
</html>