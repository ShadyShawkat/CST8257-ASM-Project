-- Create and Use Database
CREATE DATABASE IF NOT EXISTS CST8257;
USE CST8257;

-- Create Table: User
CREATE TABLE User (
    UserId VARCHAR(16) NOT NULL PRIMARY KEY,
    Name VARCHAR(256) NOT NULL,
    Phone VARCHAR(16),
    Password VARCHAR(256) NOT NULL
);

-- Create Table: Accessibility
CREATE TABLE Accessibility (
    Accessibility_Code VARCHAR(16) NOT NULL PRIMARY KEY,
    Description VARCHAR(127) NOT NULL
);

-- Create Table: Album
CREATE TABLE Album (
    Album_Id INT(11) PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(256) NOT NULL,
    Description VARCHAR(3000),
    Date_Updated DATE NOT NULL,
    Owner_Id VARCHAR(16) NOT NULL,
    Accessibility_Code VARCHAR(16) NOT NULL,
    FOREIGN KEY (Owner_Id) REFERENCES User (UserId),
    FOREIGN KEY (Accessibility_Code) REFERENCES Accessibility (Accessibility_Code)
);

-- Create Table: Picture
CREATE TABLE Picture (
    Picture_Id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Album_Id INT(11) NOT NULL,
    FileName VARCHAR(255) NOT NULL,
    Title VARCHAR(256) NOT NULL,
    Description VARCHAR(3000),
    Date_Added DATE NOT NULL,
    FOREIGN KEY (Album_Id) REFERENCES Album (Album_Id)
);

-- Create Table: Comment
CREATE TABLE Comment (
    Comment_Id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    Author_Id VARCHAR(16) NOT NULL,
    Picture_Id INT(11) NOT NULL,
    Comment_Text VARCHAR(3000) NOT NULL,
    Date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Author_Id) REFERENCES User (UserId),
    FOREIGN KEY (Picture_Id) REFERENCES Picture (Picture_Id)
);

-- Create Table: FriendshipStatus
CREATE TABLE FriendshipStatus (
    Status_Code VARCHAR(16) NOT NULL PRIMARY KEY,
    Description VARCHAR(128) NOT NULL
);

-- Create Table: Friendship
CREATE TABLE Friendship (
    Friend_RequesterId VARCHAR(16) NOT NULL,
    Friend_RequesteeId VARCHAR(16) NOT NULL,
    Status VARCHAR(16) NOT NULL,
    PRIMARY KEY (Friend_RequesterId, Friend_RequesteeId),
    FOREIGN KEY (Friend_RequesterId) REFERENCES User (UserId),
    FOREIGN KEY (Friend_RequesteeId) REFERENCES User (UserId),
    FOREIGN KEY (Status) REFERENCES FriendshipStatus (Status_Code)
);

---
## Sample Data Insertion

-- Insert data into Accessibility
INSERT INTO Accessibility (Accessibility_Code, Description) VALUES
('private', 'Accessible only by the owner'),
('shared', 'Accessible to owner and friends');

-- Insert data into User
INSERT INTO User (UserId, Name, Phone, Password) VALUES
('user001', 'Alice Wonderland', '111-222-3333', 'pass123'),
('user002', 'Bob TheBuilder', '444-555-6666', 'pass456'),
('user003', 'Charlie Chaplin', '777-888-9999', 'pass789'),
('user004', 'Diana Prince', '000-111-2222', 'pass101'),
('user005', 'Eve Adams', '333-444-5555', 'pass202'),
('user006', 'Frank Ocean', '555-666-7777', 'pass303'),
('user007', 'Grace Hopper', '888-999-0000', 'pass404'),
('user008', 'Harry Styles', '123-456-7890', 'pass505'),
('user009', 'Ivy Queen', '987-654-3210', 'pass606'),
('user010', 'Jack Sparrow', '246-813-5790', 'pass707');

-- Insert data into Album
INSERT INTO Album (Title, Description, Date_Updated, Owner_Id, Accessibility_Code) VALUES
('Alice''s Vacation', 'Pictures from my trip to the Bahamas.', '2024-06-15', 'user001', 'shared'),
('Bob''s Work Progress', 'Photos of my latest construction projects.', '2024-07-01', 'user002', 'private'),
('Charlie''s Art Collection', 'My favorite art pieces.', '2024-05-20', 'user003', 'shared'),
('Diana''s Training', 'Daily workouts and progress.', '2024-07-05', 'user004', 'private'),
('Alice''s Pet Photos', 'My furry friends!', '2024-06-20', 'user001', 'shared'),
('Frank''s Travels', 'Memories from around the globe.', '2024-07-01', 'user006', 'shared'),
('Grace''s Code Snippets', 'Interesting programming concepts.', '2024-07-03', 'user007', 'private'),
('Harry''s Concert Pics', 'Behind the scenes at my shows.', '2024-06-28', 'user008', 'shared');

