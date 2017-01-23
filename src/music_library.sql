-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 23, 2016 at 02:25 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `music_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `Albums`
--

CREATE TABLE `Albums` (
  `AlbumId` int(11) NOT NULL,
  `AlbumName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `AlbumsData`
--

CREATE TABLE `AlbumsData` (
  `AlbumsDataId` int(11) NOT NULL,
  `AlbumId` int(11) NOT NULL,
  `SongId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Artists`
--

CREATE TABLE `Artists` (
  `ArtistId` int(11) NOT NULL,
  `ArtistName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ArtistsData`
--

CREATE TABLE `ArtistsData` (
  `ArtistsData` int(11) NOT NULL,
  `ArtistId` int(11) NOT NULL,
  `AlbumId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Songs`
--

CREATE TABLE `Songs` (
  `SongId` int(11) NOT NULL,
  `SongName` varchar(255) NOT NULL,
  `TrackNumber` int(11) NOT NULL,
  `Duration` double NOT NULL COMMENT 'duration in seconds',
  `FullPath` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SongsData`
--

CREATE TABLE `SongsData` (
  `SongsDataId` int(11) NOT NULL,
  `ArtistId` int(11) NOT NULL,
  `SongId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Albums`
--
ALTER TABLE `Albums`
  ADD PRIMARY KEY (`AlbumId`);

--
-- Indexes for table `AlbumsData`
--
ALTER TABLE `AlbumsData`
  ADD PRIMARY KEY (`AlbumsDataId`),
  ADD KEY `AlbumId` (`AlbumId`),
  ADD KEY `SongId` (`SongId`);

--
-- Indexes for table `Artists`
--
ALTER TABLE `Artists`
  ADD PRIMARY KEY (`ArtistId`);

--
-- Indexes for table `ArtistsData`
--
ALTER TABLE `ArtistsData`
  ADD PRIMARY KEY (`ArtistsData`),
  ADD KEY `ArtistId` (`ArtistId`),
  ADD KEY `AlbumId` (`AlbumId`);

--
-- Indexes for table `Songs`
--
ALTER TABLE `Songs`
  ADD PRIMARY KEY (`SongId`);

--
-- Indexes for table `SongsData`
--
ALTER TABLE `SongsData`
  ADD PRIMARY KEY (`SongsDataId`),
  ADD KEY `SongId` (`SongId`),
  ADD KEY `ArtistId` (`ArtistId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Albums`
--
ALTER TABLE `Albums`
  MODIFY `AlbumId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `AlbumsData`
--
ALTER TABLE `AlbumsData`
  MODIFY `AlbumsDataId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT for table `Artists`
--
ALTER TABLE `Artists`
  MODIFY `ArtistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `ArtistsData`
--
ALTER TABLE `ArtistsData`
  MODIFY `ArtistsData` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
--
-- AUTO_INCREMENT for table `Songs`
--
ALTER TABLE `Songs`
  MODIFY `SongId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT for table `SongsData`
--
ALTER TABLE `SongsData`
  MODIFY `SongsDataId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `AlbumsData`
--
ALTER TABLE `AlbumsData`
  ADD CONSTRAINT `AlbumsData_ibfk_1` FOREIGN KEY (`AlbumId`) REFERENCES `Albums` (`AlbumId`),
  ADD CONSTRAINT `AlbumsData_ibfk_2` FOREIGN KEY (`SongId`) REFERENCES `Songs` (`SongId`);

--
-- Constraints for table `ArtistsData`
--
ALTER TABLE `ArtistsData`
  ADD CONSTRAINT `ArtistsData_ibfk_1` FOREIGN KEY (`ArtistId`) REFERENCES `Artists` (`ArtistId`),
  ADD CONSTRAINT `ArtistsData_ibfk_2` FOREIGN KEY (`AlbumId`) REFERENCES `Albums` (`AlbumId`);

--
-- Constraints for table `SongsData`
--
ALTER TABLE `SongsData`
  ADD CONSTRAINT `SongsData_ibfk_1` FOREIGN KEY (`SongId`) REFERENCES `Songs` (`SongId`),
  ADD CONSTRAINT `SongsData_ibfk_2` FOREIGN KEY (`ArtistId`) REFERENCES `Artists` (`ArtistId`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
