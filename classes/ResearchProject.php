<?php
class ResearchProject {
    private $conn;
    private $table_name = "researchprojects";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addResearchProject($userId, $projectTitle, $description, $status, $dateCreated) {
        try {
            $this->conn->beginTransaction();
            $query = "INSERT INTO " . $this->table_name . " (UserID, ProjectTitle, Description, Status, DateCreated) VALUES (:userId, :projectTitle, :description, :status, :dateCreated)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':projectTitle', $projectTitle);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':dateCreated', $dateCreated);

            $stmt->execute();
            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            if ($this->conn->inTransaction()) { $this->conn->rollBack(); }
            echo "Error adding record: " . $e->getMessage();
            return false;
        }
    }

    public function getResearchProjects() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY DateCreated DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            echo "Error fetching records: " . $e->getMessage();
            return false;
        }
    }

    public function updateResearchProject($projectId, $projectTitle, $description, $status) {
        try {
            $this->conn->beginTransaction();
            $query = "UPDATE " . $this->table_name . " SET ProjectTitle = :projectTitle, Description = :description, Status = :status WHERE ProjectID = :projectId";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':projectId', $projectId);
            $stmt->bindParam(':projectTitle', $projectTitle);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':status', $status);

            $stmt->execute();
            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            if ($this->conn->inTransaction()) { $this->conn->rollBack(); }
            echo "Error updating record: " . $e->getMessage();
            return false;
        }
    }

    public function deleteResearchProject($projectId) {
        try {
            $this->conn->beginTransaction();
            $query = "DELETE FROM " . $this->table_name . " WHERE ProjectID = :projectId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':projectId', $projectId);
            
            $stmt->execute();
            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            if ($this->conn->inTransaction()) { $this->conn->rollBack(); }
            echo "Error deleting record: " . $e->getMessage();
            return false;
        }
    }

    public function searchResearchProjects($keyword) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE ProjectTitle LIKE :keyword OR Description LIKE :keyword OR Status LIKE :keyword";
            $stmt = $this->conn->prepare($query);
            
            $search_term = "%{$keyword}%";
            $stmt->bindParam(':keyword', $search_term);
            
            $stmt->execute();
            return $stmt;
        } catch(PDOException $e) {
            echo "Error searching records: " . $e->getMessage();
            return false;
        }
    }
}
?>