-- Insert data into Picture
INSERT INTO Picture (Album_Id, FileName, Title, Description, Date_Added) VALUES
(1, 'beach_sunset.jpg', 'Beautiful Sunset', 'A stunning view of the sunset over the ocean.', '2024-06-10'),
(1, 'island_view.png', 'Island Landscape', 'Green hills and clear blue water.', '2024-06-12'),
(2, 'building_frame.gif', 'New Building Frame', 'Steel frame for the new high-rise.', '2024-06-25'),
(3, 'sculpture_detail.jpg', 'Abstract Sculpture', 'Close-up of a modern sculpture.', '2024-05-18'),
(4, 'gym_selfie.png', 'Post Workout', 'Feeling strong after a tough session.', '2024-07-04'),
(5, 'fluffy_cat.jpg', 'My Cat Whiskers', 'Just relaxing on the couch.', '2024-06-18'),
(5, 'playful_dog.gif', 'Puppy Playtime', 'My dog chasing a ball.', '2024-06-19'),
(6, 'eiffel_tower.jpg', 'Paris Nights', 'The iconic Eiffel Tower at night.', '2024-06-29'),
(7, 'algorithm_flow.png', 'Complex Algorithm', 'Visualizing a data sorting algorithm.', '2024-07-02'),
(8, 'stage_lights.gif', 'Live Performance', 'Capturing the energy on stage.', '2024-06-27');

-- Insert data into Comment
INSERT INTO Comment (Author_Id, Picture_Id, Comment_Text) VALUES
('user002', 1, 'Amazing picture, Alice!'),
('user003', 1, 'Wish I was there!'),
('user001', 3, 'Looking good, Bob!'),
('user004', 4, 'Very interesting piece, Charlie.'),
('user001', 5, 'Keep up the great work, Diana! 💪'),
('user003', 6, 'So cute!'),
('user004', 7, 'What a happy dog!'),
('user001', 8, 'Beautiful shot, Frank!'),
('user006', 9, 'Fascinating, Grace!'),
('user007', 10, 'Incredible energy, Harry!');

-- Insert data into FriendshipStatus
-- Removed 'blocked' as requested
INSERT INTO FriendshipStatus (Status_Code, Description) VALUES
('pending', 'Friend request sent, waiting for acceptance'),
('accepted', 'Friends');

-- Insert data into Friendship (Users with friends and some without)
-- Clear existing friendships to avoid duplicates if this script is run multiple times
DELETE FROM Friendship;

-- Alice (user001) is friends with Bob (user002) and Charlie (user003)
INSERT INTO Friendship (Friend_RequesterId, Friend_RequesteeId, Status) VALUES
('user001', 'user002', 'accepted'),
('user002', 'user001', 'accepted'), -- Bi-directional friendship
('user001', 'user003', 'accepted'),
('user003', 'user001', 'accepted');

-- Bob (user002) is also friends with Diana (user004)
INSERT INTO Friendship (Friend_RequesterId, Friend_RequesteeId, Status) VALUES
('user002', 'user004', 'accepted'),
('user004', 'user002', 'accepted');

-- Charlie (user003) has a pending request to Eve (user005)
INSERT INTO Friendship (Friend_RequesterId, Friend_RequesteeId, Status) VALUES
('user003', 'user005', 'pending');

-- New users and their friendships:
-- Frank (user006) is friends with Grace (user007) and Harry (user008)
INSERT INTO Friendship (Friend_RequesterId, Friend_RequesteeId, Status) VALUES
('user006', 'user007', 'accepted'),
('user007', 'user006', 'accepted'),
('user006', 'user008', 'accepted'),
('user008', 'user006', 'accepted');

-- Ivy (user009) has a pending request to Jack (user010)
INSERT INTO Friendship (Friend_RequesterId, Friend_RequesteeId, Status) VALUES
('user009', 'user010', 'pending');

-- Some users without established friendships (in this sample set):
-- Eve (user005) has only a pending request from Charlie.
-- Jack (user010) has only a pending request from Ivy.
CREATE TABLE IF NOT EXISTS SetupFlags (
    Flag VARCHAR(50) PRIMARY KEY,
    Value BOOLEAN NOT NULL
);