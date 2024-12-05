-- Create the database
CREATE DATABASE mykhs;

-- Use the database
USE mykhs;

-- Table for users (login)
CREATE TABLE users (
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('mahasiswa', 'dosen', 'admin') NOT NULL,
    PRIMARY KEY (username)
);

-- Sample data for the users table
INSERT INTO users (username, password, role) VALUES
('mahasiswa1', MD5('1'), 'mahasiswa'),
('dosen1', MD5('2'), 'dosen'),
('admin1', MD5('3'), 'admin');

-- Table for mahasiswa (students)
CREATE TABLE mahasiswa (
    npm VARCHAR(20) NOT NULL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    prodi VARCHAR(100) NOT NULL,
    fakultas VARCHAR(100) NOT NULL,  -- Faculty added here
    email VARCHAR(100) NOT NULL,     -- Email added here
    jenis_kelamin ENUM('L', 'P') NOT NULL,  -- Gender (L for Male, P for Female)
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
);

-- Table for dosen (lecturers)
CREATE TABLE dosen (
    nid VARCHAR(20) NOT NULL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    fakultas VARCHAR(100) NOT NULL,  -- Faculty
    matakuliah VARCHAR(100) NOT NULL,  -- Subject
    email VARCHAR(100) NOT NULL,     -- Email added here
    jenis_kelamin ENUM('L', 'P') NOT NULL,  -- Gender (L for Male, P for Female)
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
);

-- Table for admin
CREATE TABLE admin (
    nip VARCHAR(20) NOT NULL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,     -- Email added here
    jabatan VARCHAR(100) NOT NULL,   -- Position added here
    jenis_kelamin ENUM('L', 'P') NOT NULL,  -- Gender (L for Male, P for Female)
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
);
