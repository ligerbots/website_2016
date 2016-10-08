--
-- Table structure for table `email-tracking`
--

CREATE TABLE IF NOT EXISTS `email-tracking` (
  `id` varchar(32) NOT NULL,
  `email-id` int(11) unsigned NOT NULL,
  `name-first` text NOT NULL,
  `name-last` text NOT NULL,
  `email` text NOT NULL,
  `open-time` int(11) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `email-id` (`email-id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `email-tracking-emails`
--

CREATE TABLE IF NOT EXISTS `email-tracking-emails` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` text NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;
