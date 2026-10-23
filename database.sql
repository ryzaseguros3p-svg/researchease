-- ResearchEase database (import this in phpMyAdmin)
-- NOTE: table names are case-sensitive on InfinityFree (Linux), keep them exactly like this.

CREATE TABLE IF NOT EXISTS `users` (
  `UserID` INT AUTO_INCREMENT PRIMARY KEY,
  `FullName` VARCHAR(150) NOT NULL,
  `Email` VARCHAR(150) NOT NULL UNIQUE,
  `PasswordHash` VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `ResearchProjects` (
  `ProjectID` INT AUTO_INCREMENT PRIMARY KEY,
  `UserID` INT NOT NULL,
  `ProjectTitle` VARCHAR(255) NOT NULL,
  `Description` TEXT,
  `Status` VARCHAR(50) NOT NULL,
  `DateCreated` DATE NOT NULL,
  FOREIGN KEY (`UserID`) REFERENCES `users`(`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB;
