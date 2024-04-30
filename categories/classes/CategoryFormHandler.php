<?php

//CategoryFormHandler.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../classes/db_connect.php';
require_once __DIR__ . '/../classes/CategoryFormHandler.php';

$pdo = $db->getConnection();
$categoryPagesDir = '/category_pages/'; // Define the path to category pages directory

$categoryHandler = new CategoryFormHandler($pdo, $categoryPagesDir); // Pass both parameters
class CategoryFormHandler {
    private $db;
    private $categoryPagesDir;

    public function __construct($pdo, $categoryPagesDir = null) {
        $this->db = $pdo;
        $this->categoryPagesDir = $categoryPagesDir ?: '/category_pages/'; // Initialize with a default value if not provided
    }
    public function buildHierarchy($data, $parentId = 0) {
        $nested = [];
        foreach ($data as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildHierarchy($data, $category['id']);
                if (!empty($children)) {
                    $category['children'] = $children;
                }
                $nested[] = $category;
            }
        }
        return $nested;
    }

    public function generateCategoryHTML($data) {
        $nestedCategories = $this->buildHierarchy($data);

        $html = '';
        if (is_array($nestedCategories) && !empty($nestedCategories)) {
            foreach ($nestedCategories as $category) {
                $html .= "<div>{$category['name']}</div>";
                if (!empty($category['children'])) {
                    $html .= $this->generateCategoryHTML($category['children']);  // Recursive call
                }
            }
        } else {
            error_log('No categories to process or $nestedCategories is not an array.');
            $html = "<p>No categories available.</p>";
        }
        return $html;
    }

    public function getCategoryById($categoryId) {
        try {
            // Include 'parent_id' in the SELECT query
            $stmt = $this->db->prepare("SELECT id, name, slug, keywords, parent_id FROM categories WHERE id = :categoryId");
            $stmt->execute(['categoryId' => $categoryId]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            return $category !== false ? $category : null;
        } catch (\PDOException $exception) {
            error_log("Database error occurred while retrieving category: " . $exception->getMessage());
            throw new \Exception("Database error occurred while retrieving category: " . $exception->getMessage());
        }
    }
    

public function doesCategoryExistByName($name) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM categories WHERE name = :name");
            $stmt->execute(['name' => $name]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $exception) {
            error_log("Database error occurred while checking category existence: " . $exception->getMessage());
            throw new Exception("Database error occurred while checking category existence: " . $exception->getMessage());
        }
    }
    
    public function getAllCategories() {
        try {
            if ($this->db === null) {
                throw new Exception("Database connection is not established.");
            }
    
            // Modified to fetch immediate parent-child relationships using category_closure
            $sql = "
                SELECT c.id, c.name, c.slug, cc.ancestor_id AS parent_id
                FROM categories c
                LEFT JOIN category_closure cc ON c.id = cc.descendant_id AND cc.depth = 1
                ORDER BY c.name;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log("Database error occurred while retrieving all categories with immediate parent information: " . $exception->getMessage());
            throw new Exception("Database error occurred while retrieving all categories: " . $exception->getMessage());
        }
    }
        
    function getDescendants($categoryId) {
        try {
            $stmt = $this->db->prepare("
                WITH RECURSIVE descendants AS (
                    SELECT c.id, c.name, c.slug
                    FROM categories c
                    JOIN category_closure cc ON c.id = cc.descendant_id
                    WHERE cc.ancestor_id = :categoryId AND cc.depth > 0
                    UNION ALL
                    SELECT c.id, c.name, c.slug
                    FROM categories c
                    INNER JOIN descendants d ON c.id IN (
                        SELECT descendant_id
                        FROM category_closure
                        WHERE ancestor_id = d.id AND depth > 0
                    )
                )
                SELECT * FROM descendants;
            ");
            // Explicitly bind the categoryId to ensure it is correctly passed to the query
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('Fetched Descendants: ' . print_r($result, true));  // Log the fetched data
            return $result;
        } catch (\PDOException $exception) {
            error_log("Database error occurred while retrieving descendants: " . $exception->getMessage());
            throw new Exception("Database error occurred while retrieving descendants: " . $exception->getMessage());
        }
    }
    
    public function getImmediateChildren($categoryId) {
        try {
            $stmt = $this->db->prepare("
                SELECT cat.id, cat.name, cat.slug
                FROM categories AS cat
                JOIN category_closure AS cc ON cat.id = cc.descendant_id
                WHERE cc.ancestor_id = :categoryId AND cc.depth = 1
            ");
            $stmt->execute(['categoryId' => $categoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $exception) {
            error_log("Database error occurred while retrieving child categories: " . $exception->getMessage());
            throw new Exception("Database error occurred while retrieving child categories: " . $exception->getMessage());
        }
    }
    
    public function getCategoriesWithImmediateParent() {
        try {
            $sql = "
                SELECT c.id, c.name, c.slug, cc.ancestor_id AS parent_id
                FROM categories c
                LEFT JOIN category_closure cc ON c.id = cc.descendant_id AND cc.depth = 1
                ORDER BY c.name;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log("Database error occurred while retrieving categories with immediate parent information: " . $exception->getMessage());
            throw new Exception("Database error occurred while retrieving categories with hierarchy: " . $exception->getMessage());
        }
    }
    function fetchData() {
        // Your data fetching logic
        $result = $this->db->query('SELECT * FROM categories');
        if (!$result) {
            error_log('Failed to fetch data');
            return null;
        }
        return $result->fetchAll();
    }
    
public function getChildCategories($categoryId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.id, c.name 
                FROM categories c
                JOIN category_closure cc ON c.id = cc.descendant_id
                WHERE cc.ancestor_id = :categoryId AND cc.depth = 1
                ORDER BY c.name
            ");
            $stmt->execute(['categoryId' => $categoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $exception) {
            error_log("Database error occurred while retrieving child categories: " . $exception->getMessage());
            throw new Exception("Database error occurred while retrieving child categories: " . $exception->getMessage());
        }
    }
    
public function checkParent($parentID) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM categories WHERE id = :parent_id");
            $stmt->execute(['parent_id' => $parentID]);
            return ($stmt->fetchColumn() > 0);
        } catch (\PDOException $exception) {
            error_log("Database error occurred while checking parent category: " . $exception->getMessage());
            throw new Exception("Database error occurred while checking parent category: " . $exception->getMessage());
        }
    }
    
    public function getParentCategoryId($categoryId) {
        try {
            $stmt = $this->db->prepare("SELECT parent_id FROM category_parents WHERE category_id = :categoryId");
            $stmt->execute(['categoryId' => $categoryId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result !== false && isset($result['parent_id'])) {
                return $result['parent_id'];
            }
            return null;  // Return null if 'parent_id' is not found or query fails
        } catch (\PDOException $exception) {
            error_log("Database error occurred while retrieving parent category ID: " . $exception->getMessage());
            throw new \Exception("Database error occurred while retrieving parent category ID: " . $exception->getMessage());
        }
    }
    

public function canAddParentChildRelationship($childId, $proposedParentIds) {
        foreach ($proposedParentIds as $proposedParentId) {
            // Fetch all ancestors of the proposed parent
            $ancestors = $this->getAllAncestors($proposedParentId);
            
            // If the child ID is in the list of ancestors, it's a cycle
            if (in_array($childId, $ancestors)) {
                return false; // Indicates a cycle would be created
            }
        }
        
        // No cycles detected
        return true;
    }
    
public function getAllAncestors($categoryId) {
        $ancestors = [];
        $stmt = $this->db->prepare("SELECT ancestor_id FROM category_closure WHERE descendant_id = :categoryId AND ancestor_id != descendant_id");
        $stmt->execute(['categoryId' => $categoryId]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ancestors[] = $row['ancestor_id'];
        }
        
        return $ancestors;
    }
    public function getCategoriesWithHierarchy() {
        try {
            $sql = "
                SELECT c.id, c.name, c.slug, group_concat(cc.ancestor_id ORDER BY cc.depth DESC) AS path
                FROM categories c
                JOIN category_closure cc ON c.id = cc.descendant_id
                WHERE cc.depth > 0
                GROUP BY c.id
                ORDER BY path;
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log("Database error occurred while retrieving categories with hierarchy: " . $exception->getMessage());
            throw new Exception("Database error occurred while retrieving categories with hierarchy: " . $exception->getMessage());
        }
    }
    
    
private function updateCategoryClosure($categoryId, $parentId) {
        try {
            // Insert the category itself with depth 0. Avoid duplicate errors gracefully.
            $stmt = $this->db->prepare("INSERT INTO category_closure (ancestor_id, descendant_id, depth) VALUES (:ancestor_id, :descendant_id, 0) ON DUPLICATE KEY UPDATE ancestor_id=VALUES(ancestor_id)");
            $stmt->execute(['ancestor_id' => $categoryId, 'descendant_id' => $categoryId]);
    
            // Get all ancestors of the parent category and insert new records for the new category
            $ancestorsStmt = $this->db->prepare("SELECT ancestor_id, depth FROM category_closure WHERE descendant_id = :parent_id");
            $ancestorsStmt->execute(['parent_id' => $parentId]);
            while ($ancestor = $ancestorsStmt->fetch(PDO::FETCH_ASSOC)) {
                $stmt = $this->db->prepare("INSERT INTO category_closure (ancestor_id, descendant_id, depth) VALUES (:ancestor_id, :descendant_id, :depth + 1) ON DUPLICATE KEY UPDATE depth=VALUES(depth)");
                $stmt->execute([
                    'ancestor_id' => $ancestor['ancestor_id'],
                    'descendant_id' => $categoryId,
                    'depth' => $ancestor['depth'] + 1
                ]);
            }
        } catch (\PDOException $exception) {
            // Log and/or handle the error appropriately
            throw new Exception("Error updating category closure: " . $exception->getMessage());
        }
    }
    
    public function addCategory($name, $description, $image, $keywordsString, $url, $parentIds = []) {
        error_log("Entering addCategory method");
    
        if ($this->doesCategoryExistByName($name)) {
            error_log('Category name already exists.');
            header('Location: /categories/forms/create-category-form.php?error=' . urlencode('Category name already exists.'));
            exit;
        }
        
        $this->db->beginTransaction(); // Start the transaction
    
        try {
            $slug = $this->generateSlug($name); // Generate the slug from the category name
            error_log("Inserting category into the database");
    
            // Insert the category into the categories table including the slug and keywords
            $stmt = $this->db->prepare("INSERT INTO categories (name, description, image, slug, keywords) VALUES (:name, :description, :image, :slug, :keywords)");
            $result = $stmt->execute([
                'name' => $name,
                'description' => $description,
                'image' => $image,
                'slug' => $slug,
                'keywords' => $keywordsString
            ]);
    
            if (!$result) {
                error_log("Failed to insert category into the database. Name: " . $name);
                throw new Exception("Failed to insert category into the database.");
            }
    
            $categoryId = $this->db->lastInsertId();
            error_log("Inserted category ID: " . $categoryId);
    
            // ... (rest of the code for inserting parent-child relationships) ...
    
            $this->db->commit(); // Commit the transaction
            error_log("Transaction committed successfully and category created. Category ID: " . $categoryId);
    
            return $categoryId;
    
        } catch (\PDOException $exception) {
            $this->db->rollBack(); // Roll back the transaction in the event of a PDOException
    
            // Check if the exception is due to a duplicate entry for the slug
            if ($exception->getCode() == 23000 && strpos($exception->getMessage(), 'Duplicate entry') !== false && strpos($exception->getMessage(), 'for key \'categories.slug\'') !== false) {
                error_log("Duplicate slug detected, retrying with a new slug.");
                // Retry with a new slug
                return $this->addCategory($name, $description, $image, $keywordsString, $url, $parentIds);
            } else {
                error_log("Database error occurred while creating the category: " . $exception->getMessage());
                throw new Exception("Database error occurred while creating the category: " . $exception->getMessage());
            }
        } catch (\Exception $exception) {
            $this->db->rollBack(); // Roll back the transaction in the event of a generic Exception
            error_log("Error occurred while creating the category: " . $exception->getMessage());
            throw $exception;
        }
    }
function displayCategoryAccordion($currentCategoryId, $pdo, $categoryHandler) {
        // Now $categoryHandler is accessible within this function
         $descendants = $categoryHandler->getDescendants($currentCategoryId);
        $markup = generateAccordionMarkup($descendants);
        return $markup;
    }
    

    // Function to get the breadcrumb trail for a category
public function getBreadcrumbTrail($categoryId) {
        $breadcrumbs = [];
        
        while ($categoryId !== null) {
            $category = $this->getCategoryById($categoryId);
            if ($category !== null) {
                $breadcrumbs[] = $category;
                
                // Get the parent category ID from the category_parents table
                $parentId = $this->getParentCategoryId($categoryId);
                $categoryId = $parentId;
            } else {
                break;
            }
        }
        
        return array_reverse($breadcrumbs);
    }
    public function generateSlug($name) {
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $slug = $baseSlug;
        $counter = 1;
    
        while ($this->doesSlugExist($slug)) {
            // If the slug exists, append a number and check again
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
    
        return $slug;
    }
    
    public function getCategoryID($pageName) {
        // Assuming ID is part of the filename like 'category-123.php'
        $pattern = '/category-(\d+)\.php/';
        if (preg_match($pattern, $pageName, $matches)) {
            return $matches[1];
        }

        // If ID is not in the filename, read the file and extract the ID
        $filePath = $this->categoryPagesDir . $pageName;
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            // Assume the ID is stored in a specific format within the file
            if (preg_match('/<\!-- CategoryID: (\d+) -->/', $content, $matches)) {
                return $matches[1];
            }
        

        return null; // Return null if no ID found
    }
}


    private function doesSlugExist($slug) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM categories WHERE slug = :slug");
            $stmt->execute(['slug' => $slug]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $exception) {
            error_log("Database error occurred while checking slug existence: " . $exception->getMessage());
            throw new Exception("Database error occurred while checking slug existence: " . $exception->getMessage());
        }
    }
    
    public function getCategoryIdBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare("SELECT id FROM categories WHERE slug = :slug");
            $stmt->execute(['slug' => $slug]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result['id'];
            } else {
                error_log("No category found with the slug: " . $slug);
                return null;
            }
        } catch (PDOException $e) {
            error_log("Database error in getCategoryIdBySlug: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getCategoryBySlug($slug) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug");
            $stmt->execute(['slug' => $slug]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            } else {
                error_log("No category found with the slug: " . $slug);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Database error in getCategoryBySlug: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
