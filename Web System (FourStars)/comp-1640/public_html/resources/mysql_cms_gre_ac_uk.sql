

CREATE TABLE `Category` (
  `CategoryID` int(11) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) 



INSERT INTO `Category` (`CategoryID`, `Name`, `Description`, `Removed`) VALUES
(1, 'Reunion', NULL, 1),
(2, 'Medical', 'medical problems', 0),
(3, 'Engineering', 'computing related problems', 0),
(4, 'Farewell', NULL, 0);


CREATE TABLE `Comment` (
  `CommentID` int(11) NOT NULL,
  `IdeaID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `CommentText` text NOT NULL,
  `Anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `DatePosted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) 

INSERT INTO `Comment` (`CommentID`, `IdeaID`, `UserID`, `CommentText`, `Anonymous`, `DatePosted`, `Removed`) VALUES
(1, 2, 10, 'HAII!  How you doing, 0, '2023-03-22 21:27:00', 0),
(2, 1, 6, 'Fantastic idea', 0, '2023-03-23 16:22:31', 0),
(3, 1, 18, 'like that', 1, '2023-03-23 16:23:34', 1);


CREATE TABLE `Department` (
  `DepartmentID` int(11) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) 



INSERT INTO `Department` (`DepartmentID`, `Name`, `Description`, `Removed`) VALUES
(1, 'FOEIT', NULL, 0),
(2, 'Music', NULL, 0),
(3, 'Psychology', NULL, 0);


CREATE TABLE `Document` (
  `DocumentID` int(11) NOT NULL,
  `IdeaID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Document` longblob NOT NULL,
  `Size` int(11) NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) 

CREATE TABLE `Forum` (
  `ForumID` int(11) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Closure` datetime NOT NULL,
  `FinalClosure` datetime NOT NULL,
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) 


INSERT INTO `Forum` (`ForumID`, `Name`, `Description`, `Closure`, `FinalClosure`, `Removed`) VALUES
(1, 'Courses', 'Course related issues', '2023-03-29 12:00:00', '2023-04-29 12:00:00', 0),
(2, 'Student Affairs', 'Services & support divison', '2023-03-29 10:30:00', '2023-04-22 10:30:00', 0),
(3, 'Recreational Portal', 'Activities & events discussion', '2023-03-29 00:00:00', '2023-04-29 00:00:00', 1);


CREATE TABLE `Idea` (
  `IdeaID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ForumID` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `IdeaText` text NOT NULL,
  `Anonymous` tinyint(1) NOT NULL DEFAULT '0',
  `DatePosted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ViewCounter` int(11) NOT NULL DEFAULT '0',
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) 


INSERT INTO `Idea` (`IdeaID`, `UserID`, `ForumID`, `Title`, `IdeaText`, `Anonymous`, `DatePosted`, `ViewCounter`, `Removed`) VALUES
(1, 3, 1, 'BCOMP Fees', 'Too expensive lorhh', 0, '2023-03-21 12:00:00', 53, 0),
(2, 7, 1, 'Technology Fees', 'For what? Already pay so much', 1, '2023-03-22 21:20:00', 6, 0),
(3, 21, 1, 'Coursework dues', 'Due dates are too crucial', 0, '2023-03-23 02:51:53', 18, 0),
(4, 17, 1, 'BCOMP FYP', 'Wahhhh!! Can ask for so many requirements', 0, '2023-03-23 02:54:32', 5, 0),
(5, 2, 1, 'Course Lectures', 'Some lectures very pretty u know', 1, '2023-03-23 02:54:49', 2, 1),
(6, 3, 3, 'BCOMP Graduation', 'Want to finish fast and graduate', 0, '2020-02-19 18:45:48', 0, 0);



CREATE TABLE `IdeaCategory` (
  `IdeaCategory` int(11) NOT NULL,
  `IdeaID` int(11) NOT NULL,
  `CategoryID` int(11) NOT NULL
) 



INSERT INTO `IdeaCategory` (`IdeaCategory`, `IdeaID`, `CategoryID`) VALUES
(1, 1, 3),
(2, 5, 5);



CREATE TABLE `Rate` (
  `RateID` int(11) NOT NULL,
  `IdeaID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ThumbUp` tinyint(1) NOT NULL DEFAULT '0',
  `ThumbDown` tinyint(1) NOT NULL DEFAULT '0'
) 



INSERT INTO `Rate` (`RateID`, `IdeaID`, `UserID`, `ThumbUp`, `ThumbDown`) VALUES
(1, 1, 5, 1, 0),
(2, 2, 7, 1, 0),
(3, 1, 4, 1, 0),
(4, 1, 6, 1, 0),
(5, 1, 7, 0, 1),
(6, 3, 21, 0, 0),
(7, 4, 7, 0, 1),
(8, 4, 17, 0, 0),
(9, 2, 9, 0, 0);



