<?php
class Book {
    private $conn;

    public $book_id;
    public $book_title;
    public $author;
    public $publisher;
    public $category;
    public $image;

    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all books
    public function read() {
        $query = "SELECT * FROM books";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create a new book
    public function create() {
        $query = "INSERT INTO books (book_title, author, publisher, category, image, status) 
                  VALUES (:book_title, :author, :publisher, :category, :image, :status)";
        $stmt = $this->conn->prepare($query);
    
        // Clean data
        $this->book_title = htmlspecialchars(strip_tags($this->book_title));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->publisher = htmlspecialchars(strip_tags($this->publisher));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->status = htmlspecialchars(strip_tags($this->status));
    
        // Bind parameters
        $stmt->bindParam(':book_title', $this->book_title);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':publisher', $this->publisher);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':status', $this->status); // Bind status parameter
    
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    

    // Get a single book by ID
    public function getById() {
        $query = "SELECT * FROM books WHERE book_id = :book_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":book_id", $this->book_id, PDO::PARAM_INT);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // Populate object properties
            $this->book_title = $row['book_title'];
            $this->author = $row['author'];
            $this->publisher = $row['publisher'];
            $this->category = $row['category'];
            $this->image = $row['image'];
            $this->status = $row['status'] ?? 'Available'; // Assuming there is a status column
    
            return $row; // Return the fetched data if needed
        }
        return null; // Return null if no data is found
    }
    

    // Update an existing book
    public function update() {
        $query = "UPDATE books 
                  SET book_title = :book_title, author = :author, publisher = :publisher, 
                      category = :category, image = :image, status = :status 
                  WHERE book_id = :book_id";
        $stmt = $this->conn->prepare($query);
    
        // Clean data
        $this->book_title = htmlspecialchars(strip_tags($this->book_title));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->publisher = htmlspecialchars(strip_tags($this->publisher));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->status = htmlspecialchars(strip_tags($this->status));
    
        // Bind parameters
        $stmt->bindParam(":book_id", $this->book_id);
        $stmt->bindParam(":book_title", $this->book_title);
        $stmt->bindParam(":author", $this->author);
        $stmt->bindParam(":publisher", $this->publisher);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":status", $this->status);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    

    // Delete a book
    public function delete() {
        $query = "DELETE FROM books WHERE book_id = :book_id";
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->book_id = htmlspecialchars(strip_tags($this->book_id));

        // Bind parameter
        $stmt->bindParam(":book_id", $this->book_id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function addBookStatus($book_id) {
        $query = "INSERT INTO book_status (book_id, status) VALUES (:book_id, 'Available')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $book_id);
        
        if ($stmt->execute()) {
            echo "Book status successfully added!";
            return true;
        } else {
            echo "Failed to add book status.";
            // Debugging information
            print_r($stmt->errorInfo());
            return false;
        }
    }

    
    
}
?>
