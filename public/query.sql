DROP DATABASE ClothingStore;
CREATE DATABASE ClothingStore;
USE ClothingStore;

CREATE TABLE Manager
(
  Id INT AUTO_INCREMENT NOT NULL,
  FullName VARCHAR(70) NOT NULL,
  Email VARCHAR(255) UNIQUE NOT NULL,
  Phone VARCHAR(15),
  Password VARCHAR(255) NOT NULL,
  Salt VARCHAR(255) NOT NULL,
  CreateDate DATETIME DEFAULT NOW() NOT NULL,
  PRIMARY KEY(Id)
);

CREATE TABLE AuthTokens
(
  Id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  Selector VARCHAR(128),
  Token VARCHAR(128),
  ManagerId INT UNSIGNED NOT NULL,
  Expires DATETIME NOT NULL,
  PRIMARY KEY (Id)
);

CREATE TABLE Customer
(
  Id INT AUTO_INCREMENT NOT NULL,
  FullName VARCHAR(128) NOT NULL,
  Phone VARCHAR(15),
  Email VARCHAR(255),
  Address TEXT,
  CreateDate DATETIME DEFAULT NOW() NOT NULL,
  PRIMARY KEY(Id)
);

CREATE TABLE Department
(
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(50) UNIQUE NOT NULL,
  PRIMARY KEY(Id)
);

CREATE TABLE Kind
(
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(50) NOT NULL,
  DepartmentId INT NOT NULL,
  PRIMARY KEY(Id),
  FOREIGN KEY(DepartmentId) REFERENCES Department(Id)
);

CREATE TABLE Brand
(
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(50) UNIQUE NOT NULL,
  PRIMARY KEY(Id)
);

CREATE TABLE Size
(
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(50) UNIQUE NOT NULL,
  PRIMARY KEY(Id)
);

CREATE TABLE Color
(
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(50) UNIQUE NOT NULL,
  PRIMARY KEY(Id)
);

CREATE TABLE Style
(
  Id INT AUTO_INCREMENT NOT NULL,
  Name VARCHAR(50) UNIQUE NOT NULL,
  PRIMARY KEY(Id)
);

CREATE TABLE Clothes
(
  Id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  ManagerId INT NOT NULL,
  Name VARCHAR(50) NOT NULL,
  Description TEXT,
  KindId INT NOT NULL,
  BrandId INT NOT NULL,
  StyleId INT NOT NULL,
  Price BIGINT NOT NULL,
  CreateDate DATETIME DEFAULT NOW() NOT NULL,
  PRIMARY KEY(Id),
  FOREIGN KEY (ManagerId) REFERENCES Manager(Id),
  FOREIGN KEY (KindId) REFERENCES Kind(Id),
  FOREIGN KEY (BrandId) REFERENCES Brand(Id),
  FOREIGN KEY (StyleId) REFERENCES Style(Id)
);

CREATE TABLE ItemOfClothes
(
  Id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  SizeId INT NOT NULL,
  ColorId INT NOT NULL,
  Images VARCHAR(1000) NOT NULL,
  Count INT NOT NULL,
  Code VARCHAR(255) UNIQUE NOT NULL,
  ClothesId BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY(Id),
  FOREIGN KEY (SizeId) REFERENCES Size(Id),
  FOREIGN KEY (ColorId) REFERENCES Color(Id),
  FOREIGN KEY (ClothesId) REFERENCES Clothes(Id)
);

CREATE TABLE Ordered
(
  Id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
  CustomerId INT NOT NULL,
  ItemOfClothesId BIGINT UNSIGNED NOT NULL,
  Count INT NOT NULL,
  Amount BIGINT NOT NULL,
  OrderedDate DATETIME DEFAULT NOW() NOT NULL,
  PRIMARY KEY(Id),
  FOREIGN KEY (CustomerId) REFERENCES Customer(Id),
  FOREIGN KEY (ItemOfClothesId) REFERENCES ItemOfClothes(Id)
);


INSERT INTO Manager(FullName, Email, Phone, Password, Salt) VALUES
  ('Rostislav Zalevsky', 'z.rostislav11@gmail.com', '+380967768906', '$2y$10$IYEuZ.SHIn4al0wFm4SotOUhdetpGlaNTxS4wclParjFgtUoiGuzK', '5a97cdf1a2d34'),
  ('Rostislav Z', 'zalevsky.r11@gmail.com', NULL, '$2y$10$KFByv3LZR64H2utKElQdYe/1j0B3Iq8LGhiUrD7lWbfZhJ/m9TpIC', '5a9da2301503e');

INSERT INTO Department(Name) VALUES ('Women'), ('Men'), ('Girls'), ('Boys'), ('Baby'), ('Accessories');
# DROP TABLE AuthTokens;
# UPDATE AuthTokens SET Expires = NOW() - INTERVAL 1 DAY WHERE Id = 3;
# DELETE FROM AuthTokens WHERE Expires < NOW();
# DELETE FROM Manager WHERE Email = 'zalevsky.r11@gmail.com';

SELECT * FROM Manager;
SELECT * FROM AuthTokens;

SELECT * FROM Clothes;
SELECT * FROM ItemOfClothes;