CREATE TABLE `Role` (
  `RoleID` int(11) NOT NULL,
  `Name` varchar(30) DEFAULT NULL,
  `Type` varchar(30) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `NoDepartment` tinyint(1) NOT NULL DEFAULT '0',
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `Role` (`RoleID`, `Name`, `Type`, `Description`, `NoDepartment`, `Removed`) VALUES
(1, 'Quality Assurance Manager', 'Manager', NULL, 1, 0),
(2, 'Quality Assurance Coordinator', 'Coordinator', NULL, 0, 0),
(3, 'Academic Staff', 'Staff', NULL, 0, 0),
(4, 'Support Staff', 'Staff', NULL, 0, 0);



CREATE TABLE `Test` (
  `ID` int(11) NOT NULL,
  `Number` int(11) DEFAULT NULL,
  `String` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `Test` (`ID`, `Number`, `String`) VALUES
(3, NULL, 'faten'),
(4, 0, 'kajen'),
(5, 1, 'brian'),
(6, 1, 'Improvements'),
(7, 1, 'Pool Table'),
(8, 1, 'Computing'),
(9, 4, 'John');



CREATE TABLE `User` (
  `UserID` int(11) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `RoleID` int(11) NOT NULL,
  `UserName` varchar(20) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Admin` tinyint(1) NOT NULL DEFAULT '0',
  `Banned` tinyint(1) NOT NULL DEFAULT '0',
  `Removed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `User` (`UserID`, `DepartmentID`, `RoleID`, `UserName`, `Password`, `Email`, `Admin`, `Banned`, `Removed`) VALUES
(1, NULL, 1, 'kajen', 'password', 'kajen@mail.com', 1, 0, 0),
(2, 1, 2, 'keerthi', 'password', 'kitty@mail.com', 0, 0, 0),
(3, 1, 3, 'minqi', 'password', 'minqi@mail.com', 0, 0, 0),
(4, 1, 4, 'kai lin', 'password', 'kailin@mail.com', 0, 0, 0),
(5, 2, 2, 'brian', 'password', 'brian@mail.com', 0, 0, 0),
(6, 2, 3, 'miesha', 'password', 'amie@mail.com', 0, 0, 0),
(7, 2, 4, 'vilaa', 'password', 'vilaa@mail.com', 0, 0, 0),
(8, 3, 4, 'sathesh', 'password', 'satesh@mail.com', 0, 0, 0),
(9, 3, 3, 'jidah', 'password', 'hajidah@mail.com', 0, 0, 0),
(10, 3, 4, 'faten', 'password', 'faten@mail.com', 1, 0, 0),
(12, 1, 3, 'khai', 'password', 'khai@mail.com', 0, 0, 1),
(13, 2, 3, 'amirul', 'password', 'amirul@mail.com', 0, 0, 0),
(16, 3, 4, 'danny', 'password', 'danny@mail.com', 0, 0, 0),
(17, 1, 4, 'thana', 'password', 'thana@mail.com', 0, 0, 0),
(18, 1, 4, 'tnesh', 'password', 'tnesh@mail.com', 0, 0, 0),
(19, 2, 3, 'sharan', 'password', 'sharan@mail.com', 0, 0, 1),
(20, 3, 2, 'fahid', 'password', 'fahid@mail.com', 0, 0, 0),
(21, 1, 4, 'Hasebe', 'password', 'hasebe@mail.com', 0, 0, 0),
(22, 1, 4, 'asdf', 'password', 'asdf@gmail.com', 0, 0, 0);


ALTER TABLE `Category`
  ADD PRIMARY KEY (`CategoryID`);


ALTER TABLE `Comment`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `IdeaID` (`IdeaID`),
  ADD KEY `StaffID` (`UserID`);


ALTER TABLE `Department`
  ADD PRIMARY KEY (`DepartmentID`);


ALTER TABLE `Document`
  ADD PRIMARY KEY (`DocumentID`),
  ADD KEY `IdeaID` (`IdeaID`);

ALTER TABLE `Forum`
  ADD PRIMARY KEY (`ForumID`);

ALTER TABLE `Idea`
  ADD PRIMARY KEY (`IdeaID`),
  ADD KEY `StaffID` (`UserID`),
  ADD KEY `ForumID` (`ForumID`);


ALTER TABLE `IdeaCategory`
  ADD PRIMARY KEY (`IdeaCategory`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `IdeaID` (`IdeaID`);


ALTER TABLE `Rate`
  ADD PRIMARY KEY (`RateID`),
  ADD KEY `StaffID` (`UserID`),
  ADD KEY `IdeaID` (`IdeaID`);


ALTER TABLE `Role`
  ADD PRIMARY KEY (`RoleID`);


ALTER TABLE `Test`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `User`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `RoleID` (`RoleID`);



ALTER TABLE `Category`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `Comment`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `Department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `Document`
  MODIFY `DocumentID` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `Forum`
  MODIFY `ForumID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `Idea`
  MODIFY `IdeaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `IdeaCategory`
  MODIFY `IdeaCategory` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `Rate`
  MODIFY `RateID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;


ALTER TABLE `Role`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `Test`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;


ALTER TABLE `User`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

ALTER TABLE `Comment`
  ADD CONSTRAINT `Comment_ibfk_1` FOREIGN KEY (`IdeaID`) REFERENCES `Idea` (`IdeaID`),
  ADD CONSTRAINT `Comment_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `User` (`UserID`);

ALTER TABLE `Document`
  ADD CONSTRAINT `Document_ibfk_1` FOREIGN KEY (`IdeaID`) REFERENCES `Idea` (`IdeaID`);


ALTER TABLE `Idea`
  ADD CONSTRAINT `Idea_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `User` (`UserID`),
  ADD CONSTRAINT `Idea_ibfk_2` FOREIGN KEY (`ForumID`) REFERENCES `Forum` (`ForumID`);

ALTER TABLE `IdeaCategory`
  ADD CONSTRAINT `IdeaCategory_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `Category` (`CategoryID`),
  ADD CONSTRAINT `IdeaCategory_ibfk_2` FOREIGN KEY (`IdeaID`) REFERENCES `Idea` (`IdeaID`);


ALTER TABLE `Rate`
  ADD CONSTRAINT `Rate_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `User` (`UserID`),
  ADD CONSTRAINT `Rate_ibfk_2` FOREIGN KEY (`IdeaID`) REFERENCES `Idea` (`IdeaID`);


ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `Department` (`DepartmentID`),
  ADD CONSTRAINT `User_ibfk_2` FOREIGN KEY (`RoleID`) REFERENCES `Role` (`RoleID`);

